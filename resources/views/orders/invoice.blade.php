@extends('layouts.invoice')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5">
            <div class="row mb-5">
                <div class="col-md-6">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" style="max-height: 50px;" class="mb-3">
                    <h2 class="mb-1">FACTURA</h2>
                    <p class="text-muted mb-0">Factura #{{ $order->order_number }}</p>
                    <p class="text-muted">Fecha: {{ $order->created_at->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <address>
                        <strong>Mi Tienda S.A.</strong><br>
                        Calle Principal #123<br>
                        Ciudad, Estado 12345<br>
                        Teléfono: (123) 456-7890<br>
                        Email: info@mitienda.com
                    </address>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-md-6">
                    <h5 class="mb-2">Cliente:</h5>
                    <address>
                        <strong>{{ $order->name }}</strong><br>
                        {{ $order->address }}<br>
                        {{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}<br>
                        Email: {{ $order->email }}<br>
                        Teléfono: {{ $order->phone }}
                    </address>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5 class="mb-2">Detalles de la orden:</h5>
                    <p class="mb-1"><strong>Número de orden:</strong> {{ $order->order_number }}</p>
                    <p class="mb-1"><strong>Fecha de orden:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
                    <p class="mb-1"><strong>Estado:</strong> {{ ucfirst($order->status) }}</p>
                    <p class="mb-1">
                        <strong>Método de pago:</strong> 
                        @if($order->payment_method == 'transfer')
                            Transferencia bancaria
                        @elseif($order->payment_method == 'cash')
                            Efectivo
                        @else
                            {{ $order->payment_method }}
                        @endif
                    </p>
                    <p class="mb-0"><strong>Estado de pago:</strong> {{ ucfirst($order->payment_status) }}</p>
                </div>
            </div>

            <div class="table-responsive mb-5">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Producto</th>
                            <th class="text-end">Precio</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">${{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td class="text-end"><strong>${{ number_format($order->total, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <h5>Términos y condiciones</h5>
                    <p class="text-muted">
                        Gracias por su compra. El pago debe realizarse dentro de los 30 días posteriores a la entrega. 
                        Esta factura está sujeta a los términos y condiciones establecidos en nuestro sitio web.
                    </p>
                </div>
                <div class="col-md-4">
                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">Factura generada electrónicamente</p>
                        <p class="text-muted mb-0">No requiere firma</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <button onclick="window.print()" class="btn btn-primary print-button">
                    <i class="bi bi-printer"></i> Imprimir factura
                </button>
                <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary ms-2 print-button">
                    <i class="bi bi-arrow-left"></i> Volver al pedido
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body {
            font-size: 12pt;
        }
        .print-button {
            display: none;
        }
    }
</style>
@endpush