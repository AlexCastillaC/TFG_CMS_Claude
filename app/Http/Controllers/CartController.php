<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\View\View;

class CartController extends Controller
{

    public function index(): View
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

public function add(Request $request): RedirectResponse
{
    $productId = $request->input(key: 'product_id');
    $quantity = $request->input(key: 'quantity', default: 1);

    $product = Product::findOrFail(id: $productId);

    // Verificar stock disponible
    if($product->stock < $quantity) {
        return back()->with(key: 'error', value: 'No hay suficiente stock disponible.');
    }

    $cart = session()->get(key: 'cart', default: []);

    if(isset($cart[$productId])) {
        $cart[$productId]['quantity'] += $quantity;
    } else {
        $cart[$productId] = [
            'quantity' => $quantity
        ];
    }

    session()->put(key: 'cart', value: $cart);

    return back()->with(key: 'success', value: 'Producto añadido al carrito.');
}

/**
     * Actualizar cantidad de un producto en el carrito
     */
    public function update(Request $request): RedirectResponse
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $product = Product::findOrFail($productId);

        // Verificar stock disponible
        if($product->stock < $quantity) {
            return back()->with('error', 'No hay suficiente stock disponible.');
        }

        $cart = session()->get('cart', []);

        if(isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Carrito actualizado.');
    }

public function remove($productId)
{
    $cart = session()->get('cart', []);
    
    if(isset($cart[$productId])) {
        unset($cart[$productId]);
        session()->put('cart', $cart);
    }
    
    return back()->with('success', 'Producto eliminado del carrito.');
}

/**
     * Vaciar el carrito
     */
    public function clear(): RedirectResponse
    {
        session()->forget('cart');
        return back()->with('success', 'Carrito vaciado.');
    }
}