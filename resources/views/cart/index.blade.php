@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h1 class="mb-4">Carrito de compras</h1>

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

        @if(isset($cart) && count($cart) > 0)
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach($cart as $id => $details)
                                            @php 
                                                                        $product = \App\Models\Product::find($id);
                                                $subtotal = $product->price * $details['quantity'];
                                                $total += $subtotal;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($product->image)
                                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                                class="img-thumbnail me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-secondary me-3" style="width: 80px; height: 80px;"></div>
                                                        @endif
                                                        <div>
                                                            <h5 class="mb-0">{{ $product->name }}</h5>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>${{ number_format($product->price, 2) }}</td>
                                                <td>
                                                    <form action="{{ route('cart.update') }}" method="POST" class="d-flex align-items-center">
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $id }}">
                                                        <input type="number" name="quantity" value="{{ $details['quantity'] }}"
                                                            class="form-control form-control-sm me-2" min="1" max="{{ $product->stock }}"
                                                            style="width: 70px;">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                            <i class="bi bi-arrow-clockwise"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>${{ number_format($subtotal, 2) }}</td>
                                                <td>
                                                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i> Eliminar
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total:</td>
                                <td class="fw-bold">${{ number_format($total, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Seguir comprando
                </a>
                <a href="{{ route('checkout.index') }}" class="btn btn-success">
                    Proceder al pago <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="mt-4">
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-cart-x"></i> Vaciar carrito
                    </button>
                </form>
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-cart text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">Tu carrito está vacío</h3>
                    <p class="text-muted">¡Añade algunos productos para comenzar a comprar!</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-bag"></i> Ir a la tienda
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection