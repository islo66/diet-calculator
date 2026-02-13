<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ImportCsvDumpSeeder extends Seeder
{
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
            static fn (string $column) => !in_array($column, $uniqueBy, true)
        ));

        $processed = 0;
        $batch = [];

        while (($row = fgetcsv($handle)) !== false) {
            $record = [];
            foreach ($headers as $index => $header) {
                if (!in_array($header, $importColumns, true)) {
                    continue;
                }
                $value = $row[$index] ?? null;
                $record[$header] = ($value === '') ? null : $value;
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

        if ($uniqueBy !== [] && $updateColumns !== []) {
            DB::table($table)->upsert($batch, $uniqueBy, $updateColumns);
            return count($batch);
        }

        DB::table($table)->insertOrIgnore($batch);
        return count($batch);
    }

    private function resolveUniqueColumns(string $table, array $importColumns): array
    {
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
