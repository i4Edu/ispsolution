<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\RadiusService;

class PullRadacct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(RadiusService $radius)
    {
        // Placeholder: call radius service to pull recent radacct rows
        if (method_exists($radius, 'pullRecentAccounting')) {
            $radius->pullRecentAccounting();
        }
    }
}
