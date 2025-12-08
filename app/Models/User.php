<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'otp',
        'otp_expires_at',
        'email_verified_at',
        'google_id',
        'address',
        'phone',
        'avatar',
        'otp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
        ];
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function emails()
    {
        return $this->hasMany(EmailAccount::class);
    }

    public function github()
    {
        return $this->hasOne(GithubSetting::class);
    }

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    public function hasActiveSubscription()
    {
        // Check if there is any subscription with status 'active' and end date in the future
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>=', now())
            ->exists();
    }

    public function isAdmin()
    {
        return strtolower($this->role) === 'admin';
    }
}
