<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicController extends Controller
{
    /**
     * Mostrar tema específico con sus comentarios.
     */
    public function show(Forum $forum, Topic $topic)
    {
        // Verificar que el tema pertenece al foro
        if ($topic->forum_id != $forum->id) {
            return redirect()->route('forums.index');
        }
        
        // Cargar comentarios con usuarios
        $topic->load(['comments' => function ($query) {
            $query->orderBy('created_at');
        }, 'comments.user', 'user']);
        
        return view('social.topics.show', compact('forum', 'topic'));
    }

    /**
     * Crear nuevo tema en un foro.
     */
    public function store(Request $request, Forum $forum)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        $topic = new Topic();
        $topic->forum_id = $forum->id;
        $topic->user_id = Auth::id();
        $topic->title = $request->title;
        $topic->content = $request->content;
        $topic->save();
        
        return redirect()->route('topics.show', [$forum, $topic])
            ->with('success', 'Tema creado correctamente');
    }

    /**
     * Mostrar formulario para crear tema.
     */
    public function create(Forum $forum)
    {
        return view('social.topics.create', compact('forum'));
    }
}