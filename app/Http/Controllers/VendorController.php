<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Stand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class VendorController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        $stand = $user->stand;
        $recentOrders = Order::where('vendor_id', $user->id)
            ->orwhere('client_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('vendor.profile', [
            'user' => $user,
            'stand' => $stand,
            'recentOrders' => $recentOrders,
        ]);

    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|max:1024',
        ]);

        if ($request->hasFile('profile_picture')) {
            // Eliminar imagen anterior si existe
            if ($user->profile_picture) {
                Storage::delete('public/' . $user->profile_picture);
            }

            // Guardar nueva imagen
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = $path;
        }

        $user->update($validated);

        // Actualizar información del puesto si existe
        if ($request->has('stand_name')) {
            $standData = $request->validate([
                'stand_name' => 'required|string|max:255',
                'stand_description' => 'nullable|string',
                'stand_location' => 'required|string|max:255',
                'stand_category' => 'nullable|string|max:100',
            ]);

            if ($user->stand) {
                $user->stand->update([
                    'name' => $standData['stand_name'],
                    'description' => $standData['stand_description'],
                    'location' => $standData['stand_location'],
                    'category' => $standData['stand_category'],
                ]);
            } else {
                // Crear un nuevo puesto si no existe
                Stand::create([
                    'user_id' => $user->id,
                    'name' => $standData['stand_name'],
                    'description' => $standData['stand_description'],
                    'location' => $standData['stand_location'],
                    'category' => $standData['stand_category'],
                ]);
            }
        }

        return redirect()->route('vendor.profile')->with('success', 'Perfil y puesto actualizados correctamente');
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
        return redirect()->route('vendor.profile')
            ->with('success', 'Contraseña actualizada exitosamente.');
    }

    public function dashboard()
    {
        $user = auth()->user();

        // Obtener estadísticas relevantes para el dashboard
        $totalProducts = $user->products()->count();
        $totalOrders = $user->vendorOrders()->count();
        $pendingOrders = $user->vendorOrders()->where('status', 'pendiente')->count();
        $totalSales = $user->vendorOrders()->where('payment_status', 'pagado')->sum('total');

        // Obtener los últimos pedidos
        $recentOrders = Order::where('vendor_id', $user->id);

        return view('vendor.dashboard', [
            'user' => $user,
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'totalSales' => $totalSales,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function showStandForm()
    {
        $user = auth()->user();
        $stand = $user->stand;

        return view('vendor.stand', [
            'user' => $user,
            'stand' => $stand
        ]);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        $store = Stand::where('user_id', $id)->first();

        $products = Product::where('stand_id', $store->id)
            ->where('is_available', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        $categories = Product::where('user_id', $id)
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();


        return view('vendor.show', compact('user', 'store', 'products', 'categories'));
    }

    public function updateStand(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        if ($user->stand) {
            $user->stand->update($validated);
        } else {
            Stand::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'description' => $validated['description'],
                'location' => $validated['location'],
                'category' => $validated['category'],
            ]);
        }

        return redirect()->route('vendor.profile')->with('success', 'Información del puesto actualizada correctamente');
    }
}