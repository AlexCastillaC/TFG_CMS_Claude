@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <div class="container py-5">
        <h1 class="mb-4">Finalizar compra</h1>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if(isset($cart) && count($cart) > 0)
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Información de contacto</h5>
                        </div>
                        <div class="card-body">
                            <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST">
                                @csrf

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nombre completo *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                            name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Correo electrónico *</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                            name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Teléfono *</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                            name="phone" value="{{ old('phone') }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="document" class="form-label">Documento de identidad *</label>
                                        <input type="text" class="form-control @error('document') is-invalid @enderror"
                                            id="document" name="document" value="{{ old('document') }}" required>
                                        @error('document')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <h5 class="card-title mt-4 mb-3">Información de envío</h5>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Dirección *</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                                        name="address" value="{{ old('address') }}" required>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="city" class="form-label">Ciudad *</label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                            name="city" value="{{ old('city') }}" required>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="state" class="form-label">Provincia/Estado *</label>
                                        <input type="text" class="form-control @error('state') is-invalid @enderror" id="state"
                                            name="state" value="{{ old('state') }}" required>
                                        @error('state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="postal_code" class="form-label">Código Postal *</label>
                                        <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                            id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required>
                                        @error('postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notas adicionales (opcional)</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                        rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <h5 class="card-title mt-4 mb-3">Método de pago</h5>

                                <div class="mb-3">
                                    <div class="payment-methods">
                                        <!-- Opción de Stripe con su propio panel -->
                                        <div class="payment-method-option mb-3">
                                            <div class="form-check payment-header p-3 border rounded">
                                                <input class="form-check-input" type="radio" name="payment_method"
                                                    id="payment_stripe" value="stripe" {{ old('payment_method') == 'stripe' ? 'checked' : '' }}>
                                                <label class="form-check-label d-flex justify-content-between w-100"
                                                    for="payment_stripe">
                                                    <div>
                                                        <i class="bi bi-credit-card me-2"></i> Pago con tarjeta (Stripe)
                                                    </div>
                                                    <div>
                                                        <img src="https://cdn.jsdelivr.net/gh/stripe/press-kit@master/logo/48x48.png"
                                                            alt="Stripe" height="24">
                                                    </div>
                                                </label>
                                            </div>

                                            <div class="payment-body p-3 border border-top-0 rounded-bottom {{ old('payment_method') == 'stripe' ? '' : 'd-none' }}"
                                                id="stripe-payment-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Información de la tarjeta</label>
                                                    <div class="p-3 border rounded">
                                                        <div class="mb-3">
                                                            <label class="form-label">Número de tarjeta *</label>
                                                            <input type="text" class="form-control" name="card_number"
                                                                placeholder="1234 5678 9012 3456" maxlength="19" 
                                                                value="{{ old('card_number') }}"
                                                                {{ old('payment_method') == 'stripe' ? 'required' : '' }}>
                                                            @error('card_number')
                                                                <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Fecha de expiración *</label>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <select class="form-select" name="card_exp_month"
                                                                            {{ old('payment_method') == 'stripe' ? 'required' : '' }}>
                                                                            <option value="">MM</option>
                                                                            @for($i = 1; $i <= 12; $i++)
                                                                                <option value="{{ sprintf('%02d', $i) }}" 
                                                                                    {{ old('card_exp_month') == sprintf('%02d', $i) ? 'selected' : '' }}>
                                                                                    {{ sprintf('%02d', $i) }}
                                                                                </option>
                                                                            @endfor
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <select class="form-select" name="card_exp_year"
                                                                            {{ old('payment_method') == 'stripe' ? 'required' : '' }}>
                                                                            <option value="">YY</option>
                                                                            @for($i = date('y'); $i <= date('y') + 10; $i++)
                                                                                <option value="{{ $i }}" 
                                                                                    {{ old('card_exp_year') == $i ? 'selected' : '' }}>
                                                                                    {{ $i }}
                                                                                </option>
                                                                            @endfor
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                @error('card_exp_month')
                                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                                @enderror
                                                                @error('card_exp_year')
                                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">CVC *</label>
                                                                <input type="text" class="form-control" name="card_cvc"
                                                                    placeholder="123" maxlength="4"
                                                                    value="{{ old('card_cvc') }}"
                                                                    {{ old('payment_method') == 'stripe' ? 'required' : '' }}>
                                                                @error('card_cvc')
                                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="form-label">Nombre en la tarjeta *</label>
                                                            <input type="text" class="form-control" name="card_name"
                                                                placeholder="NOMBRE APELLIDO"
                                                                value="{{ old('card_name') }}"
                                                                {{ old('payment_method') == 'stripe' ? 'required' : '' }}>
                                                            @error('card_name')
                                                                <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="text-muted small mb-0">
                                                    <i class="bi bi-shield-lock me-1"></i> Tus datos de pago están protegidos
                                                    con encriptación de grado bancario.
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Opción de transferencia bancaria con su propio panel -->
                                        <div class="payment-method-option mb-3">
                                            <div class="form-check payment-header p-3 border rounded">
                                                <input class="form-check-input" type="radio"
                                                    name="payment_method" id="payment_transfer" value="transfer" 
                                                    {{ old('payment_method', 'transfer') == 'transfer' ? 'checked' : '' }}>
                                                <label class="form-check-label w-100" for="payment_transfer">
                                                    <i class="bi bi-bank me-2"></i> Transferencia bancaria
                                                </label>
                                            </div>

                                            <div class="payment-body p-3 border border-top-0 rounded-bottom 
                                                {{ old('payment_method', 'transfer') == 'transfer' ? '' : 'd-none' }}"
                                                id="transfer-payment-body">
                                                <div class="alert alert-info mb-0">
                                                    <p class="mb-1"><strong>Instrucciones para la transferencia:</strong>
                                                    </p>
                                                    <p class="mb-1">Banco: Banco Nacional</p>
                                                    <p class="mb-1">Titular: Mi Tienda Online S.A.</p>
                                                    <p class="mb-1">IBAN: ES12 3456 7890 1234 5678 9012</p>
                                                    <p class="mb-0">Por favor, incluye tu nombre y número de pedido en el
                                                        concepto de la transferencia.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Opción de pago contra entrega con su propio panel -->
                                        <div class="payment-method-option mb-3">
                                            <div class="form-check payment-header p-3 border rounded">
                                                <input class="form-check-input" type="radio"
                                                    name="payment_method" id="payment_cash" value="cash" 
                                                    {{ old('payment_method') == 'cash' ? 'checked' : '' }}>
                                                <label class="form-check-label w-100" for="payment_cash">
                                                    <i class="bi bi-cash me-2"></i> Pago contra entrega
                                                </label>
                                            </div>

                                            <div class="payment-body p-3 border border-top-0 rounded-bottom 
                                                {{ old('payment_method') == 'cash' ? '' : 'd-none' }}"
                                                id="cash-payment-body">
                                                <div class="alert alert-info mb-0">
                                                    <p class="mb-0">Pagarás cuando recibas tu pedido. Puedes pagar en
                                                        efectivo o con tarjeta al repartidor.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @error('payment_method')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Campo oculto para el estado del pedido -->
                                <input type="hidden" name="status" value="pending">

                                <!-- Si el usuario está autenticado, se incluye su ID -->
                                @auth
                                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                @endauth
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Resumen del pedido</h5>
                        </div>
                        <div class="card-body">
                            <div class="order-summary">
                                @php $total = 0; @endphp
                                @foreach($cart as $id => $details)
                                    @php 
                                        $product = \App\Models\Product::find($id);
                                        $subtotal = $product->price * $details['quantity'];
                                        $total += $subtotal;
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="fw-bold">{{ $details['quantity'] }}x</span> {{ $product->name }}
                                        </div>
                                        <span>${{ number_format($subtotal, 2) }}</span>
                                    </div>
                                @endforeach

                                <hr>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Subtotal</span>
                                    <span>${{ number_format($total, 2) }}</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Envío</span>
                                    <span>$0.00</span>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold">Total</span>
                                    <span class="fw-bold fs-5">${{ number_format($total, 2) }}</span>
                                </div>

                                <!-- Campo oculto para el total -->
                                <input type="hidden" form="checkout-form" name="total" value="{{ $total }}">
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <button type="submit" form="checkout-form" class="btn btn-success w-100">
                                <i class="bi bi-check-circle me-1"></i> Confirmar pedido
                            </button>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="bi bi-arrow-left me-1"></i> Volver al carrito
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">No hay productos en tu carrito</h3>
                    <p class="text-muted">Debes añadir productos al carrito antes de realizar el checkout.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-bag"></i> Ir a la tienda
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('styles')
    <style>
        .payment-header {
            cursor: pointer;
            background-color: #f8f9fa;
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        .payment-header:hover {
            background-color: #f0f0f0;
        }

        .form-check-input:checked+.form-check-label {
            font-weight: bold;
        }

        .payment-body {
            background-color: #ffffff;
        }

        /* Arreglo para el caso donde el panel está cerrado */
        .payment-header:not(.payment-radio:checked) {
            border-radius: 0.25rem !important;
        }
    </style>
@endsection