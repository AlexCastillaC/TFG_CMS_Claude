@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    @if($stand->logo)
                        <img src="{{ asset('storage/' . $stand->logo) }}" alt="{{ $stand->name }}" class="img-fluid rounded">
                    @else
                        <div class="bg-light rounded d-flex justify-content-center align-items-center" style="height: 150px">
                            <span class="text-muted">Sin logo</span>
                        </div>
                    @endif
                </div>
                <div class="col-md-9">
                    <h1 class="mb-3">{{ $stand->name }}</h1>
                    <p>{{ $stand->description }}</p>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-geo-alt me-2"></i>
                        <span>{{ $stand->location }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person me-2"></i>
                        <span>Propietario: {{ $stand->user->name ?? 'Desconocido' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h2 class="mb-4">Productos de {{ $stand->name }}</h2>
    
    <!-- Listado de productos -->
    @if($products->count() > 0)
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.jpg') }}" 
                             class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-primary fw-bold">${{ number_format($product->price, 2) }}</p>
                            <p class="card-text text-truncate">{{ $product->description }}</p>
                            <p class="card-text">
                                <span class="badge bg-secondary">{{ $product->category }}</span>
                            </p>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-primary w-100">Ver detalles</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    @else
        <div class="alert alert-info">
            Este stand aún no tiene productos disponibles.
            <a href="{{ route('products.index') }}" class="alert-link">Ver todos los productos</a>
        </div>
    @endif
</div>
@endsection