@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Información del puesto -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $stand->name }}</h4>
                    <span class="badge badge-info">{{ $stand->category }}</span>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            @if($stand->image)
                                <img src="{{ asset('storage/'.$stand->image) }}" alt="{{ $stand->name }}" class="img-fluid rounded" style="max-height: 200px;">
                            @else
                                <img src="{{ asset('images/stand-default.jpg') }}" alt="{{ $stand->name }}" class="img-fluid rounded" style="max-height: 200px;">
                            @endif
                        </div>
                        
                        <div class="col-md-8">
                            <h5 class="card-title">Descripción</h5>
                            <p class="card-text">{{ $stand->description ?? 'Sin descripción disponible.' }}</p>
                            
                            <h5 class="card-title mt-4">Ubicación</h5>
                            <p class="card-text">{{ $stand->location }}</p>
                            
                            <h5 class="card-title mt-4">Propietario</h5>
                            <p class="card-text">{{ $stand->user->name }}</p>
                            
                           
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Productos del puesto -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Productos Disponibles</h5>
                </div>
                
                <div class="card-body">
                    @if(count($stand->products) > 0)
                        <div class="row">
                            @foreach($stand->products as $product)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            @if($product->image)
                                                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="img-fluid mb-2" style="max-height: 100px;">
                                            @else
                                                <img src="{{ asset('images/product-default.jpg') }}" alt="{{ $product->name }}" class="img-fluid mb-2" style="max-height: 100px;">
                                            @endif
                                            
                                            <h5 class="card-title">{{ $product->name }}</h5>
                                            <p class="card-text text-success font-weight-bold">{{ number_format($product->price, 2) }}€</p>
                                            
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">Ver detalles</a>
                                            
                                           @if(Auth::check() && Auth::id() != $stand->user_id)
                                                <form action={{--  "{{ route('cart.add') }}"--}} method="POST" class="mt-2">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fa fa-shopping-cart mr-1"></i> Añadir
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            Este puesto aún no tiene productos disponibles.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Acciones del puesto -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                
                <div class="card-body">
                   {{--   @if(Auth::check() && Auth::id() != $stand->user_id)
                        <form action="{{ route('stands.favorite.toggle', $stand) }}" method="POST" class="mb-3">
                            @csrf
                            @if(Auth::user()->favoriteStands->contains($stand->id))
                                <button type="submit" class="btn btn-outline-danger btn-block">
                                    <i class="fa fa-heart mr-1"></i> Quitar de Favoritos
                                </button>
                            @else
                                <button type="submit" class="btn btn-outline-success btn-block">
                                    <i class="fa fa-heart-o mr-1"></i> Añadir a Favoritos
                                </button>
                            @endif
                        </form>
                        
                        <a href="{{ route('vendedor.stands.contact', $stand) }}" class="btn btn-primary btn-block">
                            <i class="fa fa-envelope mr-1"></i> Contactar con Vendedor
                        </a>
                    @endif--}}
                    
                    <a href="{{ route('stands.index') }}" class="btn btn-outline-secondary btn-block mt-3">
                        <i class="fa fa-arrow-left mr-1"></i> Volver al Listado
                    </a>
                </div>
            </div>
            
            <!-- Información adicional -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Información</h5>
                </div>
                
                <div class="card-body">
                    <p><strong>Categoría:</strong> {{ $stand->category }}</p>
                    <p><strong>Ubicación:</strong> {{ $stand->location }}</p>
                    <p><strong>Productos:</strong> {{ count($stand->products) }}</p>
                    <p><strong>Miembro desde:</strong> {{ $stand->created_at->format('d/m/Y') }}</p>
                    
                    <hr>
                    
                    <h6 class="font-weight-bold">Puestos similares:</h6>
                    <ul class="list-unstyled">
                        @php
                            $similarStands = \App\Models\Stand::where('category', $stand->category)
                                               ->where('id', '!=', $stand->id)
                                               ->take(3)
                                               ->get();
                        @endphp
                        
                        @if(count($similarStands) > 0)
                            @foreach($similarStands as $similarStand)
                                <li class="mb-2">
                                    <a href="{{ route('vendedor.stands.show', $similarStand) }}">
                                        {{ $similarStand->name }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li>No hay puestos similares disponibles.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection