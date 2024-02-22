#TODO

- Install Layout Templates
  - we all ready have a lot through breeze
    - tailwindcss
    - vue 3
    - vite
    - inertia
  - we'll have 
    - admin
      - admin will have these features
        - users/roles/permissions
        - telescope - for tracking the site usage
        - site settings
          - name
          - logo
          - favicon
          - description
          - keywords
        -   
    - public
    - auth
    
- PHP Helpers File
  - create a file in app/Helpers.php
    - this file will contain all the helper functions like:


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


- look into this article:
  - https://advanced-inertia.com/blog/typescript




