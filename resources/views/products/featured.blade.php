<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Productos destacados</h2>
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Ver todos los productos</a>
    </div>
    
    @if($featuredProducts->count() > 0)
        <div class="row">
            @foreach($featuredProducts as $product)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 product-card">
                        <div class="position-relative">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.jpg') }}" 
                                 class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                            <span class="badge bg-primary position-absolute" style="top: 10px; right: 10px;">Destacado</span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title text-truncate">{{ $product->name }}</h5>
                            <p class="card-text text-primary fw-bold">${{ number_format($product->price, 2) }}</p>
                            <p class="card-text small text-truncate">{{ $product->description }}</p>
                            <p class="card-text">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="bi bi-shop me-1"></i> {{ $product->stand->name ?? 'Sin stand' }}
                                </small>
                            </p>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm w-100">Ver detalles</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            No hay productos destacados en este momento.
        </div>
    @endif
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Efecto hover en las tarjetas de productos
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
                this.style.transition = 'all 0.3s ease';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
                this.style.transition = 'all 0.3s ease';
            });
        });
    });
</script>
@endsection