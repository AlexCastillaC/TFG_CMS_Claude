@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (Auth::user()->role == 'proveedor')
                <a href="{{ route('provider.profile', Auth::user()->stand) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al perfil
                </a>
                @endif
                @if (Auth::user()->role == 'vendedor')
                <a href="{{ route('vendor.profile', Auth::user()->stand) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al perfil
                </a>
                @endif
                
                <!-- Existing Stand Information Form -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Editar Puesto</h4>
                    </div>

                    <div class="card-body">
                        @if (session('info'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                {{ session('info') }}
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
                        @if (Auth::user()->role == 'vendedor')
                        <form method="POST" action="{{ route('vendedor.stands.update', $stand) }}" enctype="multipart/form-data"></form>
                        @else
                        <form method="POST" action="{{ route('provider.stands.update', $stand) }}" enctype="multipart/form-data"></form>
                        @endif
                        
                            @csrf
                            @method('PUT')

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Nombre del Puesto</label>
                                <div class="col-md-8">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name', $stand->name) }}" required autocomplete="name"
                                        autofocus>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="description" class="col-md-4 col-form-label text-md-right">Descripción</label>
                                <div class="col-md-8">
                                    <textarea id="description"
                                        class="form-control @error('description') is-invalid @enderror" name="description"
                                        rows="4">{{ old('description', $stand->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">Describe tu puesto, qué productos ofreces y qué te
                                        hace especial.</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="location" class="col-md-4 col-form-label text-md-right">Ubicación</label>
                                <div class="col-md-8">
                                    <input id="location" type="text"
                                        class="form-control @error('location') is-invalid @enderror" name="location"
                                        value="{{ old('location', $stand->location) }}" required>
                                    @error('location')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">Indica dónde se encuentra tu puesto (mercado, plaza,
                                        área, etc.)</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="category" class="col-md-4 col-form-label text-md-right">Categoría</label>
                                <div class="col-md-8">
                                    <select id="category" class="form-control @error('category') is-invalid @enderror"
                                        name="category" required>
                                        <option value="">Selecciona una categoría</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ old('category', $stand->category) == $category ? 'selected' : '' }}>{{ $category }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save mr-1"></i> Guardar Cambios
                                    </button>
                                    <a href="{{ route('stands.index', $stand) }}" class="btn btn-outline-secondary ml-2">
                                        Cancelar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- New Products Section -->
                <div class="card mt-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fa fa-shopping-basket mr-2"></i> Productos del Puesto
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($stand->products->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Precio</th>
                                            <th>Categoría</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stand->products as $product)
                                            <tr>
                                                <td>{{ $product->name }}</td>
                                                <td>${{ number_format($product->price, 2) }}</td>
                                                <td>{{ $product->category }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        @if (Auth::user()->role == 'vendedor')
                                                            <a href="{{ route('vendedor.productos.edit', $product) }}"
                                                                class="btn btn-sm btn-primary">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('vendedor.productos.destroy', $product) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if (Auth::user()->role == 'proveedor')
                                                            <a href="{{ route('provider.products.edit', $product) }}"
                                                                class="btn btn-sm btn-primary">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('provider.products.destroy', $product) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                No hay productos registrados en este puesto.
                            </div>
                        @endif

                        <div class="text-center mt-3">
                            @if (Auth::user()->role == 'vendedor')
                                <a href="{{ route('vendedor.productos.create', ['stand' => $stand->id]) }}"
                                    class="btn btn-success">
                                    <i class="fa fa-plus mr-1"></i> Añadir Nuevo Producto
                                </a>
                            @endif
                            @if (Auth::user()->role == 'proveedor')
                                <a href="{{ route('provider.products.create', ['stand' => $stand->id]) }}"
                                    class="btn btn-success">
                                    <i class="fa fa-plus mr-1"></i> Añadir Nuevo Producto
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Existing Danger Zone -->
                <div class="card mt-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Zona de Peligro</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Eliminar un puesto es una acción permanente y no se puede deshacer. Asegúrate
                            de haber retirado todos los productos antes de proceder.</p>
                        @if (Auth::user()->role == 'vendedor')
                        <form action="{{ route('vendedor.stands.destroy', $stand) }}" method="POST"onsubmit="return confirm('¿Estás seguro de querer eliminar este puesto? Esta acción no se puede deshacer.');"></form>
                        @else
                        <form action="{{ route('provider.stands.destroy', $stand) }}" method="POST"onsubmit="return confirm('¿Estás seguro de querer eliminar este puesto? Esta acción no se puede deshacer.');"></form>
                        @endif
                            
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-trash mr-1"></i> Eliminar Puesto
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection