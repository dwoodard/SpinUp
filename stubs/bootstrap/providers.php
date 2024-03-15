<?php

return [
    App\Providers\AppServiceProvider::class,
    // FEATURE_LARAVEL_TELESCOPE:START
    App\Providers\TelescopeServiceProvider::class,
    // FEATURE_LARAVEL_TELESCOPE:END
    // FEATURE_VENTURECRAFT_REVISIONABLE:START
    Venturecraft\Revisionable\RevisionableServiceProvider::class,
    // FEATURE_VENTURECRAFT_REVISIONABLE:END
];
