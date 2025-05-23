@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
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
                @if (Auth::user()->id != $product->user->id)
                @if($product->stock > 0)
                    <form action="{{ route('cart.add') }}" method="POST" class="mb-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label for="quantity" class="col-form-label">Cantidad:</label>
                            </div>
                            <div class="col-auto">
                                <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1"
                                    max="{{ $product->stock }}">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-cart-plus"></i> Añadir al carrito
                                </button>
                            </div>
                        </div>

                        @if($errors->has('quantity'))
                            <div class="text-danger mt-2">{{ $errors->first('quantity') }}</div>
                        @endif
                    </form>
                @endif
                @endif
                

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

        <!-- Productos relacionados -->
        @if($relatedProducts->count() > 0)
            <div class="mt-5">
                <h3 class="mb-4">Productos relacionados</h3>
                <div class="row">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="col-md-3 mb-4">
                            <div class="card h-100">
                                <img src="{{ $relatedProduct->image ? asset('storage/' . $relatedProduct->image) : asset('images/no-image.jpg') }}"
                                    class="card-img-top" alt="{{ $relatedProduct->name }}"
                                    style="height: 180px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $relatedProduct->name }}</h5>
                                    <p class="card-text text-primary fw-bold">${{ number_format($relatedProduct->price, 2) }}</p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="{{ route('products.show', $relatedProduct) }}"
                                        class="btn btn-outline-primary w-100">Ver detalles</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')

@endsection