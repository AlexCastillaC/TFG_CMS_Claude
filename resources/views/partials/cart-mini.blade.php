<div class="dropdown">
    <button class="btn btn-outline-primary position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-cart3"></i>
        @if(session('cart') && count(session('cart')) > 0)
            @php
                $itemCount = 0;
                foreach(session('cart') as $item) {
                    $itemCount += $item['quantity'];
                }
            @endphp
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $itemCount }}
            </span>
        @endif
    </button>
    <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 300px;">
        <h6 class="dropdown-header">Carrito de Compras</h6>
        
        @if(session('cart') && count(session('cart')) > 0)
            <div class="cart-items" style="max-height: 300px; overflow-y: auto;">
                @php $total = 0; @endphp
                @foreach(session('cart') as $id => $details)
                    @php 
                        $product = \App\Models\Product::find($id);
                        $subtotal = $product->price * $details['quantity'];
                        $total += $subtotal;
                    @endphp
                    <div class="cart-item d-flex align-items-center py-2 border-bottom">
                        <div class="flex-shrink-0" style="width: 50px;">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded" alt="{{ $product->name }}">
                            @else
                                <div class="bg-secondary rounded" style="width: 50px; height: 50px;"></div>
                            @endif
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="mb-0 text-truncate" style="max-width: 130px;">{{ $product->name }}</h6>
                            <small>{{ $details['quantity'] }} x ${{ number_format($product->price, 2) }}</small>
                        </div>
                        <div class="ms-auto">
                            ${{ number_format($subtotal, 2) }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="fw-bold">Total: ${{ number_format($total, 2) }}</span>
                <a href="{{ route('cart.index') }}" class="btn btn-primary btn-sm">Ver carrito</a>
            </div>
        @else
            <div class="text-center py-3">
                <i class="bi bi-cart text-muted" style="font-size: 2rem;"></i>
                <p class="mb-0 mt-2">Tu carrito está vacío</p>
            </div>
        @endif
    </div>
</div>