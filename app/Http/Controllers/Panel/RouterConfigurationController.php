<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MikrotikRouter;
use App\Services\RouterMigrationService;
use Illuminate\Http\Response;

class RouterConfigurationController extends Controller
{
    protected RouterMigrationService $migrationService;

    public function __construct(RouterMigrationService $migrationService)
    {
        $this->migrationService = $migrationService;
    }

    public function index()
    {
        $routers = MikrotikRouter::all();
        return response()->json(['data' => $routers]);
    }

    public function show($routerId)
    {
        $router = MikrotikRouter::find($routerId);
        if (!$router) {
            return response()->json(['message' => 'Router not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => $router]);
    }

    public function configureRadius($routerId)
    {
        $router = MikrotikRouter::find($routerId);
        if (!$router) {
            return response()->json(['message' => 'Router not found'], Response::HTTP_NOT_FOUND);
        }

        $ok = $this->migrationService->configureRadiusAuth($router);
        if ($ok) {
            return response()->json(['message' => 'RADIUS configured successfully']);
        }

        return response()->json(['message' => 'Failed to configure RADIUS'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function configurePpp($routerId)
    {
        // For now PPP config is handled in configureRadius which sets PPP AAA settings
        return $this->configureRadius($routerId);
    }

    public function configureFirewall($routerId)
    {
        // Firewall tweaks are applied as part of configureRadius
        return $this->configureRadius($routerId);
    }

    public function radiusStatus($routerId)
    {
        $router = MikrotikRouter::find($routerId);
        if (!$router) {
            return response()->json(['message' => 'Router not found'], Response::HTTP_NOT_FOUND);
        }

        $reachable = $this->migrationService->verifyRadiusConnectivity($router);
        return response()->json(['radius_reachable' => $reachable]);
    }
}
