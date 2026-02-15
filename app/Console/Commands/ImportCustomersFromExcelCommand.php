<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportCustomersFromExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:customers_from_excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk import customers from Excel file';

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
