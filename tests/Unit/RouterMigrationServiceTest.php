<?php

use PHPUnit\Framework\TestCase;
use App\Services\RouterMigrationService;

class RouterMigrationServiceTest extends TestCase
{
    public function testVerifyRadiusConnectivityFindsRadiusEntry()
    {
        $mikrotik = $this->createMock(\App\Services\MikrotikService::class);

        $router = new stdClass();
        $router->host = '1.2.3.4';
        $router->api_username = 'u';
        $router->api_password = 'p';
        $router->api_port = 8728;

        $mikrotik->expects($this->once())
            ->method('connect')
            ->with($router->host, $router->api_username, $router->api_password, $router->api_port)
            ->willReturn(true);

        $radiusIp = '10.0.0.5';
        putenv('RADIUS_HOST=' . $radiusIp);
        // ensure config('radius.host') reads env; but we simulate getRows directly

        $mikrotik->expects($this->once())
            ->method('getRows')
            ->with('radius')
            ->willReturn([['address' => $radiusIp]]);

        $mikrotik->expects($this->once())->method('disconnect');

        // Temporarily override config helper via closure binding
        $service = new RouterMigrationService($mikrotik);

        // stub config('radius.host') by setting in runtime config if available
        if (function_exists('config')) {
            config(['radius.host' => $radiusIp]);
        }

        $this->assertTrue($service->verifyRadiusConnectivity($router));
    }

    public function testVerifyMigrationReportsActiveSessions()
    {
        $mikrotik = $this->createMock(\App\Services\MikrotikService::class);
        $router = new stdClass();
        $router->host = '1.2.3.4';
        $router->api_username = 'u';
        $router->api_password = 'p';
        $router->api_port = 8728;

        $radiusIp = '10.0.0.5';
        if (function_exists('config')) {
            config(['radius.host' => $radiusIp]);
        }

        $mikrotik->method('connect')->willReturn(true);
        $mikrotik->method('getRows')->willReturn([['address' => $radiusIp]]);
        $mikrotik->method('getPppActive')->willReturn([['.id' => '1'], ['.id' => '2']]);
        $mikrotik->expects($this->once())->method('disconnect');

        $service = new RouterMigrationService($mikrotik);

        $result = $service->verifyMigration($router);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertSame(2, $result['active_sessions']);
    }
}
