<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('nas')->updateOrInsert([
            'name' => 'local-router'
        ], [
            'name' => 'local-router',
            'ip_address' => '127.0.0.1',
            'model' => 'mikrotik',
            'api_type' => 'ros',
            'secret' => bcrypt('secret'),
            'credentials' => null,
            'meta' => json_encode(['description' => 'Local development NAS/router']),
            'tenant_id' => 1,
            'admin_id' => null,
            'operator_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
