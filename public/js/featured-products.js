/**
 * Función para cargar productos destacados
 */
function loadFeaturedProducts() {
    $.ajax({
        url: '/api/productos/destacados',
        method: 'GET',
        beforeSend: function() {
            // Mostrar indicador de carga
            $('#featured-products-container').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando productos destacados...</p>
                </div>
            `);
        },
        success: function(response) {
            // Limpiar el contenedor
            $('#featured-products-container').empty();
            
            if (response.length === 0) {
                $('#featured-products-container').html('<div class="alert alert-info">No hay productos destacados disponibles actualmente.</div>');
                return;
            }
            
            // Crear fila para la cuadrícula
            const row = $('<div class="row"></div>');
            $('#featured-products-container').append(row);
            
            // Generar HTML para cada producto
            response.forEach(function(product) {
                const imageUrl = product.image || '/img/product-placeholder.jpg';
                const standName = product.stand ? product.stand.name : 'Vendedor independiente';
                
                const productCard = `
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 product-card">
                            <img src="storage/${imageUrl}" class="card-img-top product-image" alt="${product.name}" onerror="this.src='/img/product-placeholder.jpg'">
                            <div class="card-body">
                                <h5 class="card-title">${product.name}</h5>
                                <p class="card-text text-truncate">${product.description || 'Sin descripción'}</p>
                                <p class="card-text"><strong>${product.price} €</strong></p>
                                <p class="card-text small">Puesto: ${standName}</p>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <a href="/productos/${product.id}" class="btn btn-sm btn-outline-primary">Ver detalle</a>
                                <button class="btn btn-sm btn-success add-to-cart" data-id="${product.id}">
                                    <i class="fas fa-cart-plus"></i> Añadir
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                row.append(productCard);
            });
            
            // Inicializar los botones de "Añadir al carrito"
            initAddToCartButtons();
        },
        error: function(error) {
            console.error('Error al cargar productos destacados:', error);
            $('#featured-products-container').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar productos. Intente nuevamente más tarde.
                </div>
            `);
        }
    });
}

/**
 * Inicializar los botones de añadir al carrito
 */
function initAddToCartButtons() {
    $('.add-to-cart').off('click').on('click', function() {
        const productId = $(this).data('id');
        const button = $(this);
        
        // Deshabilitar el botón temporalmente para evitar clics múltiples
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: 'cart.add',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: 1,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Restaurar botón
                button.prop('disabled', false).html('<i class="fas fa-cart-plus"></i> Añadir');
                
                // Mostrar notificación de éxito
                Toastify({
                    text: "Producto añadido al carrito",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#4CAF50",
                }).showToast();
                
                // Actualizar contador del carrito
                updateCartCount(response.cart_count);
            },
            error: function(error) {
                // Restaurar botón
                button.prop('disabled', false).html('<i class="fas fa-cart-plus"></i> Añadir');
                
                console.error('Error al añadir al carrito:', error);
                
                // Mostrar notificación de error
                Toastify({
                    text: "Error al añadir el producto",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#F44336",
                }).showToast();
            }
        });
    });
}

/**
 * Actualizar el contador del carrito en la interfaz
 */
function updateCartCount(count) {
    const cartBadge = $('#cart-count');
    cartBadge.text(count);
    
    if (count > 0) {
        cartBadge.removeClass('d-none');
    } else {
        cartBadge.addClass('d-none');
    }
}

// Función para verificar que la API está disponible
function checkApiStatus() {
    $.ajax({
        url: '/api/status',
        method: 'GET',
        success: function() {
            console.log('API disponible, cargando productos...');
            loadFeaturedProducts();
        },
        error: function() {
            console.error('API no disponible, reintentando en 3 segundos...');
            setTimeout(checkApiStatus, 3000);
        }
    });
}

// Asegurarse de que jQuery esté completamente cargado
$(document).ready(function() {
    // Mostrar mensaje de carga temporal para depuración
    console.log('Iniciando carga de productos destacados...');
    
    // Cargar productos destacados
    loadFeaturedProducts();
    
    // Recargar cada 5 minutos (opcional)
    setInterval(loadFeaturedProducts, 300000);
});