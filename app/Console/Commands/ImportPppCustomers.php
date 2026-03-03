<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\ImportPppCustomersRequested;

class ImportPppCustomers extends Command
{
    protected $signature = 'import:ppp-customers {--router=* : Router IDs to target}';
    protected $description = 'Trigger import of PPP customers from configured routers into the system';

    public function handle()
    {
        $options = $this->options();
        $payload = ['routers' => $options['router'] ?? []];

        ImportPppCustomersRequested::dispatch($payload);

        $this->info('ImportPppCustomersRequested event dispatched.');
        return 0;
    }
}
