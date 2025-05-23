<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Mostrar lista de conversaciones activas.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener usuarios con los que tiene conversaciones
        $conversations = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($message) use ($user) {
                return $message->sender_id == $user->id 
                    ? $message->receiver_id 
                    : $message->sender_id;
            })
            ->unique()
            ->values();
        
        $users = User::whereIn('id', $conversations)->get();
        
        return view('social.messages.index', compact('users'));
    }

    /**
     * Mostrar conversación con un usuario específico.
     */
    public function show(User $user)
    {
        $currentUser = Auth::user();
        
        // Actualizar mensajes como leídos
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        // Obtener mensajes entre los dos usuarios
        $messages = Message::where(function ($query) use ($currentUser, $user) {
                $query->where('sender_id', $currentUser->id)
                    ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($currentUser, $user) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $currentUser->id);
            })
            ->orderBy('created_at')
            ->get();
        
        return view('social.messages.show', compact('user', 'messages'));
    }

    /**
     * Enviar un mensaje a un usuario.
     */
    public function store(Request $request)
{
    $request->validate([
        'content' => 'required|string|max:1000',
        'receiver_id' => 'required|exists:users,id'
    ]);
    
    $message = new Message();
    $message->sender_id = Auth::id();
    $message->receiver_id = $request->receiver_id;
    $message->content = $request->content;
    $message->save();
    
    return redirect()->route('messages.show', $request->receiver_id)->with('success', 'Mensaje enviado');
}

    /**
     * Iniciar una nueva conversación privada.
     */
    public function create()
    {
        // Obtener lista de usuarios disponibles para iniciar conversación
        // Excluyendo al usuario actual
        $users = User::where('id', '!=', Auth::id())->get();
        
        return view('social.messages.create', compact('users'));
    }

    /**
     * Iniciar una conversación con un usuario específico.
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string|max:1000',
        ]);
        
        // Verificar que no es el mismo usuario
        if ($request->user_id == Auth::id()) {
            return redirect()->back()->with('error', 'No puedes iniciar una conversación contigo mismo');
        }
        
        // Crear el primer mensaje
        $message = new Message();
        $message->sender_id = Auth::id();
        $message->receiver_id = $request->user_id;
        $message->content = $request->content;
        $message->save();
        
        return redirect()->route('messages.show', $request->user_id)
            ->with('success', 'Conversación iniciada correctamente');
    }
}