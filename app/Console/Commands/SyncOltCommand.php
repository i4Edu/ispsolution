<?php

namespace App\Console\Commands;

use App\Models\Olt;
use App\Services\OltSyncService;
use Illuminate\Console\Command;

class SyncOltCommand extends Command
{
    protected $signature = 'olt:sync {olt_id}';
    protected $description = 'Sync OLT/ONU entries for the given OLT';

    protected OltSyncService $syncService;

    public function __construct(OltSyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    public function handle()
    {
        $oltId = $this->argument('olt_id');
        $olt = Olt::find($oltId);
        if (!$olt) {
            $this->error('OLT not found');
            return 1;
        }

        $count = $this->syncService->sync($olt);
        $this->info("Synced {$count} records for OLT {$olt->id}");
        return 0;
    }
}
