<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VendorProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('user_id', Auth::id())
            ->with('stand')
            ->paginate(10);

        return view('vendor.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stand = Stand::where('user_id', Auth::id())->first();

        if (!$stand) {
            return redirect()->route('vendor.profile')
                ->with('error', 'Debes crear un puesto antes de añadir productos.');
        }

        $categories = ['Frutas', 'Verduras', 'Lácteos', 'Carnes', 'Embutidos', 'Artesanía', 'Panadería', 'Otros'];

        return view('vendor.products.create', compact('stand', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        $stand = Stand::where('user_id', Auth::id())->first();

        if (!$stand) {
            return back()->with('error', 'No tienes un puesto registrado.');
        }

        $product = new Product();
        $product->user_id = Auth::id();
        $product->stand_id = $stand->id;
        $product->name = $validated['name'];
        $product->description = $validated['description'] ?? null;
        $product->price = $validated['price'];
        $product->stock = $validated['stock'];
        $product->category = $validated['category'];
        $product->is_available = $request->has('is_available');

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }else{
            $product->image = 'products/product_placeholder.jpg';
        }

        $product->save();

        return redirect()->route('vendedor.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
{
    // Explicitly load the product with its related stand if needed
    $product->load('stand'); // Add this if you want to ensure related data is loaded

    return view('vendor.products.show', compact('product'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $producto)
    {
        if ($producto->user_id != Auth::id()) {
            return redirect()->route('vendedor.productos.index')
                ->with('error', 'No tienes permisos para editar este producto.');
        }

        $categories = ['Frutas', 'Verduras', 'Lácteos', 'Carnes', 'Embutidos', 'Artesanía', 'Panadería', 'Otros'];

        return view('vendor.products.edit', compact('producto', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $producto)
    {
        if ($producto->user_id != Auth::id()) {
            return redirect()->route('vendedor.productos.index')
                ->with('error', 'No tienes permisos para actualizar este producto.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        $producto->name = $validated['name'];
        $producto->description = $validated['description'] ?? null;
        $producto->price = $validated['price'];
        $producto->stock = $validated['stock'];
        $producto->category = $validated['category'];
        $producto->is_available = $request->has('is_available');

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($producto->image) {
                Storage::disk('public')->delete($producto->image);
            }

            $imagePath = $request->file('image')->store('products', 'public');
            $producto->image = $imagePath;
        }

        $producto->save();

        return redirect()->route('vendedor.productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $producto)
    {
        if ($producto->user_id != Auth::id()) {
            return redirect()->route('vendedor.productos.index')
                ->with('error', 'No tienes permisos para eliminar este producto.');
        }

        // Eliminar imagen si existe
        if ($producto->image) {
            Storage::disk('public')->delete($producto->image);
        }

        $producto->delete();

        return redirect()->route('vendedor.productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * Update stock of the specified resource.
     */
    public function updateStock(Request $request, Product $producto)
    {
        if ($producto->user_id != Auth::id()) {
            return redirect()->route('vendedor.productos.index')
                ->with('error', 'No tienes permisos para actualizar este producto.');
        }

        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $producto->stock = $validated['stock'];
        $producto->save();

        return redirect()->route('vendedor.productos.show', $producto)
            ->with('success', 'Stock actualizado correctamente.');
    }
}