<?php

namespace App\Http\Controllers;

use App\Models\Stand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    // Preparar las categorías disponibles para el dropdown
    $categories = ['Alimentación', 'Artesanía', 'Ropa', 'Productos ecológicos', 'Flores y plantas', 'Otros'];
    
    // Iniciar la consulta con las reglas de visibilidad
    $query = Stand::visibleToUser();
    
    // Verificar si hay filtro de categoría desde la URL
    $selectedCategory = $request->query('category');
    if ($selectedCategory && in_array($selectedCategory, $categories)) {
        $query->where('category', $selectedCategory);
    }
    
    // Obtener los stands con sus relaciones
    $stands = $query->with([
                'user',
                'products' => function ($q) {
                    $q->take(3); // Solo cargamos 3 productos para mostrar como vista previa
                }
            ])
            ->withCount('products as product_count')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
    
    // Si hay filtro de categoría, mantener el parámetro en la paginación
    if ($selectedCategory) {
        $stands->appends(['category' => $selectedCategory]);
    }
    
    return view('stands.index', compact('stands', 'categories', 'selectedCategory'));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verificar si el usuario ya tiene un puesto
        $existingStand = Stand::where('user_id', Auth::id())->first();

        if ($existingStand) {
            return redirect()->route('stands.edit', $existingStand)
                ->with('info', 'Ya tienes un puesto registrado. Puedes editarlo aquí.');
        }

        $categories = ['Alimentación', 'Artesanía', 'Ropa', 'Productos ecológicos', 'Flores y plantas', 'Otros'];

        return view('stands.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        // Verificar si el usuario ya tiene un puesto
        $existingStand = Stand::where('user_id', Auth::id())->first();

        if ($existingStand) {
            return redirect()->route('stands.edit', $existingStand)
                ->with('info', 'Ya tienes un puesto registrado. Puedes editarlo aquí.');
        }

        // Crear el puesto
        $stand = new Stand();
        $stand->user_id = Auth::id();
        $stand->name = $validated['name'];
        $stand->description = $validated['description'] ?? null;
        $stand->location = $validated['location'];
        $stand->category = $validated['category'];
        $stand->save();

        return redirect()->route('stands.show', $stand)
            ->with('success', 'Puesto creado correctamente. Ahora puedes añadir productos.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Stand $stand)
    {
        $stand->load([
            'products' => function ($query) {
                $query->visibleToUser();
            },
            'user'
        ]);

        return view('stands.show', compact('stand'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stand $stand)
    {
        // Verificar si el usuario es el propietario del puesto
        if ($stand->user_id != Auth::id()) {
            return redirect()->route('stands.index')
                ->with('error', 'No tienes permisos para editar este puesto.');
        }

        $categories = ['Alimentación', 'Artesanía', 'Ropa', 'Productos ecológicos', 'Flores y plantas', 'Otros'];

        return view('stands.edit', compact('stand', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stand $stand)
    {
        // Verificar si el usuario es el propietario del puesto
        if ($stand->user_id != Auth::id()) {
            return redirect()->route('stands.index')
                ->with('error', 'No tienes permisos para actualizar este puesto.');
        }

        // Validar datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        // Actualizar el puesto
        $stand->name = $validated['name'];
        $stand->description = $validated['description'] ?? null;
        $stand->location = $validated['location'];
        $stand->category = $validated['category'];
        $stand->save();

        if (Auth::user()->role == 'vendedor') {
            return redirect()->route('vendedor.stands.edit', compact('stand'))
                ->with('success', 'Puesto actualizado correctamente.');
        } elseif (Auth::user()->rol == 'proveedor') {
            return redirect()->route('provider.stands.edit', compact('stand'))
                ->with('success', 'Puesto actualizado correctamente.');
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stand $stand)
    {
        // Verificar si el usuario es el propietario del puesto
        if ($stand->user_id != Auth::id()) {
            return redirect()->route('stands.index')
                ->with('error', 'No tienes permisos para eliminar este puesto.');
        }

        // Verificar si el puesto tiene productos
        if ($stand->products()->count() > 0) {
            return redirect()->route('stands.show', $stand)
                ->with('error', 'No puedes eliminar un puesto que tiene productos. Elimina primero los productos.');
        }

        // Eliminar el puesto
        $stand->delete();

        return redirect()->route('stands.index')
            ->with('success', 'Puesto eliminado correctamente.');
    }

    /**
     * Display stands by category.
     */
    public function byCategory(string $category)
    {
        // Verifica que la categoría sea válida
        $validCategories = ['Alimentación', 'Artesanía', 'Ropa', 'Productos ecológicos', 'Flores y plantas', 'Otros'];

        if (!in_array($category, $validCategories)) {
            return redirect()->route('stands.index')->with('error', 'La categoría seleccionada no es válida.');
        }

        // Obtén los stands filtrados por categoría y aplicando las reglas de visibilidad
        $stands = Stand::visibleToUser()
            ->where('category', $category)
            ->with([
                'user',
                'products' => function ($query) {
                    $query->take(3); // Solo cargamos 3 productos para mostrar como vista previa
                }
            ])
            ->withCount('products as product_count')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Preparar las categorías para el dropdown
        $categories = ['Alimentación', 'Artesanía', 'Ropa', 'Productos ecológicos', 'Flores y plantas', 'Otros'];

        // Pasar la categoría seleccionada para mostrarla en la vista
        $selectedCategory = $category;

        return view('stands.index', compact('stands', 'categories', 'selectedCategory'));
    }

    /**
     * Search stands by name or description.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        $stands = Stand::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->with('user')
            ->paginate(10);

        return view('stands.search_results', compact('stands', 'query'));
    }
}