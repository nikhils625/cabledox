<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'Super Admin',
            'Admin',
            'Electrician',
            'Manager',
            'Supervisor',
        ];

        foreach ($roles as $key => $value) {
            Role::create(['name' => $value]);
        }
    }
}
