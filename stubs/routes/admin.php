<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'admin.', 'middleware' => ['web', 'role:admin|superadmin']], function () {

    //Default route for admin
    Route::get('/', function () {
        return Redirect::route('admin.pages');
    })->name('index');

    //users
    Route::resource('users', 'Admin\UserController');


    //settings
    Route::resource('site-settings', 'Admin\SiteSettingController');
});
