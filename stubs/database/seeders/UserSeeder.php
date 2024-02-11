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
        // FEATURE_LARAVEL_PERMISSION:START
        $user->assignRole('superadmin');
        // FEATURE_LARAVEL_PERMISSION:END

        // FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:START
        $user->settings = [];
        // FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:END
        $user->save();

        //create admin user
        $user = new \App\Models\User();
        $user->username = 'admin';
        $user->email = 'admin@admin.com';
        $user->password = bcrypt('asdfasdf');

        // FEATURE_LARAVEL_PERMISSION:START
        $user->assignRole('admin');
        // FEATURE_LARAVEL_PERMISSION:END

        // FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:START
        $user->settings = [];
        // FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:END

        $user->save();
    }
}
