<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stand;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Define the allowed categories
        $categories = [
            'Frutas',
            'Verduras',
            'Lácteos',
            'Carnes',
            'Embutidos',
            'Artesanía',
            'Panadería',
            'Otros'
        ];



        // Start with the base query and apply visibility scope
        $query = Product::visibleToUser();

        // Search filter
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Category filter
        if ($request->filled('category') && in_array($request->category, $categories)) {
            $query->where('category', $request->category);
        }

        // Ordering
        $orderBy = $request->input('order_by', 'created_at');
        $order = $request->input('order', 'desc');

        // Validate order by column
        $allowedOrderColumns = ['created_at', 'price', 'name'];
        $orderBy = in_array($orderBy, $allowedOrderColumns) ? $orderBy : 'created_at';

        // Validate order direction
        $order = in_array($order, ['asc', 'desc']) ? $order : 'desc';

        // Apply ordering
        $query->orderBy($orderBy, $order);

        // Paginate results
        $products = $query->paginate(12);

        return view('products.index', compact('products'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        if (!$product->is_available) {
            return redirect()->route('products.index')->with('error', 'Este producto no está disponible actualmente.');
        }

        // Cargar relaciones necesarias
        $product->load(['user', 'stand']);

        // Obtener productos relacionados de la misma categoría
        $relatedProducts = Product::visibleToUser()->where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Add product to cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock,
        ]);

        if (!$product->is_available || $product->stock <= 0) {
            return back()->with('error', 'Este producto no está disponible actualmente.');
        }

        $cart = session()->get('cart', []);
        $quantity = $request->quantity;

        // Si el producto ya está en el carrito, actualizar cantidad
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            // Añadir el producto al carrito
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'image' => $product->image,
                'stand_id' => $product->stand_id,
                'stand_name' => $product->stand->name ?? 'Desconocido'
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Producto añadido al carrito correctamente.');
    }

    /**
     * Search products by term.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $search = $request->input('search');

        $products = Product::where('is_available', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->paginate(12);

        return view('products.search', compact('products', 'search'));
    }

    /**
     * Get products by category.
     *
     * @param  string  $category
     * @return \Illuminate\Http\Response
     */
    public function category($category)
    {
        $products = Product::where('category', $category)
            ->where('is_available', true)
            ->paginate(12);

        return view('products.category', compact('products', 'category'));
    }

    /**
     * Get products by stand.
     *
     * @param  \App\Models\Stand  $stand
     * @return \Illuminate\Http\Response
     */
    public function byStand(Stand $stand)
    {
        $products = Product::where('stand_id', $stand->id)
            ->where('is_available', true)
            ->paginate(12);

        return view('products.by_stand', compact('products', 'stand'));
    }

    /**
     * Get featured products for the homepage.
     *
     * @return \Illuminate\Http\Response
     */
    public function featured()
    {
        $featuredProducts = Product::where('is_available', true)
            ->with('stand')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('home.featured', compact('featuredProducts'));
    }

    /**
     * API endpoint to return featured products in JSON format.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function featuredApi()
    {
        $products = Product::where('is_available', true)
            ->with('stand')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return response()->json($products);
    }

    /**
     * Check if product is in stock and available.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAvailability($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'available' => false,
                'message' => 'Producto no encontrado'
            ]);
        }

        return response()->json([
            'available' => $product->is_available && $product->stock > 0,
            'stock' => $product->stock
        ]);
    }
}