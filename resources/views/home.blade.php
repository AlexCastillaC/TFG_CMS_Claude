@extends('layouts.app')

@section('content')
    <style>
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            padding: 15px;
            max-width: 350px;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .toast.show {
            opacity: 1;
        }

        .card {
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 4px;
        }

        .card-header {
            padding: 12px 15px;
            background-color: rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            font-weight: bold;
        }

        .card-body {
            padding: 15px;
        }

        .alert {
            padding: 10px 15px;
            margin-bottom: 15px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
    </style>
    
    <!-- Notificación de inicio de sesión -->
    <div id="toastNotification" class="toast">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="alert alert-success" role="alert">
                                Estado actualizado
                            </div>

                            ¡Has iniciado sesión!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hero section -->
    <div class="jumbotron bg-light p-5 rounded mb-4">
        <h1 class="display-4">Bienvenido al Mercado Local San Mateo</h1>
        <p class="lead">Descubre productos frescos, artesanales y de proximidad directamente de los productores de Gran
            Canaria.</p>
        <hr class="my-4">
        <p>Explora nuestro catálogo de productos o visita los diferentes puestos del mercado.</p>
        <div class="d-flex gap-2">
            <a class="btn btn-primary btn-lg" href="{{ route('products.index') }}" role="button">Ver productos</a>
            <a class="btn btn-outline-secondary btn-lg" href="{{ route('stands.index') }}" role="button">Explorar
                puestos</a>
        </div>
    </div>

    <!-- Productos destacados -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Productos destacados</h2>
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Ver todos</a>
        </div>

        <div id="featured-products-container" class="row">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando productos destacados...</p>
            </div>
        </div>
    </section>
    
    <!-- Búsqueda de productos -->
    <section>
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form id="search-form" class="row g-3">
                    <div class="col-md-6">
                        <label for="search-query" class="form-label">Buscar productos:</label>
                        <input type="text" class="form-control" id="search-query" placeholder="Nombre o descripción...">
                    </div>
                    <div class="col-md-4">
                        <label for="search-category" class="form-label">Categoría:</label>
                        <select class="form-select" id="search-category">
                            <option value="">Todas las categorías</option>
                            <option value="frutas">Frutas</option>
                            <option value="verduras">Verduras</option>
                            <option value="lacteos">Lácteos</option>
                            <option value="carnes">Carnes</option>
                            <option value="panaderia">Panadería</option>
                            <option value="artesania">Artesanía</option>
                            <option value="conservas">Conservas</option>
                            <option value="otros">Otros</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="search-results-container" class="row">
    <div class="col-12 text-center py-4">
        <p class="text-muted">Utilice el formulario para buscar productos</p>
    </div>
</div>
    </section>

    <!-- Ventajas del mercado local -->
    <section class="mb-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3>¿Por qué comprar en el Mercado Local?</h3>
                <div class="row mt-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fas fa-leaf fa-2x text-success"></i>
                            </div>
                            <div>
                                <h5>Sostenibilidad</h5>
                                <p>Productos de proximidad que reducen la huella de carbono y apoyan la economía local.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fas fa-carrot fa-2x text-warning"></i>
                            </div>
                            <div>
                                <h5>Calidad y Frescura</h5>
                                <p>Alimentos frescos directamente de los agricultores y artesanos locales.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5>Comunidad</h5>
                                <p>Apoya a pequeños productores y forma parte de una comunidad comprometida.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toast = document.getElementById('toastNotification');

            // Mostrar el toast
            setTimeout(function () {
                toast.classList.add('show');
            }, 100);

            // Ocultar el toast después de 3 segundos
            setTimeout(function () {
                toast.classList.remove('show');

                // Remover el elemento después de la transición
                setTimeout(function () {
                    toast.remove();
                }, 300);
            }, 3000);
        });
    </script>
    <script src="{{ asset('js/featured-products.js') }}"></script>
    <script src="{{ asset('js/product-search.js') }}"></script>
@endsection