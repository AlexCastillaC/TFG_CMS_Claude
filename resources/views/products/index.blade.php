@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Catálogo de Productos</h1>
    
    <!-- Filtros y búsqueda -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('products.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar productos</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nombre o descripción...">
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">Categoría</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Todas las categorías</option>
                        <option value="Frutas" {{ request('category') == 'Frutas' ? 'selected' : '' }}>Frutas</option>
                        <option value="Verduras" {{ request('category') == 'Verduras' ? 'selected' : '' }}>Verduras</option>
                        <option value="Lácteos" {{ request('category') == 'Lácteos' ? 'selected' : '' }}>Lácteos</option>
                        <option value="Carnes" {{ request('category') == 'Carnes' ? 'selected' : '' }}>Carnes</option>
                        <option value="Embutidos" {{ request('category') == 'Embutidos' ? 'selected' : '' }}>Embutidos</option>
                        <option value="Artesanía" {{ request('category') == 'Artesanía' ? 'selected' : '' }}>Artesanía</option>
                        <option value="Panadería" {{ request('category') == 'Panadería' ? 'selected' : '' }}>Panadería</option>
                        <option value="Otros" {{ request('category') == 'Otros' ? 'selected' : '' }}>Otros</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="order_by" class="form-label">Ordenar por</label>
                    <select class="form-select" id="order_by" name="order_by">
                        <option value="created_at" {{ request('order_by') == 'created_at' || !request('order_by') ? 'selected' : '' }}>Más recientes</option>
                        <option value="price" {{ request('order_by') == 'price' ? 'selected' : '' }}>Precio</option>
                        <option value="name" {{ request('order_by') == 'name' ? 'selected' : '' }}>Nombre</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="order" class="form-label">Orden</label>
                    <select class="form-select" id="order" name="order">
                        <option value="desc" {{ request('order') == 'desc' || !request('order') ? 'selected' : '' }}>Descendente</option>
                        <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                    @if(request('search') || request('category') || request('order_by') || request('order'))
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Limpiar filtros</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
    
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
                                <small class="text-muted ml-2">{{ $product->stand->name ?? 'Sin stand' }}</small>
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
            {{ $products->appends(request()->query())->links() }}
        </div>
    @else
        <div class="alert alert-info">
            No se encontraron productos con los criterios especificados.
        </div>
    @endif
</div>
@endsection