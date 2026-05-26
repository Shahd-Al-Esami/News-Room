<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRoles;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'role',
        'email',
        'password',
        'is_active'
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
            'is_active' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role'=> UserRoles::class,
        ];
    }

/**
 * Summary of booted
 * @return void
 */
protected static function booted()
{
    static::deleted(function ($user) {
        if ($user->role === UserRoles::Writer) {
            static::clearUserDashboardCache();
        }
    });

    static::updated(function ($user) {
        if ($user->wasChanged('role')) {
            static::clearUserDashboardCache();
        }
    });
}

/**
 * Summary of clearUserDashboardCache
 * @return void
 */
protected static function clearUserDashboardCache(): void
{
    //cache invalidation for user and articles dashboard
    Cache::tags(['Users'])->flush();

    Cache::tags(['Articles'])->flush();
}



    protected $appends = ['full_name'];
//accessor
    public function fullName(): Attribute
    {
        return Attribute::make(
            get: function() {
                return $this->first_name . ' ' . $this->last_name;
            }
        );
    }

    //mutator

    public function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Hash::make($value)
        );
    }
    public function profile():HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function articles():HasMany
    {
        return $this->hasMany(Article::class);
    }

   public function comments():HasMany
    {
        return $this->hasMany(Comment::class);
    }

}
