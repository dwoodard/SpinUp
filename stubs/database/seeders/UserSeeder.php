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

        //create a user for each role
        $roles = [
            'superadmin',
            'admin',
            'user',
        ];

        //for the $roles array, create a user for each role
        foreach ($roles as $role) {
            $user = new \App\Models\User();
            $user->username = $role;
            $user->email = $role . '@' . $role . '.com';
            $user->password = bcrypt('asdfasdf');
            // FEATURE_LARAVEL_PERMISSION:START
            $user->assignRole($role);
            // FEATURE_LARAVEL_PERMISSION:END

            // FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:START
            $user->settings = [];
            // FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:END
            $user->save();
        }

        // now create 100 random users
        \App\Models\User::factory(100)->create();
    }
}
