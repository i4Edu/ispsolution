<?php

namespace App\Services;

use App\Models\Olt;

class OltSyncService
{
    /**
     * Perform a manual sync of OLT/ONU devices. Returns number of records synced.
     */
    public function sync(Olt $olt): int
    {
        // Placeholder: real implementation would call devices and reconcile
        // For now return 0 to indicate no automated changes.
        return 0;
    }
}
