<?php

namespace App\Http\Middleware;

use App\Models\Forum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckForumAccess
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $forumId = $request->route('forum');
        
        // Si es un objeto Forum, obtenemos su ID
        if ($forumId instanceof Forum) {
            $forumId = $forumId->id;
        }
        
        $forum = Forum::findOrFail($forumId);
        $user = Auth::user();
        
        // Verificar si el rol del usuario coincide con el rol de acceso del foro
        if ($forum->role_access !== $user->role) {
            return redirect()->route('forums.index')
                ->with('error', 'No tienes permiso para acceder a este foro.');
        }
        
        return $next($request);
    }
}