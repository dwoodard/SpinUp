<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

# FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:START
use Illuminate\Database\Eloquent\Builder;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;
# FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:END

# FEATURE_LARAVEL_PERMISSION:START
use Spatie\Permission\Traits\HasRoles;
# FEATURE_LARAVEL_PERMISSION:END

# FEATURE_VENTURECRAFT_REVISIONABLE:START
use Venturecraft\Revisionable\RevisionableTrait;

# FEATURE_VENTURECRAFT_REVISIONABLE:END

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    # FEATURE_LARAVEL_PERMISSION:START
    use HasRoles;
    # FEATURE_LARAVEL_PERMISSION:END
    # FEATURE_VENTURECRAFT_REVISIONABLE:START
    use RevisionableTrait;
    # FEATURE_VENTURECRAFT_REVISIONABLE:END

    # FEATURE_VENTURECRAFT_REVISIONABLE:START
    protected $revisionEnabled = true;
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    # FEATURE_VENTURECRAFT_REVISIONABLE:END


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'state',
        'zip',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            # FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:START
            'settings' => SchemalessAttributes::class,
            # FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:END
    ];
    }

    # FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:START
    public function scopeWithSettings(): Builder
    {
        return $this->settings->modelScope();
    }
    # FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES:END
}
