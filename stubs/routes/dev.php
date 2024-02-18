<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SitesettingsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group(['as' => 'dev.', 'middleware' => ['web', 'role:admin|superadmin']], function () {


    Route::get('/', function () {
        return Inertia::render('Dev/Dashboard');
    })->name('dashboard');
});
