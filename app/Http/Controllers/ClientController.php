<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function profile()
    {
        return view('client.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|max:1024',
        ]);
        
        if ($request->hasFile('profile_picture')) {
            // Eliminar imagen anterior si existe
            if ($user->profile_picture) {
                Storage::delete('public/'.$user->profile_picture);
            }
            
            // Guardar nueva imagen
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = $path;
        }
        
        $user->update($validated);
        
        return redirect()->route('client.profile')->with('success', 'Perfil actualizado correctamente');
    }

    public function changePassword(Request $request)
    {
        // Validate the request
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'confirmed'
            ]
        ], [
            'current_password.current_password' => 'La contraseña actual es incorrecta.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.regex' => 'La nueva contraseña debe incluir mayúsculas, minúsculas y números.',
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.'
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Update the password
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        // Redirect back with success message
        return redirect()->route('client.profile')
            ->with('success', 'Contraseña actualizada exitosamente.');
    }
}
