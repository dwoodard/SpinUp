<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SitesettingsController;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'admin.', 'middleware' => ['web', 'role:admin|superadmin']], function () {

    //Default route for admin
    Route::get('/', function () {
        return "Admin Dashboard";
    })->name('index');

    //users
    Route::resource('users', UserController::class);


    //settings
    Route::resource('site-settings', SiteSettingsController::class);
});
