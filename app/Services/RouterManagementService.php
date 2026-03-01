<?php

namespace App\Services;

use App\Models\MikrotikRouter;

class RouterManagementService
{
    protected RouterMigrationService $migrationService;

    public function __construct(RouterMigrationService $migrationService)
    {
        $this->migrationService = $migrationService;
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
