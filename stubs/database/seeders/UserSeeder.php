<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //create super admin user
        $user = new \App\Models\User();
        $user->username = 'super';
        $user->email = 'super@admin.com';
        $user->password = bcrypt('asdfasdf');
        $user->assignRole('superadmin');
        $user->settings = [];
        $user->save();

        //create admin user
        $user = new \App\Models\User();
        $user->username = 'admin';
        $user->email = 'admin@admin.com';
        $user->password = bcrypt('asdfasdf');
        $user->assignRole('admin');
        $user->settings = [];
        $user->save();
    }
}

