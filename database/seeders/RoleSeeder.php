<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default roles
        Role::updateOrCreate(
            ['id' => 1],
            [
                'name' => Role::ADMIN,
            ]
        );

        Role::updateOrCreate(
            ['id' => 2],
            [
                'name' => Role::CUSTOMER,
            ]
        );

    }
}
