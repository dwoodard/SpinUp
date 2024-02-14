<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SitesettingsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group(['as' => 'admin.', 'middleware' => ['web', 'role:admin|superadmin']], function () {

    //Default route for admin
    Route::get('/', function () {
        return redirect()->route('dashboard');
    })->name('index');

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    //users
    Route::resource('users', UserController::class);


    //settings
    Route::resource('site-settings', SiteSettingsController::class);
});
