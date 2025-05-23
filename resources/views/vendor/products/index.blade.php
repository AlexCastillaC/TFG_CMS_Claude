
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Mis Productos</span>
                        <a href="{{ route('vendedor.productos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Producto
                        </a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('vendedor.stands.edit', Auth::user()->stand) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al puesto
                </a>
            </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($products->isEmpty())
                        <div class="alert alert-info">
                            No tienes productos registrados. ¡Comienza añadiendo uno!
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>
                                                @if ($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="50">
                                                @else
                                                    <img src="{{ asset('images/no-image.png') }}" alt="Sin imagen" width="50">
                                                @endif
                                            </td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category }}</td>
                                            <td>{{ number_format($product->price, 2) }} €</td>
                                            <td>
                                                <span class="{{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $product->stock }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $product->is_available ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $product->is_available ? 'Disponible' : 'No disponible' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('vendedor.productos.show', $product) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('vendedor.productos.edit', $product) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('vendedor.productos.destroy', $product) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection