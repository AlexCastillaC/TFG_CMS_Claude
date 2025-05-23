<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Mostrar la página de checkout
     */
    public function index(): View|RedirectResponse
    {
        $cart = session()->get('cart', []);

        // Verificar si hay productos en el carrito
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'No hay productos en el carrito.');
        }

        // Verificar disponibilidad de productos antes de mostrar checkout
        foreach ($cart as $id => $details) {
            $product = Product::find($id);

            if (!$product) {
                // Eliminar producto inexistente del carrito
                unset($cart[$id]);
                session()->put('cart', $cart);
                return redirect()->route('cart.index')->with('error', 'Uno de los productos ya no está disponible.');
            }

            if ($product->stock < $details['quantity']) {
                return redirect()->route('cart.index')->with('error', "No hay suficiente stock de {$product->name}. Stock disponible: {$product->stock}");
            }
        }

        return view('checkout.index', compact('cart'));
    }

    /**
     * Procesar el pedido
     */
    public function process(Request $request): RedirectResponse
    {
        $cart = session()->get('cart', []);

        // Verificar si hay productos en el carrito
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'No hay productos en el carrito.');
        }

        // Validar los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'document' => 'required|string|max:30',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'payment_method' => 'required|in:transfer,cash,stripe',
            'notes' => 'nullable|string|max:1000',
            'stripeToken' => 'required_if:payment_method,stripe',
        ]);

        try {
            DB::beginTransaction();

            // Calcular el total
            $total = 0;
            $vendor_id = null; // Asumimos que todos los productos son del mismo vendedor

            foreach ($cart as $id => $details) {
                $product = Product::findOrFail($id);
                $total += $product->price * $details['quantity'];

                // Capturar el vendor_id (asumiendo que cada producto tiene un vendor_id)
                if (!$vendor_id && isset($product->user_id)) {
                    $vendor_id = $product->user_id;
                }

                // Verificar stock nuevamente
                if ($product->stock < $details['quantity']) {
                    DB::rollBack();
                    return redirect()->route('cart.index')->with('error', "No hay suficiente stock de {$product->name}. Stock disponible: {$product->stock}");
                }
            }

            // Determinar el estado de pago inicial
            $paymentStatus = 'pendiente';
            $orderStatus = 'pendiente';

            // Procesar el pago con Stripe si es el método seleccionado
            if ($request->payment_method === 'stripe') {
                try {
                    // Configurar Stripe con tu clave secreta
                    \Stripe\Stripe::setApiKey(env(''));

                    // Preparar descripción del pedido
                    $productsList = [];
                    foreach ($cart as $id => $details) {
                        $product = Product::findOrFail($id);
                        $productsList[] = $details['quantity'] . 'x ' . $product->name;
                    }
                    $description = 'Pedido: ' . implode(', ', $productsList);

                    // Crear el cargo en Stripe (convirtiendo el total a centavos)
                    $charge = \Stripe\Charge::create([
                        'amount' => (int) ($total * 100),
                        'currency' => 'eur', // Cambiar según tu moneda (eur, mxn, etc.)
                        'description' => $description,
                        'source' => $request->stripeToken,
                        'metadata' => [
                            'customer_name' => $request->name,
                            'customer_email' => $request->email,
                        ],
                    ]);

                    // Si se procesa correctamente, actualizar estado de pago
                    if ($charge->paid) {
                        $paymentStatus = 'pagado';
                        $orderStatus = 'procesando';
                    }
                } catch (\Stripe\Exception\CardException $e) {
                    // Error de tarjeta (declinada, fondos insuficientes, etc.)
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Error en la tarjeta: ' . $e->getMessage());
                } catch (\Exception $e) {
                    // Otros errores de Stripe o del servidor
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Error en el procesamiento del pago: ' . $e->getMessage());
                }
            }

            // Crear el pedido
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'client_id' => auth()->id() ?? 1, // Ajusta según tu lógica para usuarios no autenticados
                'vendor_id' => $vendor_id ?? 1, // Ajusta según tu modelo de negocio
                'total' => $total,
                'status' => $orderStatus,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'document' => $request->document,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'notes' => $request->notes,
                'payment_details' => $request->payment_method === 'stripe' ? json_encode([
                    'transaction_id' => $charge->id ?? null,
                    'payment_method' => 'stripe',
                    'card_brand' => $charge->payment_method_details->card->brand ?? null,
                    'card_last4' => $charge->payment_method_details->card->last4 ?? null,
                ]) : null,
            ]);

            // Crear los ítems del pedido y actualizar stock
            foreach ($cart as $id => $details) {
                $product = Product::findOrFail($id);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'price' => $product->price,
                    'quantity' => $details['quantity'],
                ]);

                // Actualizar stock
                $product->stock -= $details['quantity'];
                $product->save();
            }

            // Limpiar el carrito
            session()->forget('cart');

            DB::commit();

            // Enviar correo de confirmación (si tienes esta funcionalidad)
            // Mail::to($request->email)->send(new OrderConfirmation($order));

            // Redirigir a la página de confirmación
            return redirect()->route('checkout.success', $order->order_number)
                ->with('success', 'Pedido realizado con éxito. Número de pedido: ' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ha ocurrido un error al procesar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar página de éxito
     */
    public function success(string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        return view('checkout.success', compact('order'));
    }
}