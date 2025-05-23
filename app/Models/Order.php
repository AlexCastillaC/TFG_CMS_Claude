<?php

// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'client_id',
        'vendor_id',
        'total',
        'status',
        'payment_method',
        'payment_status',
        'name',
        'email',
        'phone',
        'document',
        'address',
        'city',
        'state',
        'postal_code',
        'notes'
    ];

    // Relaciones
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}