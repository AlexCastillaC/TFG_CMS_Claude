@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Mis Pedidos</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @php
        $isClient = auth()->user()->role == 'cliente' || auth()->user()->role == 'vendedor';
        $isVendor = auth()->user()->role == 'vendedor' || auth()->user()->role == 'proveedor';
    @endphp

    @if($isClient && $isVendor)
        <ul class="nav nav-tabs mb-4" id="ordersTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="client-orders-tab" data-bs-toggle="tab" 
                        data-bs-target="#client-orders" type="button" role="tab" 
                        aria-controls="client-orders" aria-selected="true">
                    Mis Compras
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="vendor-orders-tab" data-bs-toggle="tab" 
                        data-bs-target="#vendor-orders" type="button" role="tab" 
                        aria-controls="vendor-orders" aria-selected="false">
                    Mis Ventas
                </button>
            </li>
        </ul>

        <div class="tab-content" id="ordersTabContent">
            <!-- Pestaña de Órdenes como Cliente -->
            <div class="tab-pane fade show active" id="client-orders" role="tabpanel" aria-labelledby="client-orders-tab">
                @include('orders.partials.client_orders')
            </div>
            
            <!-- Pestaña de Órdenes como Vendedor -->
            <div class="tab-pane fade" id="vendor-orders" role="tabpanel" aria-labelledby="vendor-orders-tab">
                @include('orders.partials.vendor_orders')
            </div>
        </div>
    @elseif($isClient)
        <!-- Solo Cliente -->
        @include('orders.partials.client_orders')
    @elseif($isVendor)
        <!-- Solo Vendedor -->
        @include('orders.partials.vendor_orders')
    @else
        <div class="alert alert-warning">
            No tienes permisos para ver pedidos. Contacta al administrador si crees que esto es un error.
        </div>
    @endif
</div>
@endsection