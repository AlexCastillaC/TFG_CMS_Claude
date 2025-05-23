<?php

// app/Models/Product.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'stand_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'category',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stand()
    {
        return $this->belongsTo(Stand::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function providerSaleItems()
    {
        return $this->hasMany(ProviderSaleItem::class);
    }

    /**
     * Scope a query to only include visible products based on user role
     */

    public function scopeVisibleToUser($query)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Define visibility rules as a closure to avoid repetition
        $visibilityRules = [
            'cliente' => function ($q) {
                return $q->whereHas('user', fn($userQ) => $userQ->where('role', 'vendedor'));
            },
            'vendedor' => function ($q) use ($user) {
                return $q->whereHas(
                    'user',
                    fn($userQ) =>
                    $userQ->where('role', 'vendedor')
                        ->orWhere('role', 'proveedor')
                        ->orWhere('id', $user->id)
                );
            },
            'proveedor' => function ($q) use ($user) {
                return $q->whereHas('user', fn($userQ) => $userQ->where('role', 'proveedor')->orWhere('role', 'vendedor'));
            }
        ];

        // If no user, default to vendedores' products
        if (!$user) {
            return $query->whereHas(
                'stand',
                $visibilityRules['cliente']
            );
        }

        // Apply visibility based on user role
        return $query->whereHas(
            'stand',
            $visibilityRules[$user->role] ??
            // Default to no products for unknown roles
            fn($q) => $q->whereRaw('1 = 0')
        );
    }
}
