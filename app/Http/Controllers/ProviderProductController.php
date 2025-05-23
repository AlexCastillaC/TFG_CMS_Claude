<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProviderProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('user_id', Auth::id())
                          ->paginate(10);
        
        return view('provider.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ['Materias Primas', 'Semillas', 'Herramientas', 'Fertilizantes', 'Envases', 'Otros'];
        
        return view('provider.products.create', compact('categories'));
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
        
        $product = new Product();
        $product->user_id = Auth::id();
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
        
        return redirect()->route('proveedor.productos.index')
                        ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $producto)
    {
        if ($producto->user_id != Auth::id()) {
            return redirect()->route('proveedor.productos.index')
                            ->with('error', 'No tienes permisos para ver este producto.');
        }
        
        return view('provider.products.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $producto)
    {
        if ($producto->user_id != Auth::id()) {
            return redirect()->route('proveedor.productos.index')
                            ->with('error', 'No tienes permisos para editar este producto.');
        }
        
        $categories = ['Materias Primas', 'Semillas', 'Herramientas', 'Fertilizantes', 'Envases', 'Otros'];
        
        return view('provider.products.edit', compact('producto', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $producto)
    {
        if ($producto->user_id != Auth::id()) {
            return redirect()->route('proveedor.productos.index')
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
        
        return redirect()->route('proveedor.productos.index')
                        ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $producto)
    {
        if ($producto->user_id != Auth::id()) {
            return redirect()->route('proveedor.productos.index')
                            ->with('error', 'No tienes permisos para eliminar este producto.');
        }
        
        // Eliminar imagen si existe
        if ($producto->image) {
            Storage::disk('public')->delete($producto->image);
        }
        
        $producto->delete();
        
        return redirect()->route('proveedor.productos.index')
                        ->with('success', 'Producto eliminado correctamente.');
    }
    
    /**
     * Update stock of the specified resource.
     */
    public function updateStock(Request $request, Product $producto)
    {
        if ($producto->user_id != Auth::id()) {
            return redirect()->route('proveedor.productos.index')
                            ->with('error', 'No tienes permisos para actualizar este producto.');
        }
        
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
        ]);
        
        $producto->stock = $validated['stock'];
        $producto->save();
        
        return redirect()->route('proveedor.productos.show', $producto)
                        ->with('success', 'Stock actualizado correctamente.');
    }
}