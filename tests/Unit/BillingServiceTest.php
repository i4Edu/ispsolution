<?php

use PHPUnit\Framework\TestCase;
use App\Services\BillingService;

class BillingServiceTest extends TestCase
{
    public function testCalculateChargeReturnsZeroWhenNoPackage()
    {
        $billing = new BillingService();

        $user = new stdClass();
        $user->servicePackage = null;

        $this->assertSame(0.0, $billing->calculateCharge($user));
    }

    public function testCalculateChargeUsesMonthlyPriceCents()
    {
        $billing = new BillingService();

        $package = new stdClass();
        $package->monthly_price_cents = 4500; // $45.00

        $user = new stdClass();
        $user->servicePackage = $package;

        $this->assertEquals(45.00, $billing->calculateCharge($user));
    }
}
