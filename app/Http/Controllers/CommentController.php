<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Forum;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Añadir comentario a un tema.
     */
    public function store(Request $request, Forum $forum, Topic $topic)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
        
        // Verificar que el tema pertenece al foro
        if ($topic->forum_id != $forum->id) {
            return redirect()->route('forums.index');
        }
        
        $comment = new Comment();
        $comment->topic_id = $topic->id;
        $comment->user_id = Auth::id();
        $comment->content = $request->content;
        $comment->save();
        
        return redirect()->route('topics.show', [$forum, $topic])
            ->with('success', 'Comentario añadido correctamente');
    }
}