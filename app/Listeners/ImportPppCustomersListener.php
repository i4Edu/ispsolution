<?php

namespace App\Listeners;

use App\Events\ImportPppCustomersRequested;
use App\Jobs\PullRadacct;

class ImportPppCustomersListener
{
    public function handle(ImportPppCustomersRequested $event)
    {
        // Dispatch a job to pull radacct or migrate PPP secrets from routers
        PullRadacct::dispatch();
    }
}
