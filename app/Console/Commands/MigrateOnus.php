<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateOnus extends Command
{
    protected $signature = 'migrate:onus {--dry-run}';

    protected $description = 'Import ONU records from legacy tables into the new ONUs table';

    public function handle(): int
    {
        $sourceTable = Schema::hasTable('onus_legacy') ? 'onus_legacy' : (Schema::hasTable('onus') ? 'onus' : null);

        if (! $sourceTable) {
            $this->warn('No legacy ONU table found (expected onus_legacy or onus)');
            return 0;
        }

        $rows = DB::table($sourceTable)->get();

        if ($rows->isEmpty()) {
            $this->info('No ONU records to migrate.');
            return 0;
        }

        foreach ($rows as $r) {
            $serial = $r->serial ?? $r->mac ?? null;
            if (! $serial) {
                continue;
            }

            $data = [
                'serial_number' => $serial,
                'olt_id' => $r->olt_id ?? null,
                'pon_port' => $r->pon_port ?? null,
                'onu_type' => $r->model ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($this->option('dry-run')) {
                $this->line('DRY RUN: would import ONU ' . $serial);
                continue;
            }

            DB::table('onus')->updateOrInsert(['serial_number' => $serial], $data);
        }

        $this->info('ONU migration complete.');

        return 0;
    }
}
