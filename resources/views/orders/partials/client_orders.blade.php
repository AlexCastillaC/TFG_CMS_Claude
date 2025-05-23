{{-- orders/partials/client_orders.blade.php --}}

@if(count($clientOrders) > 0)
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Número de Pedido</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Método de Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientOrders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>${{ number_format($order->total, 2) }}</td>
                            <td>
                                @switch($order->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                        @break
                                    @case('processing')
                                        <span class="badge bg-info text-dark">Procesando</span>
                                        @break
                                    @case('shipped')
                                        <span class="badge bg-primary">Enviado</span>
                                        @break
                                    @case('delivered')
                                        <span class="badge bg-success">Entregado</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Cancelado</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $order->status }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if($order->payment_method == 'transfer')
                                    <span>Transferencia</span>
                                @elseif($order->payment_method == 'cash')
                                    <span>Pago contra entrega</span>
                                @else
                                    <span>{{ $order->payment_method }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('orders.show', $order->order_number) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    
                                    @if($order->status == 'pending')
                                        @if($order->payment_method == 'transfer')
                                            <a href="{{ route('orders.payment', $order->order_number) }}" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-upload"></i> Pagar
                                            </a>
                                        @endif
                                        
                                        <form action="{{ route('orders.cancel', $order->order_number) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de cancelar este pedido?')">
                                                <i class="bi bi-x-circle"></i> Cancelar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $clientOrders->links() }}
    </div>
@else
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-bag-x text-muted" style="font-size: 4rem;"></i>
            <h3 class="mt-3">No tienes pedidos de compra</h3>
            <p class="text-muted">¡Realiza tu primera compra en nuestra tienda!</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">
                <i class="bi bi-bag"></i> Ir a la tienda
            </a>
        </div>
    </div>
@endif