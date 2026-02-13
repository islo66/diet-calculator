<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ImportCsvDumpSeeder extends Seeder
{
    private array $idRemapByTable = [];
    private array $foreignKeyCache = [];

    public function run(): void
    {
        $importPath = env('CSV_IMPORT_PATH', base_path('database/seeders/data/sqlite-export'));

        if (!File::isDirectory($importPath)) {
            throw new \RuntimeException("CSV import path not found: {$importPath}");
        }

        $tableDefinitions = $this->resolveTableDefinitionsFromPath($importPath);
        if ($tableDefinitions === []) {
            $this->command?->warn('No CSV files found for import.');
            return;
        }

        $filteredDefinitions = [];
        foreach ($tableDefinitions as $tableDefinition) {
            $table = $tableDefinition['name'];
            if (!Schema::hasTable($table)) {
                $this->command?->warn("Skipping missing table on target DB: {$table}");
                continue;
            }
            $filteredDefinitions[] = $tableDefinition;
        }

        if ($filteredDefinitions === []) {
            $this->command?->warn('No matching target tables found.');
            return;
        }

        $orderedDefinitions = $this->orderTableDefinitions($filteredDefinitions);
        $importedTables = [];

        foreach ($orderedDefinitions as $definition) {
            $table = $definition['name'];
            $csvPath = $importPath . DIRECTORY_SEPARATOR . $table . '.csv';

            if (!File::exists($csvPath)) {
                $this->command?->warn("Skipping table without CSV file: {$table}");
                continue;
            }

            $missingDeps = array_values(array_filter(
                $definition['depends_on'],
                static fn (string $dependency) => !in_array($dependency, $importedTables, true)
            ));

            if ($missingDeps !== []) {
                $this->command?->warn("Skipping {$table}: unresolved dependencies (" . implode(', ', $missingDeps) . ')');
                continue;
            }

            $processedRows = $this->importTableFromCsv($table, $csvPath);
            $this->command?->line("Processed {$processedRows} rows for {$table}");
            $importedTables[] = $table;
        }

        $this->resetSequencesIfPostgres($importedTables);
    }

    private function resolveTableDefinitionsFromPath(string $importPath): array
    {
        $manifestPath = $importPath . DIRECTORY_SEPARATOR . 'manifest.json';
        if (File::exists($manifestPath)) {
            $decoded = json_decode(File::get($manifestPath), true, 512, \JSON_THROW_ON_ERROR);
            $definitions = [];

            foreach (($decoded['tables'] ?? []) as $tableDef) {
                $name = (string) ($tableDef['name'] ?? '');
                if ($name === '') {
                    continue;
                }

                $dependsOn = array_values(array_filter(
                    array_map('strval', (array) ($tableDef['depends_on'] ?? [])),
                    static fn (string $dependency) => $dependency !== ''
                ));

                $definitions[] = [
                    'name' => $name,
                    'depends_on' => $dependsOn,
                ];
            }

            return $definitions;
        }

        $files = File::files($importPath);
        $tables = [];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'csv') {
                continue;
            }
            $tables[] = $file->getBasename('.csv');
        }

        sort($tables);

        return array_map(
            static fn (string $table) => ['name' => $table, 'depends_on' => []],
            $tables
        );
    }

    private function orderTableDefinitions(array $definitions): array
    {
        $knownTables = [];
        foreach ($definitions as $definition) {
            $knownTables[$definition['name']] = true;
        }

        $normalized = [];
        foreach ($definitions as $definition) {
            $normalized[] = [
                'name' => $definition['name'],
                'depends_on' => array_values(array_filter(
                    $definition['depends_on'],
                    static fn (string $dependency) => isset($knownTables[$dependency])
                )),
            ];
        }

        $remaining = [];
        foreach ($normalized as $definition) {
            $remaining[$definition['name']] = $definition;
        }

        $ordered = [];
        while ($remaining !== []) {
            $progress = false;

            foreach (array_keys($remaining) as $table) {
                $dependsOn = $remaining[$table]['depends_on'];
                $stillMissing = array_filter(
                    $dependsOn,
                    static fn (string $dependency) => isset($remaining[$dependency])
                );

                if ($stillMissing === []) {
                    $ordered[] = $remaining[$table];
                    unset($remaining[$table]);
                    $progress = true;
                }
            }

            if (!$progress) {
                foreach ($remaining as $definition) {
                    $ordered[] = $definition;
                }
                break;
            }
        }

        return $ordered;
    }

    private function importTableFromCsv(string $table, string $csvPath): int
    {
        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            throw new \RuntimeException("Cannot open CSV file: {$csvPath}");
        }

        $headers = fgetcsv($handle);
        if ($headers === false || $headers === []) {
            fclose($handle);
            return 0;
        }

        $availableColumns = Schema::getColumnListing($table);
        $columnsMap = array_fill_keys($availableColumns, true);
        $importColumns = array_values(array_filter(
            $headers,
            static fn (string $column) => isset($columnsMap[$column])
        ));

        if ($importColumns === []) {
            fclose($handle);
            return 0;
        }

        $uniqueBy = $this->resolveUniqueColumns($table, $importColumns);
        $updateColumns = array_values(array_filter(
            $importColumns,
            static fn (string $column) => $column !== 'id' && !in_array($column, $uniqueBy, true)
        ));
        $foreignKeys = $this->getForeignKeys($table);

        $processed = 0;
        $batch = [];

        while (($row = fgetcsv($handle)) !== false) {
            $record = [];
            foreach ($headers as $index => $header) {
                if (!in_array($header, $importColumns, true)) {
                    continue;
                }
                $value = $row[$index] ?? null;
                $value = ($value === '') ? null : $value;

                if ($value !== null && isset($foreignKeys[$header])) {
                    $parentTable = $foreignKeys[$header];
                    $mapped = $this->idRemapByTable[$parentTable][(string) $value] ?? null;
                    if ($mapped !== null) {
                        $value = $mapped;
                    }
                }

                $record[$header] = $value;
            }

            $batch[] = $record;

            if (count($batch) >= 500) {
                $processed += $this->applyBatch($table, $batch, $uniqueBy, $updateColumns);
                $batch = [];
            }
        }

        if ($batch !== []) {
            $processed += $this->applyBatch($table, $batch, $uniqueBy, $updateColumns);
        }

        fclose($handle);

        return $processed;
    }

    private function applyBatch(string $table, array $batch, array $uniqueBy, array $updateColumns): int
    {
        if ($batch === []) {
            return 0;
        }

        $payload = $batch;

        // If upsert key is not id, inserting explicit ids can violate existing PKs.
        if (
            $uniqueBy !== []
            && !in_array('id', $uniqueBy, true)
            && Schema::hasColumn($table, 'id')
        ) {
            $payload = array_map(static function (array $row): array {
                unset($row['id']);
                return $row;
            }, $batch);
        }

        if ($uniqueBy !== [] && $updateColumns !== []) {
            $effectiveUpdateColumns = array_values(array_filter(
                $updateColumns,
                static fn (string $column) => !($column === 'id' && !in_array('id', $uniqueBy, true))
            ));

            DB::table($table)->upsert($payload, $uniqueBy, $effectiveUpdateColumns);
            $this->captureIdRemap($table, $batch, $uniqueBy);
            return count($batch);
        }

        DB::table($table)->insertOrIgnore($payload);
        $this->captureIdRemap($table, $batch, $uniqueBy);
        return count($batch);
    }

    private function resolveUniqueColumns(string $table, array $importColumns): array
    {
        $uniqueConstraints = $this->getUniqueConstraints($table);
        $eligible = array_values(array_filter($uniqueConstraints, static fn (array $cols) => $cols !== []));

        $subsetEligible = array_values(array_filter(
            $eligible,
            static fn (array $cols) => count(array_diff($cols, $importColumns)) === 0
        ));

        usort($subsetEligible, static fn (array $a, array $b) => count($a) <=> count($b));

        foreach ($subsetEligible as $cols) {
            if (!in_array('id', $cols, true)) {
                return $cols;
            }
        }

        if (in_array('id', $importColumns, true) && Schema::hasColumn($table, 'id')) {
            return ['id'];
        }

        $primaryColumns = $this->getPrimaryKeyColumns($table);
        $primaryColumns = array_values(array_filter(
            $primaryColumns,
            static fn (string $column) => in_array($column, $importColumns, true)
        ));

        return $primaryColumns;
    }

    private function captureIdRemap(string $table, array $batch, array $uniqueBy): void
    {
        if (!Schema::hasColumn($table, 'id') || !in_array('id', array_keys($batch[0] ?? []), true)) {
            return;
        }

        if ($uniqueBy === []) {
            return;
        }

        $idMap = $this->idRemapByTable[$table] ?? [];

        foreach ($batch as $row) {
            if (!array_key_exists('id', $row) || $row['id'] === null) {
                continue;
            }

            $sourceId = (string) $row['id'];

            if (in_array('id', $uniqueBy, true)) {
                $idMap[$sourceId] = $row['id'];
                continue;
            }

            $lookup = [];
            foreach ($uniqueBy as $column) {
                if (!array_key_exists($column, $row)) {
                    $lookup = [];
                    break;
                }
                $lookup[$column] = $row[$column];
            }

            if ($lookup === []) {
                continue;
            }

            $actualId = DB::table($table)->where($lookup)->value('id');
            if ($actualId !== null) {
                $idMap[$sourceId] = $actualId;
            }
        }

        $this->idRemapByTable[$table] = $idMap;
    }

    private function getForeignKeys(string $table): array
    {
        if (isset($this->foreignKeyCache[$table])) {
            return $this->foreignKeyCache[$table];
        }

        $driver = DB::getDriverName();
        $result = [];

        if ($driver === 'pgsql') {
            $rows = DB::select(
                "SELECT
                    kcu.column_name,
                    ccu.table_name AS foreign_table_name,
                    ccu.column_name AS foreign_column_name
                 FROM information_schema.table_constraints tc
                 JOIN information_schema.key_column_usage kcu
                   ON tc.constraint_name = kcu.constraint_name
                  AND tc.table_schema = kcu.table_schema
                 JOIN information_schema.constraint_column_usage ccu
                   ON ccu.constraint_name = tc.constraint_name
                  AND ccu.table_schema = tc.table_schema
                 WHERE tc.constraint_type = 'FOREIGN KEY'
                   AND tc.table_schema = current_schema()
                   AND tc.table_name = ?",
                [$table]
            );

            foreach ($rows as $row) {
                if ((string) $row->foreign_column_name === 'id') {
                    $result[(string) $row->column_name] = (string) $row->foreign_table_name;
                }
            }
        } elseif ($driver === 'sqlite') {
            $rows = DB::select('PRAGMA foreign_key_list("' . str_replace('"', '""', $table) . '")');
            foreach ($rows as $row) {
                if ((string) $row->to === 'id') {
                    $result[(string) $row->from] = (string) $row->table;
                }
            }
        }

        $this->foreignKeyCache[$table] = $result;

        return $result;
    }

    private function getUniqueConstraints(string $table): array
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            $rows = DB::select(
                "SELECT
                    tc.constraint_name,
                    tc.constraint_type,
                    kcu.column_name,
                    kcu.ordinal_position
                 FROM information_schema.table_constraints tc
                 JOIN information_schema.key_column_usage kcu
                   ON tc.constraint_name = kcu.constraint_name
                  AND tc.table_schema = kcu.table_schema
                 WHERE tc.table_schema = current_schema()
                   AND tc.table_name = ?
                   AND tc.constraint_type IN ('PRIMARY KEY', 'UNIQUE')
                 ORDER BY tc.constraint_name, kcu.ordinal_position",
                [$table]
            );

            $grouped = [];
            foreach ($rows as $row) {
                $constraint = (string) $row->constraint_name;
                $grouped[$constraint]['type'] = (string) $row->constraint_type;
                $grouped[$constraint]['cols'][] = (string) $row->column_name;
            }

            $uniques = [];
            foreach ($grouped as $constraintData) {
                $uniques[] = $constraintData['cols'];
            }

            return $uniques;
        }

        if ($driver === 'sqlite') {
            $indexRows = DB::select('PRAGMA index_list("' . str_replace('"', '""', $table) . '")');
            $result = [];

            foreach ($indexRows as $indexRow) {
                if ((int) $indexRow->unique !== 1) {
                    continue;
                }

                $indexName = (string) $indexRow->name;
                $columnsRows = DB::select('PRAGMA index_info("' . str_replace('"', '""', $indexName) . '")');
                $columns = array_map(static fn ($row) => (string) $row->name, $columnsRows);

                if ($columns !== []) {
                    $result[] = $columns;
                }
            }

            return $result;
        }

        return [];
    }

    private function getPrimaryKeyColumns(string $table): array
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            $rows = DB::select(
                "SELECT kcu.column_name
                 FROM information_schema.table_constraints tc
                 JOIN information_schema.key_column_usage kcu
                   ON tc.constraint_name = kcu.constraint_name
                  AND tc.table_schema = kcu.table_schema
                 WHERE tc.constraint_type = 'PRIMARY KEY'
                   AND tc.table_schema = current_schema()
                   AND tc.table_name = ?
                 ORDER BY kcu.ordinal_position",
                [$table]
            );

            return array_map(static fn ($row) => (string) $row->column_name, $rows);
        }

        if ($driver === 'sqlite') {
            $rows = DB::select('PRAGMA table_info("' . str_replace('"', '""', $table) . '")');
            $primaryRows = array_values(array_filter(
                $rows,
                static fn ($row) => (int) $row->pk > 0
            ));
            usort($primaryRows, static fn ($a, $b) => ((int) $a->pk) <=> ((int) $b->pk));

            return array_map(static fn ($row) => (string) $row->name, $primaryRows);
        }

        if ($driver === 'mysql') {
            $rows = DB::select(
                "SELECT column_name
                 FROM information_schema.key_column_usage
                 WHERE table_schema = database()
                   AND table_name = ?
                   AND constraint_name = 'PRIMARY'
                 ORDER BY ordinal_position",
                [$table]
            );

            return array_map(static fn ($row) => (string) $row->column_name, $rows);
        }

        return [];
    }

    private function resetSequencesIfPostgres(array $tables): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'id')) {
                continue;
            }

            $sequenceRow = DB::selectOne(
                'SELECT pg_get_serial_sequence(?, ?) AS seq',
                [$table, 'id']
            );

            $sequence = $sequenceRow?->seq ?? null;
            if ($sequence === null) {
                continue;
            }

            $tableQuoted = $this->quoteIdentifier($table);
            $sequenceEscaped = str_replace("'", "''", (string) $sequence);

            DB::statement(
                "SELECT setval('{$sequenceEscaped}', COALESCE((SELECT MAX(id) FROM {$tableQuoted}), 0) + 1, false)"
            );
        }
    }

    private function quoteIdentifier(string $name): string
    {
        return '"' . str_replace('"', '""', $name) . '"';
    }
}
