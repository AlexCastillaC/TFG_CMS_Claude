@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Crear Nuevo Foro</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('forums.store') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="title" class="col-md-4 col-form-label text-md-right">Título</label>

                                <div class="col-md-6">
                                    <input id="title" type="text" class="form-control @error('title') is-invalid @enderror"
                                        name="title" value="{{ old('title') }}" required autocomplete="title" autofocus>

                                    @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="description" class="col-md-4 col-form-label text-md-right">Descripción</label>

                                <div class="col-md-6">
                                    <textarea id="description"
                                        class="form-control @error('description') is-invalid @enderror"
                                        name="description">{{ old('description') }}</textarea>

                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="role_access" class="col-md-4 col-form-label text-md-right">Accesible
                                    para</label>

                                <div class="col-md-6">
                                    @php
                                        $userRole = Auth::user()->role;
                                        $roleLabel = '';

                                        if ($userRole == 'cliente') {
                                            $roleLabel = 'Clientes';
                                        } elseif ($userRole == 'vendedor') {
                                            $roleLabel = 'Vendedores';
                                        } elseif ($userRole == 'proveedor') {
                                            $roleLabel = 'Proveedores';
                                        }
                                    @endphp

                                    <input type="hidden" id="role_access" name="role_access" value="{{ $userRole }}">
                                    <input type="text" class="form-control" value="{{ $roleLabel }}" readonly>
                                    @error('role_access')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    @error('role_access')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Crear Foro
                                    </button>
                                    <a href="{{ route('forums.index') }}" class="btn btn-secondary ml-2">
                                        Cancelar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection