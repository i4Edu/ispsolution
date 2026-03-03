<?php

namespace App\Services;

use App\Services\MikrotikService;
use App\Services\RouterMigrationService;
use App\Models\MikrotikRouter;

class RouterManagementService
{
    protected MikrotikService $mikrotik;
    protected RouterMigrationService $migrationService;

    public function __construct(MikrotikService $mikrotik, RouterMigrationService $migrationService)
    {
        $this->mikrotik = $mikrotik;
        $this->migrationService = $migrationService;
    }

    /**
     * Apply RADIUS-related configuration to a router by pushing
     * the provided config via the MikrotikService.
     */
    public function applyRadiusConfig(int $routerId, array $config): bool
    {
        return $this->mikrotik->pushRadiusConfig($routerId, $config);
    }

    public function configureRadiusForRouter(MikrotikRouter $router): bool
    {
        return $this->migrationService->configureRadiusAuth($router);
    }

    public function verifyRadius(MikrotikRouter $router): bool
    {
        return $this->migrationService->verifyRadiusConnectivity($router);
    }
}
