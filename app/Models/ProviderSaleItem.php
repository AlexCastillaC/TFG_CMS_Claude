<?php

// app/Models/ProviderSaleItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_sale_id',
        'product_id',
        'quantity',
        'price',
    ];

    // Relaciones
    public function providerSale()
    {
        return $this->belongsTo(ProviderSale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
