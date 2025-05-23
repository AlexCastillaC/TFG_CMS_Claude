<?php

// app/Models/ProviderSale.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderSale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'vendor_id',
        'total',
        'status',
        'payment_method',
        'payment_status',
    ];

    // Relaciones
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function items()
    {
        return $this->hasMany(ProviderSaleItem::class);
    }
}
