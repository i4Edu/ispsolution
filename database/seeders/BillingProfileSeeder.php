<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BillingProfileSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('billing_profiles')->updateOrInsert([
            'name' => 'default'
        ], [
            'name' => 'default',
            'rules' => json_encode(['tax_percent' => 0, 'currency' => 'USD']),
            'billing_cycle_days' => 30,
            'is_prepaid' => true,
            'tenant_id' => 1,
            'admin_id' => null,
            'operator_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
