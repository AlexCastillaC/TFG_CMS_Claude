@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="mb-4">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left"></i> Volver a mis pedidos
            </a>
            <h1>Detalles del Pedido #{{ $order->order_number }}</h1>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <!-- Datos del pedido -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Información del pedido</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Número de pedido:</strong> {{ $order->order_number }}</p>
                                <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Total:</strong> ${{ number_format($order->total, 2) }}</p>
                                <p>
                                    <strong>Estado:</strong>
                                    @php
                                        $statusClass = [
                                            'pendiente' => 'warning',
                                            'procesando' => 'info',
                                            'enviado' => 'primary',
                                            'entregado' => 'success',
                                            'cancelado' => 'danger'
                                        ][$order->status];
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Método de pago:</strong>
                                    @if($order->payment_method == 'transfer')
                                        Transferencia bancaria
                                    @elseif($order->payment_method == 'cash')
                                        Efectivo
                                    @else
                                        {{ $order->payment_method }}
                                    @endif
                                </p>
                                <p>
                                    <strong>Estado de pago:</strong>
                                    @php
                                        $paymentStatusClass = [
                                            'pendiente' => 'warning',
                                            'pagado' => 'success',
                                            'reembolsado' => 'info'
                                        ][$order->payment_status];
                                    @endphp
                                    <span
                                        class="badge bg-{{ $paymentStatusClass }}">{{ ucfirst($order->payment_status) }}</span>
                                </p>
                                @if($order->payment_method == 'transfer' && $order->payment_status == 'pendiente')
                                    <div class="alert alert-warning mt-2">
                                        <h6 class="alert-heading">Datos bancarios para transferencia:</h6>
                                        <p class="mb-0"><strong>Banco:</strong> Banco Nacional</p>
                                        <p class="mb-0"><strong>Titular:</strong> Mi Tienda S.A.</p>
                                        <p class="mb-0"><strong>Cuenta:</strong> 0000-0000-0000-0000</p>
                                        <p class="mb-0"><strong>Concepto:</strong> {{ $order->order_number }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($order->vendor_id == Auth::id())
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6>Actualizar estado del pedido</h6>
                                    <form action="{{ route('orders.update-status', $order) }}" method="POST" class="d-flex">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select me-2">
                                            <option value="pendiente" {{ $order->status == 'pendiente' ? 'selected' : '' }}>
                                                Pendiente</option>
                                            <option value="procesando" {{ $order->status == 'procesando' ? 'selected' : '' }}>
                                                Procesando</option>
                                            <option value="enviado" {{ $order->status == 'enviado' ? 'selected' : '' }}>Enviado
                                            </option>
                                            <option value="entregado" {{ $order->status == 'entregado' ? 'selected' : '' }}>
                                                Entregado</option>
                                            <option value="cancelado" {{ $order->status == 'cancelado' ? 'selected' : '' }}>
                                                Cancelado</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary">Actualizar</button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <h6>Actualizar estado de pago</h6>
                                    <form action="{{ route('orders.update-payment-status', $order) }}" method="POST"
                                        class="d-flex">
                                        @csrf
                                        @method('PATCH')
                                        <select name="payment_status" class="form-select me-2">
                                            <option value="pendiente" {{ $order->payment_status == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                            <option value="pagado" {{ $order->payment_status == 'pagado' ? 'selected' : '' }}>
                                                Pagado</option>
                                            <option value="reembolsado" {{ $order->payment_status == 'reembolsado' ? 'selected' : '' }}>Reembolsado</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary">Actualizar</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Productos del pedido -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Productos</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    @if($item->product->image)
                                                        <img src="{{ asset('storage/' . $item->product->image) }}"
                                                            alt="{{ $item->product->name }}" class="img-thumbnail me-3"
                                                            style="width: 60px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-secondary me-3" style="width: 60px; height: 60px;"></div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $item->product->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">${{ number_format($item->price, 2) }}</td>
                                            <td class="align-middle">{{ $item->quantity }}</td>
                                            <td class="align-middle">${{ number_format($item->price * $item->quantity, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total:</td>
                                        <td class="fw-bold">${{ number_format($order->total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="d-flex mb-4">
                    <a href="{{ route('orders.invoice', $order->order_number) }}" class="btn btn-outline-primary me-2">
                        <i class="bi bi-file-text"></i> Ver factura
                    </a>

                    @if(in_array($order->status, ['pendiente', 'procesando']) && Auth::user()->role == 'cliente')
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                            data-bs-target="#cancelOrderModal">
                            <i class="bi bi-x-circle"></i> Cancelar pedido
                        </button>

                        <!-- Modal de confirmación para cancelar -->
                        <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Confirmar cancelación</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>¿Estás seguro de que deseas cancelar este pedido? Esta acción no se puede deshacer.
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        <form action="{{ route('orders.cancel', $order) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-danger">Cancelar pedido</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <!-- Datos de envío -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Información de envío</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong> {{ $order->name }}</p>
                        <p><strong>Email:</strong> {{ $order->email }}</p>
                        <p><strong>Teléfono:</strong> {{ $order->phone }}</p>
                        <p><strong>Documento:</strong> {{ $order->document }}</p>
                        <p><strong>Dirección:</strong> {{ $order->address }}</p>
                        <p><strong>Ciudad:</strong> {{ $order->city }}</p>
                        <p><strong>Estado/Provincia:</strong> {{ $order->state }}</p>
                        <p><strong>Código postal:</strong> {{ $order->postal_code }}</p>

                        @if($order->notes)
                            <hr>
                            <h6>Notas:</h6>
                            <p>{{ $order->notes }}</p>
                        @endif
                    </div>
                </div>

                <!-- Seguimiento (solo se muestra si el pedido está enviado) -->
                @if($order->status == 'enviado')
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Seguimiento del envío</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center my-3">
                                <div class="timeline">
                                    <div class="timeline-step">
                                        <div class="timeline-content" data-toggle="tooltip" data-placement="top"
                                            title="Pedido confirmado">
                                            <div class="inner-circle bg-success">
                                                <i class="bi bi-check-lg text-white"></i>
                                            </div>
                                            <p class="h6 mt-2 mb-0">Confirmado</p>
                                        </div>
                                    </div>
                                    <div class="timeline-step">
                                        <div class="timeline-content" data-toggle="tooltip" data-placement="top"
                                            title="En preparación">
                                            <div class="inner-circle bg-success">
                                                <i class="bi bi-box text-white"></i>
                                            </div>
                                            <p class="h6 mt-2 mb-0">Preparando</p>
                                        </div>
                                    </div>
                                    <div class="timeline-step">
                                        <div class="timeline-content" data-toggle="tooltip" data-placement="top"
                                            title="En camino">
                                            <div class="inner-circle bg-success">
                                                <i class="bi bi-truck text-white"></i>
                                            </div>
                                            <p class="h6 mt-2 mb-0">En ruta</p>
                                        </div>
                                    </div>
                                    <div class="timeline-step">
                                        <div class="timeline-content" data-toggle="tooltip" data-placement="top"
                                            title="Entregado">
                                            <div
                                                class="inner-circle {{ $order->status == 'entregado' ? 'bg-success' : 'bg-light' }}">
                                                <i
                                                    class="bi bi-house-door {{ $order->status == 'entregado' ? 'text-white' : 'text-muted' }}"></i>
                                            </div>
                                            <p class="h6 mt-2 mb-0">Entregado</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($order->tracking_number)
                                <div class="mt-3">
                                    <p><strong>Número de seguimiento:</strong> {{ $order->tracking_number }}</p>
                                    @if($order->shipping_company)
                                        <p><strong>Compañía de envío:</strong> {{ $order->shipping_company }}</p>
                                    @endif
                                    <a href="{{ $order->tracking_url ?? '#' }}" target="_blank"
                                        class="btn btn-sm btn-primary mt-2 {{ $order->tracking_url ? '' : 'disabled' }}">
                                        <i class="bi bi-box-arrow-up-right"></i> Seguir paquete
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-info mt-3">
                                    <i class="bi bi-info-circle"></i> Pronto recibirás información de seguimiento para tu envío.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Información del vendedor (si existe) -->
                @if($order->vendor)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Información del vendedor</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                @if($order->vendor->profile_picture)
                                    <img src="{{ asset('storage/' . $order->vendor->profile_picture) }}" class="rounded-circle me-3"
                                        width="50" height="50" alt="{{ $order->vendor->name }}">
                                @else
                                    <div class="rounded-circle bg-secondary me-3 d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <i class="bi bi-person text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0">{{ $order->vendor->name }}</h6>
                                    <small class="text-muted">{{ $order->vendor->email }}</small>
                                </div>
                            </div>

                            <a href="{{ route('vendors.show', $order->vendor->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-shop"></i> Ver perfil del vendedor
                            </a>

                            @if(Auth::user()->role == 'cliente')
                                <a href="{{ route('messages.create', ['recipient' => $order->vendor->id, 'order' => $order->order_number]) }}"
                                    class="btn btn-sm btn-outline-secondary ms-2">
                                    <i class="bi bi-chat-dots"></i> Contactar
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .timeline {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        .timeline:before {
            content: "";
            position: absolute;
            width: 100%;
            height: 2px;
            background-color: #e5e5e5;
            top: 15px;
            z-index: 1;
        }

        .timeline-step {
            z-index: 2;
            text-align: center;
            flex: 1;
        }

        .inner-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .timeline-content {
            position: relative;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Inicializar los tooltips de Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endpush