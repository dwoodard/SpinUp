#TODO

- fix Installing Stubs... show stub instead
- work on the layout both for admin and public

- DELETE `resources/views/layouts/welcome.blade.php`
- CP Stub `resources/js/Pages/Welcome.vue`

- add classes to app.blade.php     
  - <html class="h-full bg-white"> 
  - <body class="h-full">

- copy layout stubs to resources/js/Layouts


- move from 'routes/web.php' to admin
        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->middleware(['auth', 'verified'])->name('dashboard');

- Dashboard to Admin/Dashboard
  - append routes/admin.php to web.php

- Seeder for:
 - Users




