<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserSettingsController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('/dev')->group(__DIR__ . '/dev.php');

Route::prefix('/admin')->group(__DIR__ . '/admin.php');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');


//Resource Application
Route::resource('/apply', ApplicationController::class);





Route::prefix('/ui')->group(function () {
    $components  = [
        'accordion',
        'alert',
        'alert-dialog',
        'aspect-ratio',
        'avatar',
        'badge',
        'button',
        'calendar',
        'card',
        'carousel',
        'checkbox',
        'collapsible',
        'command',
        'context-menu',
        'dialog',
        'drawer',
        'dropdown-menu',
        // 'form',
        'hover-card',
        'input',
        'label',
        'menubar',
        'navigation-menu',
        'pagination',
        'pin-input',
        'popover',
        'progress',
        'radio-group',
        'range-calendar',
        'resizable',
        'scroll-area',
        'select',
        'separator',
        'sheet',
        'skeleton',
        'slider',
        'sonner',
        'switch',
        'table',
        'tabs',
        'tags-input',
        'textarea',
        'toast',
        'toggle',
        // 'toggle-group',
        'tooltip',
    ];


    // make a redirect for /ui to ui/components
    Route::redirect('/', '/ui/components');

    Route::get('/components', function () use ($components) {
        return Inertia::render('UI/Index', [
            'components' => $components
        ]);
    })->name('ui.index');

    // use the array to render the ui components
    foreach ($components as $component) {
        Route::get("/components/$component", function () use ($component, $components) {
            Inertia::share([
                       'component' => $component,
                       'components' => $components
                   ]);


            return Inertia::render('UI/components/' . $component);
        })->name("ui.$component");
    }

    // in the terminal create a function to create the routes that creates all these files

});




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //UserSettings
    Route::resource('/user-settings', UserSettingsController::class);
});

require __DIR__ . '/auth.php';
