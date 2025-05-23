{{-- resources/views/proveedor/productos/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('stands.edit', Auth::user()->stand) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al puesto
                </a>
                <h1 class="mb-4">Mis Productos</h1>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Lista de Productos</span>
                        <a href="{{ route('provider.products.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus mr-1"></i> Añadir Nuevo Producto
                        </a>
                    </div>
                    <div class="card-body">
                        @if($productos->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Imagen</th>
                                            <th>Nombre</th>
                                            <th>Precio</th>
                                            <th>Stock</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($productos as $producto)
                                            <tr>
                                                <td>
                                                    @if($producto->image)
                                                        <img src="{{ Storage::url($producto->image) }}" alt="{{ $producto->name }}"
                                                            class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                                    @else
                                                        <span class="text-muted">Sin imagen</span>
                                                    @endif
                                                </td>
                                                <td>{{ $producto->name }}</td>
                                                <td>${{ number_format($producto->price, 2) }}</td>
                                                <td>{{ $producto->stock }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('provider.products.show', $producto) }}"
                                                            class="btn btn-info btn-sm mr-1" title="Ver detalles">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('provider.products.edit', $producto) }}"
                                                            class="btn btn-warning btn-sm mr-1" title="Editar">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('provider.products.destroy', $producto) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $productos->links() }}
                        @else
                            <div class="alert alert-info">
                                No tienes productos registrados aún.
                                <a href="{{ route('provider.products.create') }}" class="alert-link">
                                    Añade tu primer producto
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection