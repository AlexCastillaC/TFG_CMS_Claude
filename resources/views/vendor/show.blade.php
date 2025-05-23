@extends('layouts.app')


@section('content')
<div class="container py-5">
    <div class="mb-4">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Volver al inicio
        </a>
    </div>

    <div class="row">
        <!-- Información del vendedor -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}" 
                                 class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" 
                                 style="width: 150px; height: 150px;">
                                <i class="bi bi-person text-white" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                    </div>
                    
                    <h3 class="card-title">{{ $user->name }}</h3>
                    
                    <p class="card-text">
                        <i class="bi bi-telephone me-2"></i> {{ $user->phone }}
                    </p>
                    
                    <p class="card-text">
                        <i class="bi bi-envelope me-2"></i> {{ $user->email }}
                    </p>
                    
                    <div class="mt-4">
                        <a href="{{ route('messages.create', ['recipient' => $user->id]) }}" class="btn btn-primary">
                            <i class="bi bi-chat-dots"></i> Contactar vendedor
                        </a>
                    </div>
                </div>
            </div>
            
            @if($store)
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Información del puesto</h5>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $store->name }}</h5>
                        
                        @if($store->description)
                            <p class="card-text">{{ $store->description }}</p>
                        @endif
                        
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-geo-alt me-2"></i>
                            <span>{{ $store->location ?: 'Ubicación no disponible' }}</span>
                        </div>
                        
                        @if($store->category)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-tag me-2"></i>
                                <span>Categoría: {{ $store->category }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Productos del vendedor -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Productos</h5>
                    
                    @if($categories->count() > 0)
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="categoriesDropdown" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                Filtrar por categoría
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                                <li><a class="dropdown-item" href="{{ route('vendors.show', $user->id) }}">Todas las categorías</a></li>
                                @foreach($categories as $category)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('vendors.show', ['vendor' => $user->id, 'category' => $category->category]) }}">
                                            {{ $category->category }} ({{ $category->total }})
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($products->count() > 0)
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            @foreach($products as $product)
                                <div class="col">
                                    <div class="card h-100 product-card">
                                        <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top product-img" 
                                                    alt="{{ $product->name }}">
                                            @else
                                                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center product-img">
                                                    <i class="bi bi-image text-white" style="font-size: 2rem;"></i>
                                                </div>
                                            @endif
                                            <div class="card-body">
                                                <h6 class="card-title text-truncate">{{ $product->name }}</h6>
                                                <p class="card-text fw-bold text-primary">${{ number_format($product->price, 2) }}</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bag-x" style="font-size: 3rem;"></i>
                            <p class="mt-3">Este vendedor no tiene productos disponibles actualmente.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-img {
        height: 200px;
        object-fit: cover;
    }
    
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
@endpush