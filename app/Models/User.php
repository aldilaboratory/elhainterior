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
        'phone',
        'address',
        'password',
        'role',
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

    public function cart()
    {
        return $this->hasOne(Cart::class)->where('is_active', true);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';   // sesuaikan kolom role/table Anda
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function addresses() {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress() {
        return $this->hasOne(Address::class)->where('is_default', true);
    }
}
