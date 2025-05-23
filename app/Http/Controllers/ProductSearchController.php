<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductSearchController extends Controller
{
    /**
     * Search products by various criteria.
     */
    public function search(Request $request)
    {
        $query = Product::where('is_available', true);
        
        // Filtrar por término de búsqueda
        if ($request->has('term') && !empty($request->term)) {
            $term = $request->term;
            $query->where(function($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('description', 'LIKE', "%{$term}%");
            });
        }
        
        // Filtrar por categoría
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }
        
        // Filtrar por rango de precio
        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Si es un cliente, muestra solo productos de vendedores
        if (Auth::check() && Auth::user()->isClient()) {
            $query->whereHas('user', function($q) {
                $q->where('role', 'vendedor');
            });
        }
        
        // Si es un vendedor, muestra solo productos de proveedores
        if (Auth::check() && Auth::user()->isVendor()) {
            $query->whereHas('user', function($q) {
                $q->where('role', 'proveedor');
            });
        }
        
        // Ordenar resultados
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->with(['user', 'stand'])->paginate(12);
        
        // Obtener lista de categorías para el filtro
        $categories = Product::where('is_available', true)
                             ->distinct()
                             ->pluck('category')
                             ->filter()
                             ->toArray();
                             
        return view('products.search', compact('products', 'categories'));
    }
    
    /**
     * Show products by category.
     */
    public function category($category)
    {
        $query = Product::where('is_available', true)
                        ->where('category', $category);
        
        // Si es un cliente, muestra solo productos de vendedores
        if (Auth::check() && Auth::user()->isClient()) {
            $query->whereHas('user', function($q) {
                $q->where('role', 'vendedor');
            });
        }
        
        // Si es un vendedor, muestra solo productos de proveedores
        if (Auth::check() && Auth::user()->isVendor()) {
            $query->whereHas('user', function($q) {
                $q->where('role', 'proveedor');
            });
        }
        
        $products = $query->with(['user', 'stand'])
                         ->orderBy('created_at', 'desc')
                         ->paginate(12);
        
        return view('products.category', compact('products', 'category'));
    }
}