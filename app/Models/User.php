<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'profile_picture',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isClient()
    {
        return $this->role === 'cliente';
    }

    public function isVendor()
    {
        return $this->role === 'vendedor';
    }

    public function isProvider()
    {
        return $this->role === 'proveedor';
    }

    // Relaciones
    public function stand()
    {
        return $this->hasOne(Stand::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function clientOrders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function vendorOrders()
    {
        return $this->hasMany(Order::class, 'vendor_id');
    }

    public function providerSales()
    {
        return $this->hasMany(ProviderSale::class, 'provider_id');
    }

    public function vendorPurchases()
    {
        return $this->hasMany(ProviderSale::class, 'vendor_id');
    }

    public function forums()
    {
        return $this->hasMany(Forum::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}
