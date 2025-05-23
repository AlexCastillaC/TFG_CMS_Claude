<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;


class PaymentController extends Controller
{

    public function process($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Verificar que el pedido pertenezca al usuario autenticado
        if ($order->client_id != auth()->id()) {
            abort(403);
        }

        // Verificar que el pedido esté en estado pendiente
        if ($order->payment_status != 'pendiente') {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'Este pedido ya ha sido procesado');
        }

        // Configurar parámetros para la pasarela de pago
        $paymentParams = [
            'amount' => $order->total,
            'currency' => 'EUR',
            'description' => 'Pedido #' . $order->id . ' - Mercado Local San Mateo',
            'orderId' => $order->id,
            'returnUrl' => route('payment.callback'),
            'cancelUrl' => route('payment.cancel')
        ];

        // Integración con pasarela específica (ejemplo genérico)
        $gateway = new PaymentGateway();
        $response = $gateway->createPayment($paymentParams);

        if ($response->isSuccessful()) {
            return redirect($response->getRedirectUrl());
        }

        return back()->with('error', 'Error al procesar el pago: ' . $response->getMessage());
    }

    public function callback(Request $request)
    {
        $paymentId = $request->input('payment_id');
        $orderId = $request->input('order_id');

        $order = Order::findOrFail($orderId);

        // Verificar el estado del pago con la pasarela
        $gateway = new PaymentGateway();
        $response = $gateway->verifyPayment($paymentId);

        if ($response->isSuccessful()) {
            $order->payment_status = 'pagado';
            $order->save();

            // Notificar al vendedor sobre el nuevo pedido
            event(new OrderPaid($order));

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Pago procesado correctamente');
        }

        return redirect()->route('orders.show', $order->id)
            ->with('error', 'Error en el pago: ' . $response->getMessage());
    }

    public function cancel(Request $request)
    {
        $orderId = $request->input('order_id');
        $order = Order::findOrFail($orderId);

        // Opcionalmente, cancelar el pedido o mantenerlo pendiente

        return redirect()->route('orders.show', $order->id)
            ->with('info', 'El proceso de pago ha sido cancelado');
    }
}