<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Mercado Local San Mateo') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- App CSS debe cargarse al final para poder sobrescribir estilos de otras bibliotecas -->
    <!-- jQuery primero -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap luego -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Otras bibliotecas -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Mercado Local San Mateo') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('products.index') }}">{{ __('Productos') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('stands.index') }}">{{ __('Puestos') }}</a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Iniciar sesión') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Registrarse') }}</a>
                                </li>
                            @endif
                        @else
                            <!-- Mensajes privados -->
                            <li class="nav-item me-3">
                                <a class="nav-link position-relative" href="{{ route('messages.index') }}"
                                    title="Mensajes privados">
                                    <i class="fas fa-envelope fa-lg"></i>
                                    @php
                                        $unreadMessages = App\Models\Message::where('receiver_id', Auth::id())
                                            ->whereNull('read_at')
                                            ->count();
                                    @endphp
                                    @if($unreadMessages > 0)
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $unreadMessages > 99 ? '99+' : $unreadMessages }}
                                        </span>
                                    @endif
                                </a>
                            </li>

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @if(Auth::user()->isClient())
                                        <a class="dropdown-item" href="{{ route('client.profile') }}">
                                            {{ __('Mi Perfil') }}
                                        </a>
                                    @elseif(Auth::user()->isVendor())
                                        <a class="dropdown-item" href="{{ route('vendor.profile') }}">
                                            {{ __('Mi Perfil') }}
                                        </a>
                                    @elseif(Auth::user()->isProvider())
                                        <a class="dropdown-item" href="{{ route('provider.profile') }}">
                                            {{ __('Mi Perfil') }}
                                        </a>
                                    @endif

                                    <a class="dropdown-item" href="{{ route('forums.index') }}">
                                        <i class="fas fa-comments me-2"></i>{{ __('Foros comunitarios') }}
                                    </a>

                                    <a class="dropdown-item" href="{{ route('messages.index') }}">
                                        <i class="fas fa-envelope me-2"></i>{{ __('Mensajes privados') }}
                                    </a>

                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                        {{ __('Cerrar sesión') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                        <li>
                            <div class="ms-auto">
                                @include('partials.cart-mini')
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                <!-- Widget del clima -->
                <div class="weather-container mb-4">
                    <div id="weather-widget" class="card shadow-sm d-none">
                        <div class="card-body p-2">
                            <div class="d-flex align-items-center">
                                <img id="weather-icon" src="" alt="Clima" class="me-2" style="width: 40px;">
                                <div>
                                    <h6 class="mb-0">San Mateo, Gran Canaria</h6>
                                    <div class="d-flex align-items-center">
                                        <span id="weather-temp" class="me-2 h5 mb-0"></span>
                                        <span id="weather-condition" class="text-muted"></span>
                                    </div>
                                    <small class="d-block text-muted">
                                        <span id="weather-humidity"></span> |
                                        <span id="weather-wind"></span>
                                    </small>
                                    <small id="weather-updated" class="d-block text-muted"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="weather-error" class="d-none">
                        <!-- Sin contenido visible, solo para control de errores -->
                    </div>
                </div>
                @yield('content')
            </div>
        </main>
        <footer class="bg-dark text-white py-4 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Mercado Local San Mateo</h5>
                        <p>Fomentando la producción y consumo sostenible en Gran Canaria</p>
                    </div>
                    <div class="col-md-3">
                        <h5>Enlaces</h5>
                        <ul class="list-unstyled">
                            <li><a href="/" class="text-white">Inicio</a></li>
                            <li><a href="/productos" class="text-white">Productos</a></li>
                            <li><a href="/puestos" class="text-white">Puestos</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h5>Estadísticas</h5>
                        <ul class="list-unstyled">
                            <li>Vendedores: <span id="stats-vendors">0</span></li>
                            <li>Productos: <span id="stats-products">0</span></li>
                            <li>Puestos: <span id="stats-stands">0</span></li>
                        </ul>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <small>&copy; {{ date('Y') }} Mercado Local San Mateo. Todos los derechos reservados.</small>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    
    <script src="{{ asset('js/weather.js') }}"></script>
    <script src="{{ asset('js/market-stats.js') }}"></script>
    
    @yield('scripts')
</body>
</html>