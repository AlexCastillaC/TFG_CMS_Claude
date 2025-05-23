<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{

    //API PROPIA
    /**
     * Obtener productos destacados para mostrar en la página principal
     * @return \Illuminate\Http\JsonResponse
     */
    public function featuredProducts()
    {
        $products = Product::visibleToUser()->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->with(['stand'])
            ->get();

        return response()->json($products);
    }

    /**
     * Obtener puestos cercanos según ubicación
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function nearbyStands(Request $request)
    {
        // En una implementación real, usaríamos la ubicación del usuario
        // Por ahora, simplemente devolvemos todos los puestos ordenados por nombre
        $stands = Stand::with(['vendor:id,name,profile_picture'])
            ->orderBy('name')
            ->take(5)
            ->get();

        return response()->json($stands);
    }

    /**
     * Buscar productos por nombre o categoría
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchProducts(Request $request)
    {
        $query = $request->input('query', '');
        $category = $request->input('category', '');

        $productsQuery = Product::visibleToUser()->where('is_available', true);

        if (!empty($query)) {
            $productsQuery->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
        }

        if (!empty($category)) {
            $productsQuery->where('category', $category);
        }

        $products = $productsQuery->with(['stand', 'user:id,name'])
            ->take(20)
            ->get();

        return response()->json($products);
    }

    /**
     * Obtener estadísticas básicas del mercado
     * @return \Illuminate\Http\JsonResponse
     */
    public function marketStats()
    {
        $stats = [
            'total_vendors' => \App\Models\User::where('role', 'vendedor')->count(),
            'total_products' => Product::where('is_available', true)->count(),
            'total_stands' => Stand::count(),
        ];

        return response()->json($stats);
    }



    // API CLIMA
    /**
     * Obtener datos del clima para San Mateo, Gran Canaria
     * @return \Illuminate\Http\JsonResponse
     */
    public function weather()
    {
        $apiKey = env('OPENWEATHER_API_KEY');

        if (!$apiKey) {
            \Log::error('Clave de API de OpenWeatherMap no configurada.');
            return response()->json(['success' => false, 'error' => 'Configuración del servidor incorrecta'], 500);
        }

        $lat = '27.9275';
        $lon = '-15.5317';

        $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $apiKey,
            'units' => 'metric',
            'lang' => 'es'
        ]);

        // Si la respuesta es exitosa (código 200)
        if ($response->successful()) {
            $data = $response->json();

            // Verifica si los datos esperados están en la respuesta
            if (!isset($data['main']['temp'], $data['weather'][0]['description'], $data['main']['humidity'], $data['wind']['speed'], $data['weather'][0]['icon'])) {
                \Log::error('Estructura inesperada en la API de clima: ' . json_encode($data));
                return response()->json(['success' => false, 'error' => 'Datos incompletos recibidos del servicio'], 500);
            }

            return response()->json([
                'temperatura' => round($data['main']['temp']),
                'condicion' => $data['weather'][0]['description'],
                'humedad' => $data['main']['humidity'],
                'viento' => $data['wind']['speed'],
                'icono' => "https://openweathermap.org/img/wn/{$data['weather'][0]['icon']}@2x.png",
                'actualizacion' => now()->format('H:i'),
                'success' => true
            ]);
        } else {
            // Si OpenWeatherMap devuelve un error, registrarlo en los logs
            \Log::error('Error al obtener clima: Código ' . $response->status() . ' - ' . $response->body());
            return response()->json(['success' => false, 'error' => 'No se pudo obtener la información del clima'], 500);
        }
    }


}