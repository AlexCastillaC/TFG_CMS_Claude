{{-- orders/partials/vendor_orders.blade.php --}}

@if(count($vendorOrders) > 0)
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Número de Pedido</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Método de Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vendorOrders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->client->name ?? 'Cliente' }}</td>
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
                                        <form action="{{ route('orders.update-status', $order->order_number) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="processing">
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-check-circle"></i> Aceptar
                                            </button>
                                        </form>
                                    @elseif($order->status == 'processing')
                                        <form action="{{ route('orders.update-status', $order->order_number) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="shipped">
                                            <button type="submit" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-truck"></i> Enviar
                                            </button>
                                        </form>
                                    @elseif($order->status == 'shipped')
                                        <form action="{{ route('orders.update-status', $order->order_number) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="delivered">
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-check2-all"></i> Entregado
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if(in_array($order->status, ['pending', 'processing']))
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
        {{ $vendorOrders->links() }}
    </div>
@else
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-shop text-muted" style="font-size: 4rem;"></i>
            <h3 class="mt-3">No tienes pedidos de venta</h3>
            <p class="text-muted">¡Aún no has recibido pedidos como vendedor!</p>
            <a href="{{ route('products.create') }}" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle"></i> Crear un producto
            </a>
        </div>
    </div>
@endif