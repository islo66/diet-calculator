<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

Artisan::command('sqlite:export-csv
    {--path=database/seeders/data/sqlite-export : Export folder (relative to project root or absolute path)}
    {--connection= : SQLite connection name (defaults to current DB connection)}
    {--exclude= : Additional comma-separated list of tables to skip}
    {--include-service-tables : Include Laravel/service tables too}
', function () {
    $connectionName = (string) ($this->option('connection') ?: config('database.default'));
    $connection = DB::connection($connectionName);

    if ($connection->getDriverName() !== 'sqlite') {
        $this->error("Connection [{$connectionName}] is not SQLite.");
        return self::FAILURE;
    }

    $pathOption = (string) $this->option('path');
    $outputPath = Str::startsWith($pathOption, ['/']) ? $pathOption : base_path($pathOption);
    File::ensureDirectoryExists($outputPath);

    $serviceTables = [
        'migrations',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'sessions',
        'password_reset_tokens',
        'personal_access_tokens',
        'telescope_entries',
        'telescope_entries_tags',
        'telescope_monitoring',
    ];

    $extraExclude = array_values(array_filter(array_map('trim', explode(',', (string) $this->option('exclude')))));
    $exclude = $this->option('include-service-tables')
        ? $extraExclude
        : array_values(array_unique(array_merge($serviceTables, $extraExclude)));
    $excludedMap = array_fill_keys($exclude, true);

    $tableRows = $connection->select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
    $availableTables = array_map(static fn ($row) => (string) $row->name, $tableRows);

    $tables = [];
    foreach ($availableTables as $tableName) {
        if (!isset($excludedMap[$tableName])) {
            $tables[] = $tableName;
        }
    }

    if (!$this->option('include-service-tables')) {
        $skipped = array_values(array_intersect($exclude, $availableTables));
        if ($skipped !== []) {
            $this->line('Skipping service tables: ' . implode(', ', $skipped));
        }
    }

    if ($tables === []) {
        $this->warn('No tables found for export.');
        return self::SUCCESS;
    }

    $dependencies = [];
    foreach ($tables as $table) {
        $fkRows = $connection->select('PRAGMA foreign_key_list("' . str_replace('"', '""', $table) . '")');
        $deps = [];
        foreach ($fkRows as $fkRow) {
            $depTable = (string) $fkRow->table;
            if (in_array($depTable, $tables, true)) {
                $deps[$depTable] = true;
            }
        }
        $dependencies[$table] = array_keys($deps);
    }

    $remaining = array_fill_keys($tables, true);
    $orderedTables = [];
    while ($remaining !== []) {
        $progress = false;

        foreach (array_keys($remaining) as $table) {
            $deps = $dependencies[$table] ?? [];
            $missingDeps = array_filter($deps, static fn (string $dep) => isset($remaining[$dep]));

            if ($missingDeps === []) {
                $orderedTables[] = $table;
                unset($remaining[$table]);
                $progress = true;
            }
        }

        if (!$progress) {
            foreach (array_keys($remaining) as $table) {
                $orderedTables[] = $table;
            }
            break;
        }
    }

    $manifest = [
        'generated_at' => now()->toIso8601String(),
        'source_connection' => $connectionName,
        'tables' => [],
    ];

    foreach ($orderedTables as $table) {
        $this->line("Exporting table: {$table}");
        $columnsInfo = $connection->select('PRAGMA table_info("' . str_replace('"', '""', $table) . '")');
        $columns = array_map(static fn ($column) => (string) $column->name, $columnsInfo);

        $csvPath = rtrim($outputPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $table . '.csv';
        $handle = fopen($csvPath, 'w');

        if ($handle === false) {
            $this->error("Cannot write file: {$csvPath}");
            return self::FAILURE;
        }

        fputcsv($handle, $columns);

        $rows = $connection->table($table)->select($columns)->get();
        foreach ($rows as $row) {
            $csvRow = [];
            foreach ($columns as $column) {
                $value = $row->{$column};
                $csvRow[] = $value === null ? '' : (string) $value;
            }
            fputcsv($handle, $csvRow);
        }

        fclose($handle);

        $manifest['tables'][] = [
            'name' => $table,
            'columns' => $columns,
            'rows' => count($rows),
            'depends_on' => $dependencies[$table] ?? [],
        ];
    }

    $manifestPath = rtrim($outputPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'manifest.json';
    File::put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    $this->info('Export complete.');
    $this->line("Files written to: {$outputPath}");
    $this->line("Manifest: {$manifestPath}");

    return self::SUCCESS;
})->purpose('Export all SQLite tables to CSV files for cross-DB migration');
