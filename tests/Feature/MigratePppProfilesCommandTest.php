<?php

namespace Tests\Feature;

use App\Services\MikrotikService;
use Tests\TestCase;

class MigratePppProfilesCommandTest extends TestCase
{
    public function test_dry_run_profiles_import()
    {
        $this->mock(MikrotikService::class, function ($mock) {
            $mock->shouldReceive('connect')->andReturnTrue();
            $mock->shouldReceive('disconnect')->andReturnNull();
            $mock->shouldReceive('getRows')->with('ppp/profile')->andReturn([
                ['name' => 'default', 'local-address' => '10.0.0.1']
            ]);
        });

        config(['mikrotik.test_routers' => [ (object)[
            'id' => 1,
            'name' => 'demo-router',
            'ip_address' => '127.0.0.1',
            'username' => 'u',
            'password' => 'p',
            'api_port' => 8728,
        ] ]]);

        $this->artisan('migrate:mikrotik-ppp-profiles --dry-run')
            ->assertExitCode(0);
    }

    public function test_no_profiles_returns_success()
    {
        $this->mock(MikrotikService::class, function ($mock) {
            $mock->shouldReceive('connect')->andReturnTrue();
            $mock->shouldReceive('disconnect')->andReturnNull();
            $mock->shouldReceive('getRows')->with('ppp/profile')->andReturn([]);
        });

        config(['mikrotik.test_routers' => [ (object)[
            'id' => 1,
            'name' => 'demo-router',
            'ip_address' => '127.0.0.1',
            'username' => 'u',
            'password' => 'p',
            'api_port' => 8728,
        ] ]]);

        $this->artisan('migrate:mikrotik-ppp-profiles --dry-run')
            ->assertExitCode(0);
    }
}
