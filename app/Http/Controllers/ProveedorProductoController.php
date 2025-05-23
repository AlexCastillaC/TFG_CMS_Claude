<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProveedorProductoController extends Controller
{
    /**
     * Mostrar lista de productos del proveedor
     */
    public function index()
    {
        $productos = Product::where('user_id', Auth::user()->id)->paginate(10);
        return view('provider.products.index', compact('productos'));
    }

    /**
     * Mostrar formulario de creación de producto
     */
    public function create()
    {
        $categories = ['Frutas', 'Verduras', 'Lácteos', 'Carnes', 'Embutidos', 'Artesanía', 'Panadería', 'Otros'];
        return view('provider.products.create', compact('categories'));
    }

    /**
     * Guardar nuevo producto
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

        $producto = new Product();
        $producto->user_id = Auth::id();
        $producto->name = $validated['name'];
        $producto->description = $validated['description'] ?? null;
        $producto->price = $validated['price'];
        $producto->stock = $validated['stock'];
        $producto->category = $validated['category'];
        $producto->is_available = $request->has('is_available');
       

        // Manejo de imagen (opcional)
        if ($request->hasFile('imagen')) {
            $rutaImagen = $request->file('imagen')->store('products', 'public');
            $producto->image = $rutaImagen;
        }

        $producto->save();

        return redirect()->route('provider.products.index')
            ->with('success', 'Producto creado exitosamente');
    }

    /**
     * Mostrar detalles de un producto
     */
    public function show(Product $producto)
    {
        // Verificar que el producto pertenezca al proveedor actual
        if ($producto->user_id !== Auth::user()->id) {
            abort(403, 'No tienes permiso para ver este producto');
        }

        return view('provider.products.show', compact('producto'));
    }

    /**
     * Mostrar formulario de edición de producto
     */
    public function edit(Product $producto)
    {
        // Verificar que el producto pertenezca al proveedor actual
        if ($producto->user_id !== Auth::user()->id) {
            abort(403, 'No tienes permiso para editar este producto');
        }
        $categories = ['Frutas', 'Verduras', 'Lácteos', 'Carnes', 'Embutidos', 'Artesanía', 'Panadería', 'Otros'];
        return view('provider.products.edit', compact('producto', 'categories'));
    }

    /**
     * Actualizar producto
     */
    public function update(Request $request, Product $producto)
    {
        // Verificar que el producto pertenezca al proveedor actual
        if ($producto->user_id !== Auth::user()->id) {
            abort(403, 'No tienes permiso para actualizar este producto');
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
        // Manejo de imagen (opcional)
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen) {
                \Storage::disk('public')->delete($producto->imagen);
            }

            $rutaImagen = $request->file('imagen')->store('products', 'public');
            $producto->image = $rutaImagen;
        }

        $producto->save();

        return redirect()->route('provider.products.index')
            ->with('success', 'Producto actualizado exitosamente');
    }

    /**
     * Eliminar producto
     */
    public function destroy(Product $producto)
    {
        // Verificar que el producto pertenezca al proveedor actual
        if ($producto->user_id !== Auth::user()->id) {
            abort(403, 'No tienes permiso para eliminar este producto');
        }

        // Eliminar imagen si existe
        if ($producto->image) {
            \Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()->route('provider.products.index')
            ->with('success', 'Producto eliminado exitosamente');
    }
}