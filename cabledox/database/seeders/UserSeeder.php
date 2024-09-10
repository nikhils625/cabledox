<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->first_name = 'Super';
        $user->last_name  = 'Admin';
        $user->email      = 'testingm2w@gmail.com'; 
        $user->phone      = '8965230125';
        $user->role_id    = 1;
        $user->password   = Hash::make('admin@123');
        $user->status     = 1;
        $user->created_by = 0;
        $user->save();

        $user->syncRoles(1);
    }
}
