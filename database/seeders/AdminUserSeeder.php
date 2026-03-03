<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@example.com';

        DB::table('users')->updateOrInsert([
            'email' => $email
        ], [
            'name' => 'Administrator',
            'email' => $email,
            'phone' => null,
            'password' => Hash::make('password'),
            'is_subscriber' => false,
            'operator_level' => 10,
            'tenant_id' => 1,
            'admin_id' => null,
            'operator_id' => null,
            'company_in_native_lang' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
