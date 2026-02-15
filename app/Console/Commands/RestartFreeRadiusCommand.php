<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RestartFreeRadiusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'radius:restart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restart FreeRADIUS service to prevent memory leaks.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Attempting to restart FreeRADIUS service...');

        // Use systemctl for restarting FreeRADIUS.
        // This command might require sudo privileges.
        // Ensure the web server user has NOPASSWD access for 'systemctl restart freeradius'
        $process = Process::fromShellCommandline('sudo systemctl restart freeradius');

        try {
            $process->mustRun();

            $this->info('FreeRADIUS service restarted successfully.');
        } catch (ProcessFailedException $exception) {
            $this->error('Failed to restart FreeRADIUS service.');
            $this->error($exception->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
