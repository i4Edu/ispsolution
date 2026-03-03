<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\RadAcct;
use Carbon\Carbon;

class DeleteStaleRadSessions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Mark sessions with no stop time older than 48 hours as stopped
        $threshold = Carbon::now()->subHours(48);
        RadAcct::whereNull('acctstoptime')
            ->where('acctstarttime', '<', $threshold)
            ->update([
                'acctstoptime' => Carbon::now(),
                'acctterminatecause' => 'stale-cleanup'
            ]);
    }
}
