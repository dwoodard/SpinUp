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
        \Spatie\Permission\Models\Role::create(['name' => 'superadmin']);
        \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        \Spatie\Permission\Models\Role::create(['name' => 'user']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::table('roles')->truncate();
    }
}
