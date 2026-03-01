<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            ['name' => 'Cash', 'slug' => 'cash', 'is_active' => true],
            ['name' => 'Bkash', 'slug' => 'bkash', 'is_active' => true],
            ['name' => 'Card', 'slug' => 'card', 'is_active' => true],
        ];

        foreach ($gateways as $g) {
            PaymentGateway::updateOrCreate(['slug' => $g['slug']], $g);
        }

        $this->command->info(count($gateways) . ' payment gateways seeded.');
    }
}
