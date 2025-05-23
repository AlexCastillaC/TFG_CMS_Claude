/**
 * Función para buscar productos dinámicamente
 */
function searchProducts() {
    const query = $('#search-query').val();
    const category = $('#search-category').val();
    
    // Mostrar indicador de carga
    $('#search-results-container').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Buscando productos...</p>
        </div>
    `);
    
    $.ajax({
        url: '/api/productos/buscar',
        method: 'GET',
        data: {
            query: query,
            category: category
        },
        success: function(response) {
            // Limpiar el contenedor
            $('#search-results-container').empty();
            
            if (response.length === 0) {
                $('#search-results-container').html('<div class="alert alert-info">No se encontraron productos que coincidan con tu búsqueda.</div>');
                return;
            }
            
            // Crear contenedor de resultados
            const resultsGrid = $('<div class="row"></div>');
            
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
                                <p class="card-text"><strong>${typeof product.price === 'number' ? product.price.toFixed(2) : product.price} €</strong></p>
                                <p class="card-text small">Puesto: ${standName}</p>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <a href="/productos/${product.id}" class="btn btn-sm btn-outline-primary">Ver detalle</a>
                                <button class="btn btn-sm btn-success add-to-cart-search" data-id="${product.id}">
                                    <i class="fas fa-cart-plus"></i> Añadir
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                resultsGrid.append(productCard);
            });
            
            // Añadir resultados al contenedor
            $('#search-results-container').append(resultsGrid);
            
            // Inicializar los botones de "Añadir al carrito"
            initSearchAddToCartButtons();
        },
        error: function(error) {
            console.error('Error al buscar productos:', error);
            $('#search-results-container').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al realizar la búsqueda. Intente nuevamente más tarde.
                </div>
            `);
        }
    });
}

/**
 * Inicializar los botones de añadir al carrito para resultados de búsqueda
 */
function initSearchAddToCartButtons() {
    $('.add-to-cart-search').off('click').on('click', function() {
        const productId = $(this).data('id');
        const button = $(this);
        
        // Deshabilitar el botón temporalmente para evitar clics múltiples
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '/carrito/agregar',
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
                
                console.error('Error al añadir al carrito desde búsqueda:', error);
                
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

// Inicializar eventos de búsqueda
$(document).ready(function() {
    console.log('Inicializando funcionalidad de búsqueda de productos...');
    
    // Evento de envío del formulario de búsqueda
    $('#search-form').on('submit', function(e) {
        e.preventDefault();
        searchProducts();
    });
    
    // Búsqueda en tiempo real (opcional, con debounce)
    let searchTimeout;
    $('#search-query, #search-category').on('input change', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            searchProducts();
        }, 500); // Esperar 500ms después de que el usuario deje de escribir
    });
});