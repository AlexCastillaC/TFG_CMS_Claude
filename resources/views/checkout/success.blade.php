@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    
                    <h1 class="mb-3">¡Gracias por tu compra!</h1>
                    <p class="lead">Tu pedido ha sido recibido y está siendo procesado.</p>
                    
                    <div class="alert alert-info my-4">
                        <p class="mb-0">Número de pedido: <strong>{{ $order->order_number }}</strong></p>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Resumen de tu pedido:</h5>
                        <p><strong>Total:</strong> ${{ number_format($order->total, 2) }}</p>
                        <p><strong>Método de pago:</strong> 
                            @if($order->payment_method == 'transfer')
                                Transferencia bancaria
                            @elseif($order->payment_method == 'cash')
                                Pago contra entrega
                            @endif
                        </p>
                    </div>
                    
                    @if($order->payment_method == 'transfer')
                        <div class="alert alert-warning mb-4">
                            <h5 class="alert-heading">Instrucciones de pago</h5>
                            <p>Por favor, realiza la transferencia a la siguiente cuenta bancaria:</p>
                            <p><strong>Banco:</strong> Banco Nacional</p>
                            <p><strong>Titular:</strong> Mi Tienda S.A.</p>
                            <p><strong>Cuenta:</strong> 0000-0000-0000-0000</p>
                            <p><strong>Concepto:</strong> {{ $order->order_number }}</p>
                            <p class="mb-0">Una vez realizado el pago, por favor envía el comprobante al correo electrónico: pagos@mitienda.com</p>
                        </div>
                    @endif
                    
                    <p>Te hemos enviado un correo electrónico con los detalles de tu pedido a <strong>{{ $order->email }}</strong>.</p>
                    <p>Si tienes alguna pregunta sobre tu pedido, por favor contáctanos por correo electrónico o teléfono.</p>
                    
                    <div class="mt-4">
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            <i class="bi bi-bag"></i> Seguir comprando
                        </a>
                        @auth
                            <a href="{{ route('orders.show', $order->order_number) }}" class="btn btn-outline-primary ms-2">
                                <i class="bi bi-eye"></i> Ver detalles del pedido
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection