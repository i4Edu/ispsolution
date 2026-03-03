<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            TenantSeeder::class,
            AdminUserSeeder::class,
            NasSeeder::class,
            PackageSeeder::class,
            BillingProfileSeeder::class,
        ]);
    }
}
