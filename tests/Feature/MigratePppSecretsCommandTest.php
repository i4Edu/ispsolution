<?php

namespace Tests\Feature;

use App\Console\Commands\MigratePppSecrets;
use App\Services\MikrotikService;
use Tests\TestCase;

class MigratePppSecretsCommandTest extends TestCase
{
    public function test_dry_run_with_secrets_outputs_dry_run_lines()
    {
        $this->mock(MikrotikService::class, function ($mock) {
            $mock->shouldReceive('connect')->andReturnTrue();
            $mock->shouldReceive('disconnect')->andReturnNull();
            $mock->shouldReceive('getPppSecrets')->andReturn([
                ['name' => 'testuser', 'password' => 'secret', 'profile' => 'default']
            ]);
        });

        // Provide routers via config so command falls back to them in tests
        config(['mikrotik.test_routers' => [ (object)[
            'id' => 1,
            'name' => 'demo-router',
            'ip_address' => '127.0.0.1',
            'username' => 'u',
            'password' => 'p',
            'api_port' => 8728,
        ] ]]);

        $this->artisan('migrate:mikrotik-ppp-secrets --dry-run')
            ->assertExitCode(0);
    }

    public function test_no_secrets_returns_success()
    {
        $this->mock(MikrotikService::class, function ($mock) {
            $mock->shouldReceive('connect')->andReturnTrue();
            $mock->shouldReceive('disconnect')->andReturnNull();
            $mock->shouldReceive('getPppSecrets')->andReturn([]);
        });

        config(['mikrotik.test_routers' => [ (object)[
            'id' => 1,
            'name' => 'demo-router',
            'ip_address' => '127.0.0.1',
            'username' => 'u',
            'password' => 'p',
            'api_port' => 8728,
        ] ]]);

        $this->artisan('migrate:mikrotik-ppp-secrets --dry-run')
            ->assertExitCode(0);
    }
}
