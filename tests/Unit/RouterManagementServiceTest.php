<?php

use PHPUnit\Framework\TestCase;
use App\Services\RouterManagementService;

class RouterManagementServiceTest extends TestCase
{
    public function testApplyRadiusConfigDelegatesToMikrotik()
    {
        $mikrotik = $this->createMock(\App\Services\MikrotikService::class);
        $migration = $this->createMock(\App\Services\RouterMigrationService::class);

        $mikrotik->expects($this->once())
            ->method('pushRadiusConfig')
            ->with(42, ['a' => 'b'])
            ->willReturn(true);

        $service = new RouterManagementService($mikrotik, $migration);

        $this->assertTrue($service->applyRadiusConfig(42, ['a' => 'b']));
    }
}
