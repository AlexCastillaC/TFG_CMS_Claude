// public/js/market-stats.js

/**
 * Función para cargar estadísticas del mercado
 */
function loadMarketStats() {
    $.ajax({
        url: '/api/mercado/estadisticas',
        method: 'GET',
        success: function(response) {
            // Actualizar los contadores con animación
            animateCounter('#stats-vendors', response.total_vendors);
            animateCounter('#stats-products', response.total_products);
            animateCounter('#stats-stands', response.total_stands);
        },
        error: function(error) {
            console.error('Error al cargar estadísticas del mercado:', error);
        }
    });
}

/**
 * Función para animar un contador
 */
function animateCounter(elementId, finalValue) {
    const element = $(elementId);
    const startValue = parseInt(element.text()) || 0;
    const duration = 1000; // 1 segundo
    const frameRate = 60;
    const frames = duration / (1000 / frameRate);
    const increment = (finalValue - startValue) / frames;
    
    let currentValue = startValue;
    let frame = 0;
    
    const animation = setInterval(function() {
        frame++;
        currentValue += increment;
        
        if (frame >= frames) {
            clearInterval(animation);
            element.text(finalValue);
        } else {
            element.text(Math.round(currentValue));
        }
    }, 1000 / frameRate);
}

$(document).ready(function() {
    // Cargar estadísticas inmediatamente
    loadMarketStats();
    
    // Actualizar cada 5 minutos
    setInterval(loadMarketStats, 300000);
});