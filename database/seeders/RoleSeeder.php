<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'developers', 'label' => 'Developers'],
            ['name' => 'super_admin', 'label' => 'Super Admin'],
            ['name' => 'admin', 'label' => 'Admin'],
            ['name' => 'operator', 'label' => 'Operator'],
            ['name' => 'sub-operator', 'label' => 'Sub-Operator'],
            ['name' => 'staff', 'label' => 'Staff'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert([
                'name' => $role['name']
            ], $role);
        }
    }
}
