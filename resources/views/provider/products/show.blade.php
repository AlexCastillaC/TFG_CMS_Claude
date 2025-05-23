{{-- resources/views/proveedor/productos/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fa fa-eye mr-2"></i> Detalles del Producto
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            @if($producto->image)
                                <img src="{{ Storage::url($producto->image) }}" 
                                     alt="{{ $producto->name }}" 
                                     class="img-fluid rounded mb-3" 
                                     style="max-height: 250px; object-fit: cover;">
                            @else
                                <div class="alert alert-secondary text-center">
                                    Sin imagen disponible
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th class="w-25">Nombre:</th>
                                    <td>{{ $producto->name }}</td>
                                </tr>
                                <tr>
                                    <th>Descripción:</th>
                                    <td>{{ $producto->description ?? 'Sin descripción' }}</td>
                                </tr>
                                <tr>
                                    <th>Precio:</th>
                                    <td>${{ number_format($producto->price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Stock:</th>
                                    <td>
                                        <span class="{{ $producto->stock <= 10 ? 'text-danger' : 'text-success' }}">
                                            {{ $producto->stock }} 
                                            @if($producto->stock <= 10)
                                                <small>(Stock bajo)</small>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Fecha de Creación:</th>
                                    <td>{{ $producto->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Última Actualización:</th>
                                    <td>{{ $producto->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('provider.products.edit', $producto) }}" class="btn btn-warning mr-2">
                                <i class="fa fa-edit mr-1"></i> Editar Producto
                            </a>
                            <form action="{{ route('provider.products.destroy', $producto) }}" 
                                  method="POST" 
                                  class="d-inline" 
                                  onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-trash mr-1"></i> Eliminar Producto
                                </button>
                            </form>
                            <a href="{{ route('provider.products.index') }}" class="btn btn-secondary ml-2">
                                <i class="fa fa-arrow-left mr-1"></i> Volver a Lista
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection