// public/js/weather.js

/**
 * Función para obtener y mostrar la información del clima
 */
function updateWeather() {
    $.ajax({
        url: '/api/clima',
        method: 'GET',
        success: function(response) {
            if (response && response.success !== false) {
                if (response.temperatura && response.condicion) {
                    $('#weather-temp').text(response.temperatura + '°C');
                    $('#weather-condition').text(response.condicion);
                    $('#weather-humidity').text('Humedad: ' + response.humedad + '%');
                    $('#weather-wind').text('Viento: ' + response.viento + ' m/s');
                    $('#weather-icon').attr('src', response.icono);
                    $('#weather-updated').text('Actualizado: ' + response.actualizacion);
        
                    $('#weather-widget').removeClass('d-none');
                    $('#weather-error').addClass('d-none');
                } else {
                    console.error('Respuesta incompleta de la API:', response);
                    $('#weather-widget').addClass('d-none');
                    $('#weather-error').removeClass('d-none').text('Error en los datos recibidos');
                }
            } else {
                console.error('Error en la API:', response);
                $('#weather-widget').addClass('d-none');
                $('#weather-error').removeClass('d-none').text('No se pudo obtener la información del clima');
            }
        },
        
    });
}

$(document).ready(function() {
    // Actualizar el clima inmediatamente y luego cada 30 minutos
    updateWeather();
    setInterval(updateWeather, 1800000); // 30 minutos
});