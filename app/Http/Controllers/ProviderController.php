<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProviderController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
    $stand = $user->stand;
    $recentOrders = Order::where('vendor_id', $user->id)
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
        
        return redirect()->route('provider.profile')->with('success', 'Perfil actualizado correctamente');
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
        return redirect()->route('provider.profile')
            ->with('success', 'Contraseña actualizada exitosamente.');
    }
    
    public function dashboard()
    {
        $user = auth()->user();
        
        // Obtener estadísticas relevantes para el dashboard
        $totalProducts = $user->products()->count();
        $totalSales = $user->providerSales()->count();
        $pendingSales = $user->providerSales()->where('status', 'pendiente')->count();
        $totalRevenue = $user->providerSales()->where('payment_status', 'pagado')->sum('total');
        
        // Obtener las últimas ventas
        $recentSales = $user->providerSales()
            ->with(['vendor', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('provider.dashboard', [
            'user' => $user,
            'totalProducts' => $totalProducts,
            'totalSales' => $totalSales,
            'pendingSales' => $pendingSales,
            'totalRevenue' => $totalRevenue,
            'recentSales' => $recentSales,
        ]);
    }
    
    public function productIndex()
    {
        $user = auth()->user();
        $products = $user->products()->orderBy('created_at', 'desc')->paginate(10);
        
        return view('provider.products.index', [
            'products' => $products
        ]);
    }
    
    public function productCreate()
    {
        return view('provider.products.create');
    }
    
    public function productStore(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',
        ]);
        
        $productData = [
            'user_id' => $user->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'category' => $validated['category'],
            'is_available' => true,
        ];
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $productData['image'] = $path;
        }
        
        Product::create($productData);
        
        return redirect()->route('provider.products.index')->with('success', 'Producto creado correctamente');
    }
    
    public function productEdit($id)
    {
        $user = auth()->user();
        $product = $user->products()->findOrFail($id);
        
        return view('provider.products.edit', [
            'product' => $product
        ]);
    }
    
    public function productUpdate(Request $request, $id)
    {
        $user = auth()->user();
        $product = $user->products()->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean',
        ]);
        
        $productData = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'category' => $validated['category'],
            'is_available' => $request->has('is_available'),
        ];
        
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($product->image) {
                Storage::delete('public/'.$product->image);
            }
            
            // Guardar nueva imagen
            $path = $request->file('image')->store('products', 'public');
            $productData['image'] = $path;
        }
        
        $product->update($productData);
        
        return redirect()->route('provider.products.index')->with('success', 'Producto actualizado correctamente');
    }
    
    public function productDestroy($id)
    {
        $user = auth()->user();
        $product = $user->products()->findOrFail($id);
        
        // Eliminar imagen si existe
        if ($product->image) {
            Storage::delete('public/'.$product->image);
        }
        
        $product->delete();
        
        return redirect()->route('provider.products.index')->with('success', 'Producto eliminado correctamente');
    }
}