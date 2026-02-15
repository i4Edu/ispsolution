<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MasterGroupAdminImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'master:group_admin_import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import complete operator structure with customers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Command logic goes here
    }
}
