<?php

namespace App\Console\Commands;

use App\Services\MikrotikService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigratePppProfiles extends Command
{
    protected $signature = 'migrate:mikrotik-ppp-profiles {--dry-run}';

    protected $description = 'Fetch PPP profiles from MikroTik routers and import into mikrotik_profiles table.';

    public function __construct(protected MikrotikService $mikrotik)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $routers = $this->getRouters();

        if (empty($routers)) {
            $this->info('No MikroTik routers configured.');
            return 0;
        }

        foreach ($routers as $router) {
            $this->info("Connecting to router {$router->name} ({$router->ip_address})");
            try {
                $connected = $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->api_port);
            } catch (\Throwable $e) {
                $this->warn("Failed to connect: {$e->getMessage()}");
                continue;
            }

            if (! $connected) {
                $this->warn("Unable to connect to router {$router->ip_address}");
                continue;
            }

            try {
                $profiles = $this->mikrotik->getRows('ppp/profile');
            } catch (\Throwable $e) {
                $this->warn('Failed to fetch PPP profiles: ' . $e->getMessage());
                $this->mikrotik->disconnect();
                continue;
            }

            if (empty($profiles)) {
                $this->info('No PPP profiles found.');
                $this->mikrotik->disconnect();
                continue;
            }

            foreach ($profiles as $p) {
                $name = $p['name'] ?? null;
                if (! $name) {
                    continue;
                }

                $row = [
                    'router_id' => $router->id,
                    'name' => $name,
                    'local_address' => $p['local-address'] ?? $p['local_address'] ?? null,
                    'remote_address' => $p['remote-address'] ?? $p['remote_address'] ?? null,
                    'rate_limit' => $p['rate-limit'] ?? null,
                    'session_timeout' => isset($p['session-timeout']) ? (int)$p['session-timeout'] : null,
                    'idle_timeout' => isset($p['idle-timeout']) ? (int)$p['idle-timeout'] : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($this->option('dry-run')) {
                    $this->line('DRY RUN: would import profile ' . $name);
                    continue;
                }

                DB::table('mikrotik_profiles')->updateOrInsert(
                    ['router_id' => $router->id, 'name' => $name],
                    $row
                );
            }

            $this->info('Imported profiles for ' . $router->name);
            $this->mikrotik->disconnect();
        }

        $this->info('PPP profile migration complete.');

        return 0;
    }

    protected function getRouters(): array
    {
        if (class_exists(\App\Models\MikrotikRouter::class) && Schema::hasTable('mikrotik_routers')) {
            return \App\Models\MikrotikRouter::all()->toArray();
        }

        return config('mikrotik.test_routers', []);
    }
}
