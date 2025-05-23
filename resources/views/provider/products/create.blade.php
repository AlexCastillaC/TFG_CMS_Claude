{{-- resources/views/proveedor/productos/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fa fa-plus-circle mr-2"></i> Crear Nuevo Producto
                        </h5>
                    </div>
                    <div class="card-body">
                    <form method="POST" action="{{ route('provider.products.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="name">Nombre del Producto *</label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Descripción</label>
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror"
                                    name="description" rows="3">{{ old('description') }}</textarea>
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
                                            value="{{ old('price') }}" required>
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
                                            value="{{ old('stock', 0) }}" required>
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
                                        <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                            {{ $category }}</option>
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
                                <input id="image" type="file" class="form-control @error('image') is-invalid @enderror"
                                    name="image">
                                <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo:
                                    2MB.</small>
                                @error('image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_available" id="is_available"
                                    value="1" {{ old('is_available', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_available">
                                    Producto disponible para la venta
                                </label>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Producto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection