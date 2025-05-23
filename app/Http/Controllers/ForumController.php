<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    /**
     * Mostrar lista de foros disponibles según rol.
     */
    public function index()
    {
        $userRole = Auth::user()->role;
        
        // Mostrar foros para el rol del usuario
        $forums = Forum::where('role_access', $userRole)
            ->orWhere('role_access', 'all')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('social.forums.index', compact('forums'));
    }

    /**
     * Mostrar foro específico con sus temas.
     */
    public function show(Forum $forum)
    {
        // Verificar que el usuario tenga acceso a este foro
        $userRole = Auth::user()->role;
        
        if ($forum->role_access != $userRole && $forum->role_access != 'all') {
            return redirect()->route('forums.index')
                ->with('error', 'No tienes permiso para acceder a este foro');
        }
        
        // Cargar los temas con relaciones eager loading
        $forum->load(['topics' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }, 'topics.user']);
        
        return view('social.forums.show', compact('forum'));
    }

    /**
     * Crear nuevo foro (accesible por todos los usuarios).
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'role_access' => 'required|in:cliente,vendedor,proveedor',
        ]);
        
        $forum = new Forum();
        $forum->user_id = Auth::id();
        $forum->title = $request->title;
        $forum->description = $request->description;
        $forum->role_access = $request->role_access;
        $forum->save();
        
        return redirect()->route('forums.show', $forum)
            ->with('success', 'Foro creado correctamente');
    }

    /**
     * Mostrar formulario para crear foro.
     */
    public function create()
    {
        return view('social.forums.create');
    }
}