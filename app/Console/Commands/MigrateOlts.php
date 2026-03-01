<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateOlts extends Command
{
    protected $signature = 'migrate:olts {--dry-run}';

    protected $description = 'Import OLT records from legacy tables into the new OLT table';

    public function handle(): int
    {
        // Prefer legacy table 'olts_legacy' or 'olts' if present
        $sourceTable = Schema::hasTable('olts_legacy') ? 'olts_legacy' : (Schema::hasTable('olts') ? 'olts' : null);

        if (! $sourceTable) {
            $this->warn('No legacy OLT table found (expected olts_legacy or olts)');
            return 0;
        }

        $rows = DB::table($sourceTable)->get();

        if ($rows->isEmpty()) {
            $this->info('No OLT records to migrate.');
            return 0;
        }

        foreach ($rows as $r) {
            $name = $r->name ?? ($r->hostname ?? null);
            if (! $name) {
                continue;
            }

            $data = [
                'name' => $name,
                'ip_address' => $r->ip_address ?? $r->host ?? null,
                'brand' => $r->brand ?? null,
                'model' => $r->model ?? null,
                'snmp_community' => $r->snmp_community ?? null,
                'snmp_version' => $r->snmp_version ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($this->option('dry-run')) {
                $this->line('DRY RUN: would import OLT ' . $name);
                continue;
            }

            DB::table('olts')->updateOrInsert(['name' => $data['name']], $data);
        }

        $this->info('OLT migration complete.');

        return 0;
    }
}
