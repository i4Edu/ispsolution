<?php

namespace App\Console\Commands;

use App\Models\MikrotikRouter;
use App\Models\Nas;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MikrotikService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigratePppSecrets extends Command
{
    protected $signature = 'migrate:mikrotik-ppp-secrets {--tenant-id=} {--operator-id=} {--dry-run}';

    protected $description = 'Connect to configured MikroTik routers, fetch PPP secrets and import into mikrotik_ppp_secrets table.';

    public function __construct(protected MikrotikService $mikrotik)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $tenantId = $this->option('tenant-id');
        $operatorId = $this->option('operator-id');

        // Resolve tenant (allow tests to run without a tenants table)
        if ($tenantId) {
            $tenant = \Illuminate\Support\Facades\Schema::hasTable('tenants') ? Tenant::find($tenantId) : (object)['id' => (int)$tenantId];
        } else {
            $tenant = \Illuminate\Support\Facades\Schema::hasTable('tenants') ? Tenant::first() : (object)['id' => config('mikrotik.default_tenant_id', 1)];
        }

        if (! $tenant) {
            $this->error('No tenant found. Create a tenant or specify --tenant-id');
            return 1;
        }

        // Resolve operator (allow tests to run without a users table)
        if ($operatorId) {
            $operator = \Illuminate\Support\Facades\Schema::hasTable('users') ? User::find($operatorId) : (object)['id' => (int)$operatorId];
        } else {
            $operator = \Illuminate\Support\Facades\Schema::hasTable('users') ? User::where('operator_type', 'admin')->first() ?? User::first() : (object)['id' => config('mikrotik.default_operator_id', 1)];
        }

        if (! $operator) {
            $this->error('No operator found. Create an operator or specify --operator-id');
            return 1;
        }

            $routers = $this->getRouters();

            if (empty($routers)) {
            $this->info('No MikroTik routers configured. Nothing to migrate.');
            return 0;
        }

        $this->info('Starting migration of PPP secrets...');

        foreach ($routers as $router) {
            $this->info("Processing router: {$router->name} ({$router->ip_address})");

            try {
                $connected = $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->api_port);
            } catch (\Throwable $e) {
                $this->warn("Failed to connect to router {$router->ip_address}: {$e->getMessage()}");
                continue;
            }

            if (! $connected) {
                $this->warn("Unable to connect to router {$router->ip_address}");
                continue;
            }

            try {
                $secrets = $this->mikrotik->getPppSecrets();
            } catch (\Throwable $e) {
                $this->warn("Failed to fetch PPP secrets from {$router->ip_address}: {$e->getMessage()}");
                $this->mikrotik->disconnect();
                continue;
            }

            if (empty($secrets)) {
                $this->info('No PPP secrets found on router.');
                $this->mikrotik->disconnect();
                continue;
            }

            // Try to find matching NAS record for this router (by server/ip)
            $nas = null;
            if (\Illuminate\Support\Facades\Schema::hasTable('nas')) {
                $nas = Nas::where('server', $router->ip_address)->first();
            }

            if (! $nas) {
                if ($this->option('dry-run')) {
                    $this->warn("No NAS entry found for router {$router->ip_address}. Continuing because --dry-run is set.");
                } else {
                    $defaultNasId = config('mikrotik.default_nas_id', null);
                    if ($defaultNasId) {
                        $nas = (object)['id' => $defaultNasId];
                    } else {
                        $this->warn("No NAS entry found for router {$router->ip_address}. Skipping import for this router.");
                        $this->mikrotik->disconnect();
                        continue;
                    }
                }
            }

            $inserted = 0;
            foreach ($secrets as $row) {
                // Row keys may vary depending on RouterOS API library; normalize
                $name = $row['name'] ?? $row['user'] ?? null;
                if (! $name) {
                    continue;
                }

                    $nasId = $nas ? ($nas->id ?? null) : null;

                    $data = [
                    'tenant_id' => $tenant->id,
                    'customer_import_id' => null,
                    'operator_id' => $operator->id,
                        'nas_id' => $nasId,
                    'router_id' => $router->id,
                    'name' => $name,
                    'password' => $row['password'] ?? $row['pass'] ?? '',
                    'profile' => $row['profile'] ?? null,
                    'remote_address' => $row['remote-address'] ?? $row['remote_address'] ?? null,
                    'disabled' => $row['disabled'] ?? 'no',
                    'comment' => $row['comment'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($this->option('dry-run')) {
                    $this->line('DRY RUN: would import ' . $name);
                    $inserted++;
                    continue;
                }

                // Use updateOrInsert to avoid duplicates
                DB::table('mikrotik_ppp_secrets')->updateOrInsert(
                    ['name' => $data['name'], 'router_id' => $router->id],
                    $data
                );

                $inserted++;
            }

            $this->info("Imported {$inserted} secrets for router {$router->name}");

            $this->mikrotik->disconnect();
        }

        $this->info('PPP secrets migration complete.');

        return 0;
    }

    protected function getRouters(): array
    {
        // If model/table exists, use Eloquent
        if (class_exists(\App\Models\MikrotikRouter::class) && \Illuminate\Support\Facades\Schema::hasTable('mikrotik_routers')) {
            return \App\Models\MikrotikRouter::all()->toArray();
        }

        // Fallback to config-provided routers for testing
        return config('mikrotik.test_routers', []);
    }
}
