<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['guard_name' => 'api', 'name' => 'Admin']);
        $cashierRole = Role::create(['guard_name' => 'api', 'name' => 'Cashier']);

        // Assign roles to users (optional)
        $a = User::find(1);
        $a->assignRole($adminRole);
    }
}

