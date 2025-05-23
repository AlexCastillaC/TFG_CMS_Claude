{{-- resources/views/proveedor/productos/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <a href="{{ route('provider.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a productos
                </a>
                <a href="{{ route('stands.edit', Auth::user()->stand) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al puesto
                </a>
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fa fa-edit mr-2"></i> Editar Producto
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('provider.products.update', $producto) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label for="name">Nombre del Producto *</label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name', $producto->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Descripción</label>
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror"
                                    name="description" rows="3">{{ old('description', $producto->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price">Precio (€) *</label>
                                        <input id="price" type="number" step="0.01" min="0"
                                            class="form-control @error('price') is-invalid @enderror" name="price"
                                            value="{{ old('price', $producto->price) }}" required>
                                        @error('price')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stock">Stock *</label>
                                        <input id="stock" type="number" min="0"
                                            class="form-control @error('stock') is-invalid @enderror" name="stock"
                                            value="{{ old('stock', $producto->stock) }}" required>
                                        @error('stock')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="category">Categoría *</label>
                                <select id="category" class="form-control @error('category') is-invalid @enderror"
                                    name="category" required>
                                    <option value="">Selecciona una categoría</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}" {{ old('category', $producto->category) == $category ? 'selected' : '' }}>{{ $category }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="image">Imagen del Producto</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('image') is-invalid @enderror"
                                        id="image" name="image">
                                    <label class="custom-file-label" for="image">Seleccionar archivo</label>
                                </div>
                                @if($producto->image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $producto->image) }}" alt="{{ $producto->name }}"
                                            class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                @endif
                                @error('image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_available" id="is_available"
                                        value="1" {{ old('is_available', $producto->is_available) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_available">
                                        Producto Disponible
                                    </label>
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection