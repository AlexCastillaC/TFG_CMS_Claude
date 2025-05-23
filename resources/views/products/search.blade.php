@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Resultados de búsqueda: "{{ $search }}"</h1>
    
    <!-- Formulario de búsqueda -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('products.search') }}" method="GET" class="d-flex">
                <input type="text" class="form-control me-2" name="search" value="{{ $search }}" placeholder="Buscar productos...">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </form>
        </div>
    </div>
    
    <!-- Resultados -->
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
                            <p class="card-text"><small class="text-muted">{{ $product->stand->name ?? 'Sin stand' }}</small></p>
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
            {{ $products->appends(['search' => $search])->links() }}
        </div>
    @else
        <div class="alert alert-info">
            No se encontraron productos que coincidan con "{{ $search }}".
            <a href="{{ route('products.index') }}" class="alert-link">Ver todos los productos</a>
        </div>
    @endif
</div>
@endsection