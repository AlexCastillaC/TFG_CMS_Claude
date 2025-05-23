<!-- resources/views/client/profile.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Perfil de Cliente</h4>
                    </div>

                    <div class="card-body">
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
                            <div class="col-md-4 text-center mb-4">
                                <div class="profile-image-container mb-3">
                                    @if($user->profile_picture)
                                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Foto de perfil"
                                            class="img-fluid rounded-circle"
                                            style="width: 200px; height: 200px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/default-profile.png') }}" alt="Foto de perfil predeterminada"
                                            class="img-fluid rounded-circle"
                                            style="width: 200px; height: 200px; object-fit: cover;">
                                    @endif
                                </div>
                                <h5>{{ $user->name }}</h5>
                                <p class="text-muted">
                                    <span class="badge badge-info">Cliente</span>
                                </p>
                                <p>Miembro desde: {{ $user->created_at->format('d/m/Y') }}</p>
                            </div>

                            <div class="col-md-8">
                                <div class="tab-content mt-3" id="profileTabsContent">
                                    <!-- Pestaña de Datos Personales -->
                                    <div class="tab-pane fade show active" id="personal" role="tabpanel"
                                        aria-labelledby="personal-tab">
                                        <form method="POST" action="{{ route('client.profile.update') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="form-group row">
                                                <label for="name" class="col-md-4 col-form-label text-md-right">Nombre
                                                    completo</label>
                                                <div class="col-md-8">
                                                    <input id="name" type="text"
                                                        class="form-control @error('name') is-invalid @enderror" name="name"
                                                        value="{{ old('name', $user->name) }}" required autocomplete="name">
                                                    @error('name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="email" class="col-md-4 col-form-label text-md-right">Correo
                                                    electrónico</label>
                                                <div class="col-md-8">
                                                    <input id="email" type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        name="email" value="{{ old('email', $user->email) }}" required
                                                        autocomplete="email">
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="phone"
                                                    class="col-md-4 col-form-label text-md-right">Teléfono</label>
                                                <div class="col-md-8">
                                                    <input id="phone" type="text"
                                                        class="form-control @error('phone') is-invalid @enderror"
                                                        name="phone" value="{{ old('phone', $user->phone) }}"
                                                        autocomplete="tel">
                                                    @error('phone')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="address"
                                                    class="col-md-4 col-form-label text-md-right">Dirección</label>
                                                <div class="col-md-8">
                                                    <textarea id="address"
                                                        class="form-control @error('address') is-invalid @enderror"
                                                        name="address"
                                                        rows="3">{{ old('address', $user->address ?? '') }}</textarea>
                                                    @error('address')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                    <small class="form-text text-muted">Esta dirección se utilizará como
                                                        predeterminada para tus envíos.</small>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="profile_picture"
                                                    class="col-md-4 col-form-label text-md-right">Foto de perfil</label>
                                                <div class="col-md-8">
                                                    <div class="custom-file">
                                                        <input type="file"
                                                            class="custom-file-input @error('profile_picture') is-invalid @enderror"
                                                            id="profile_picture" name="profile_picture" accept="image/*">
                                                        <label class="custom-file-label" for="profile_picture">Seleccionar
                                                            imagen</label>
                                                    </div>
                                                    @error('profile_picture')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                    <small class="form-text text-muted">Tamaño máximo: 1MB. Formatos
                                                        permitidos: JPG, PNG.</small>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="bio" class="col-md-4 col-form-label text-md-right">Sobre
                                                    mí</label>
                                                <div class="col-md-8">
                                                    <textarea id="bio"
                                                        class="form-control @error('bio') is-invalid @enderror" name="bio"
                                                        rows="3">{{ old('bio', $user->bio ?? '') }}</textarea>
                                                    @error('bio')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row mb-0">
                                                <div class="col-md-8 offset-md-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-save mr-1"></i> Guardar cambios
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Security Data Section -->
                                    <div class="card mb-4">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0"><i class="fa fa-lock mr-2"></i> Seguridad</h5>
                                        </div>
                                        <div class="card-body">
                                            <form method="POST" action="{{ route('client.profile.change-password') }}">
                                                @csrf

                                                <div class="form-group row">
                                                    <label for="current_password"
                                                        class="col-md-4 col-form-label text-md-right">Contraseña
                                                        actual</label>
                                                    <div class="col-md-8">
                                                        <input id="current_password" type="password"
                                                            class="form-control @error('current_password') is-invalid @enderror"
                                                            name="current_password" required>
                                                        @error('current_password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="new_password"
                                                        class="col-md-4 col-form-label text-md-right">Nueva
                                                        contraseña</label>
                                                    <div class="col-md-8">
                                                        <input id="new_password" type="password"
                                                            class="form-control @error('new_password') is-invalid @enderror"
                                                            name="new_password" required>
                                                        @error('new_password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                        <small class="form-text text-muted">
                                                            La contraseña debe tener al menos 8 caracteres e incluir
                                                            mayúsculas, minúsculas y números.
                                                        </small>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="new_password_confirmation"
                                                        class="col-md-4 col-form-label text-md-right">Confirmar nueva
                                                        contraseña</label>
                                                    <div class="col-md-8">
                                                        <input id="new_password_confirmation" type="password"
                                                            class="form-control" name="new_password_confirmation" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-0">
                                                    <div class="col-md-8 offset-md-4">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fa fa-lock mr-1"></i> Cambiar contraseña
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Actividad Reciente -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Actividad Reciente</h5>
                    </div>
                    <div class="card-body">
                        @if(count($user->clientOrders ?? []) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Detalles</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->clientOrders->take(5) as $order)
                                            <tr>
                                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                                <td>Pedido</td>
                                                <td>
                                                    <a href="{{ route('orders.show', $order->order_number) }}">
                                                        Pedido #{{ $order->id }} - {{ $order->total }}€
                                                    </a>
                                                </td>
                                                <td>
                                                    @if($order->status == 'pendiente')
                                                        <span class="badge badge-warning" style="background-color: grey;">Pendiente</span>
                                                    @elseif($order->status == 'procesando')
                                                        <span class="badge badge-info" style="background-color: blue;">Procesando</span>
                                                    @elseif($order->status == 'enviado')
                                                        <span class="badge badge-primary" style="background-color: blue;">Enviado</span>
                                                    @elseif($order->status == 'entregado')
                                                        <span class="badge badge-success" style="background-color: green;">Entregado</span>
                                                    @elseif($order->status == 'cancelado')
                                                        <span class="badge badge-danger" style="background-color: red;">Cancelado</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-primary btn-sm">Ver todos los
                                pedidos</a>
                        @else
                            <div class="alert alert-info">
                                No tienes actividad reciente. ¡Comienza a explorar el mercado local!
                            </div>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">Explorar productos</a>
                        @endif
                    </div>
                </div>

                <!-- Sección de Puestos Favoritos -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Puestos Favoritos</h5>
                    </div>
                    <div class="card-body">
                        @if(count($user->favoriteStands ?? []) > 0)
                            <div class="row">
                                @foreach($user->favoriteStands as $stand)
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <img src="{{ asset('storage/' . ($stand->image ?? 'stands/default.jpg')) }}"
                                                    alt="{{ $stand->name }}" class="img-fluid mb-2"
                                                    style="max-height: 100px; width: auto;">
                                                <h5 class="card-title">{{ $stand->name }}</h5>
                                                <p class="card-text text-muted">{{ $stand->category }}</p>
                                                <a href="{{ route('stands.show', $stand) }}"
                                                    class="btn btn-sm btn-outline-primary">Ver puesto</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                No tienes puestos favoritos. Guarda los puestos que más te gusten para acceder a ellos
                                rápidamente.
                            </div>
                            <a href="{{ route('stands.index') }}" class="btn btn-primary">Explorar puestos</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para mostrar el nombre del archivo seleccionado en el input file -->
    @push('scripts')
        <script>
            document.querySelector('.custom-file-input').addEventListener('change', function (e) {
                var fileName = e.target.files[0].name;
                var label = e.target.nextElementSibling;
                label.textContent = fileName;
            });
        </script>
    @endpush
@endsection