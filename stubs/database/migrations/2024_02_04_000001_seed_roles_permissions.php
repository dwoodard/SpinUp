<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedRolesPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // FEATURE_LARAVEL_PERMISSION:START
        \Spatie\Permission\Models\Role::create(['name' => 'superadmin']);
        \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        \Spatie\Permission\Models\Role::create(['name' => 'user']);
        // FEATURE_LARAVEL_PERMISSION:END
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // FEATURE_LARAVEL_PERMISSION:START
        \Illuminate\Support\Facades\DB::table('roles')->truncate();
        // FEATURE_LARAVEL_PERMISSION:END
    }
}
