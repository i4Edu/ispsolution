<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('packages')->updateOrInsert([
            'name' => 'Basic Plan'
        ], [
            'name' => 'Basic Plan',
            'description' => 'Entry level ISP package',
            'price' => 25.00,
            'bandwidth_upload' => 1024,
            'bandwidth_download' => 10240,
            'pppoe_profile_id' => null,
            'tenant_id' => 1,
            'admin_id' => null,
            'operator_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
