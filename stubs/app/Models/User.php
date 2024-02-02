<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
# FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:START
use Illuminate\Database\Eloquent\Builder;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;
# FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:END
# FEATURE_LARAVEL_PERMISSION:START
use Spatie\Permission\Traits\HasRoles;
# FEATURE_LARAVEL_PERMISSION:END

class User extends Authenticatable
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        # FEATURE_LARAVEL_PERMISSION:START
        HasRoles
        # FEATURE_LARAVEL_PERMISSION:END
        //;
    ;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        # FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:START
        'settings' => SchemalessAttributes::class,
        # FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:END
    ];

    # FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:START
    public function scopeWithSettings(): Builder
    {
        return $this->settings->modelScope();
    }
    # FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:END
}
