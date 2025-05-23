@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Puestos del Mercado</h1>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Filtros y búsqueda -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form action="{{ route('stands.search') }}" method="GET" class="form-inline">
                            <div class="input-group w-100">
                                <input type="text" name="query" class="form-control" placeholder="Buscar puestos..."
                                    value="{{ request('query') ?? '' }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-6">
    <div class="dropdown text-end">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="categoriesDropdown"
            data-bs-toggle="dropdown" aria-expanded="false">
            @if(isset($selectedCategory))
                {{ $selectedCategory }}
            @else
                Filtrar por Categoría
            @endif
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="categoriesDropdown">
            <li>
                <a class="dropdown-item @if(!isset($selectedCategory)) active @endif"
                    href="{{ route('stands.index') }}">Todas las categorías</a>
            </li>
            <li><hr class="dropdown-divider"></li>
            @foreach($categories as $category)
                <li>
                    <a class="dropdown-item @if(isset($selectedCategory) && $selectedCategory == $category) active @endif"
                        href="{{ route('stands.index', ['category' => $category]) }}">{{ $category }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
                </div>
            </div>

            <!-- Listado de puestos -->
            <div class="row">
                @foreach($stands as $stand)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">{{ $stand->name }}</h5>
                                <span class="badge badge-info">{{ $stand->category }}</span>
                            </div>

                            <div class="card-body">
                                <div class="text-center mb-3">
                                    @if($stand->image)
                                        <img src="{{ asset('storage/' . $stand->image) }}" alt="{{ $stand->name }}" class="img-fluid"
                                            style="max-height: 150px;">
                                    @else
                                        <img src="{{ asset('images/stand-default.jpg') }}" alt="{{ $stand->name }}"
                                            class="img-fluid" style="max-height: 150px;">
                                    @endif
                                </div>

                                <p class="card-text">
                                    {{ \Illuminate\Support\Str::limit($stand->description ?? 'Sin descripción disponible.', 100) }}
                                </p>

                                <p class="card-text">
                                    <strong>Ubicación:</strong> {{ $stand->location }}
                                </p>

                                <p class="card-text">
                                    <strong>Propietario:</strong> {{ $stand->user->name }}
                                </p>
                            </div>

                            <div class="card-footer bg-white">
                                <a href="{{ route('stands.show', $stand) }}" class="btn btn-primary btn-block">
                                    Ver Puesto
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if(count($stands) == 0)
                    <div class="col-12">
                        <div class="alert alert-info">
                            No se encontraron puestos.
                        </div>
                    </div>
                @endif
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $stands->links() }}
            </div>


        </div>
@endsection