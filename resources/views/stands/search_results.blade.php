@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-3">Resultados de búsqueda</h1>
            
            @if($stands->count() > 0)
                <p class="text-muted">
                    Mostrando resultados para: "{{ $query }}"
                    ({{ $stands->firstItem() }}-{{ $stands->lastItem() }} de {{ $stands->total() }} puestos)
                </p>
            @endif
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('stands.search') }}" method="GET" class="row g-3">
                <div class="col-md-10">
                    <label for="query" class="form-label">Buscar puestos</label>
                    <input type="text" class="form-control" id="query" name="query" 
                           value="{{ $query }}" 
                           placeholder="Buscar por nombre o descripción...">
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-search me-2"></i>Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Listado de Puestos -->
    @if($stands->count() > 0)
        <div class="row">
            @foreach($stands as $stand)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ $stand->image ? asset('storage/' . $stand->image) : asset('images/default-stand.jpg') }}" 
                             class="card-img-top" alt="{{ $stand->name }}" 
                             style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body">
                            <h5 class="card-title">{{ $stand->name }}</h5>
                            <p class="card-text text-truncate">{{ $stand->description ?? 'Sin descripción' }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="badge bg-secondary">
                                    <i class="fa fa-tag me-1"></i>{{ $stand->category }}
                                </span>
                                
                                @if($stand->user)
                                    <small class="text-muted">
                                        <i class="fa fa-user me-1"></i>{{ $stand->user->name }}
                                    </small>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white">
                            <a href="{{ route('stands.show', $stand) }}" class="btn btn-primary w-100">
                                Ver detalles del puesto
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $stands->appends(['query' => $query])->links() }}
        </div>
    @else
        <div class="alert alert-info text-center">
            <p>No se encontraron puestos que coincidan con "{{ $query }}".</p>
            <p>Prueba con otros términos de búsqueda.</p>
        </div>
    @endif
</div>
@endsection