<?php

// app/Models/Stand.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Stand extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'location',
        'category',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
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

    // If no user, default to vendedores' stands
    if (!$user) {
        return $query->whereHas('user', fn($userQ) => $userQ->where('role', 'vendedor'));
    }

    // Apply visibility based on user role
    return $visibilityRules[$user->role] 
        ? $visibilityRules[$user->role]($query)
        : $query->whereRaw('1 = 0'); // Default to no stands for unknown roles
}
}
