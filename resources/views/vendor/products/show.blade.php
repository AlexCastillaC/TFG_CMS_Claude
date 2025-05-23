@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('vendedor.productos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </nav>

        <div class="row">
            <!-- Imagen del producto -->
            <div class="col-md-5 mb-4">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.jpg') }}"
                    class="img-fluid rounded" alt="{{ $product->name }}">
            </div>

            <!-- Detalles del producto -->
            <div class="col-md-7">
                <h1 class="mb-3">{{ $product->name }}</h1>
                <p class="fs-4 text-primary fw-bold">${{ number_format($product->price, 2) }}</p>

                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-secondary me-2">{{ $product->category }}</span>
                    @if($product->stock > 0)
                        <span class="badge bg-success">Disponible ({{ $product->stock }} unidades)</span>
                    @else
                        <span class="badge bg-danger">Agotado</span>
                    @endif
                </div>

                <div class="mb-4">
                    <h5>Descripción:</h5>
                    <p>{{ $product->description }}</p>
                </div>

                <div class="mb-4">
                    <h5>Vendido por:</h5>
                    <p><strong>{{ $product->user->name }}</strong></p>
                    <h5>Puesto:</h5>
                    <a
                        href="{{ route('stands.show', ['stand' => $product->stand->id]) }}"><strong>{{ $product->stand->name }}</strong></a>
                    </p>
                </div>

                <!-- Detalles adicionales en pestañas -->
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details"
                            type="button" role="tab" aria-selected="true">
                            Detalles adicionales
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping"
                            type="button" role="tab" aria-selected="false">
                            Envío
                        </button>
                    </li>
                </ul>
                <div class="tab-content p-3 border border-top-0 rounded-bottom" id="productTabsContent">
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        <p><strong>Fecha de publicación:</strong> {{ $product->created_at->format('d/m/Y') }}</p>
                        <p><strong>Última actualización:</strong> {{ $product->updated_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="tab-pane fade" id="shipping" role="tabpanel">
                        <p>Información sobre envíos y políticas de devolución:</p>
                        <ul>
                            <li>El tiempo de entrega es de 3-5 días hábiles.</li>
                            <li>Envío gratuito en compras mayores a $500.</li>
                            <li>Productos en buen estado pueden ser devueltos dentro de los 15 días posteriores a la compra.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Verificar disponibilidad en tiempo real
            setInterval(function () {
                {
                    {
                        {
                            --fetch('{{ route("products.checkAvailability", $product->id) }}')
                                .then(response => response.json())
                                .then(data => {
                                    const stockBadge = document.querySelector('.badge.bg-success, .badge.bg-danger');
                                    const addToCartForm = document.querySelector('form[action*="addToCart"]');

                                    if (!data.available) {
                                        stockBadge.className = 'badge bg-danger';
                                        stockBadge.textContent = 'Agotado';

                                        if (addToCartForm) {
                                            addToCartForm.style.display = 'none';
                                        }
                                    } else {
                                        stockBadge.className = 'badge bg-success';
                                        stockBadge.textContent = `Disponible (${data.stock} unidades)`;

                                        if (addToCartForm) {
                                            addToCartForm.style.display = 'block';
                                            const quantityInput = addToCartForm.querySelector('#quantity');
                                            quantityInput.max = data.stock;

                                            if (parseInt(quantityInput.value) > data.stock) {
                                                quantityInput.value = data.stock;
                                            }
                                        }
                                    }
                                }); --}
                    }
                }
            }, 30000); // Verificar cada 30 segundos
        });
    </script>
@endsection