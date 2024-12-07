$(document).ready(function() {
    // Obtener todos los gráficos
    var charts = $(".oilChart");

    charts.each(function(index, chart) {
        var ctx = chart.getContext('2d');
        var chartIndex = index + 1;
        var labels = [];
        var data = [];
        var backgroundColor = [];
        var borderColor = [];

        $(".titulo" + chartIndex).each(function() {
            labels.push($(this).val());
        });

        $(".valor" + chartIndex).each(function() {
            data.push(parseInt($(this).val()));
        });

        // Calcular el total y los porcentajes
        var total = data.reduce(function(a, b) {
            return a + b;
        }, 0);

        var percentages = data.map(function(value) {
            return ((value / total) * 100).toFixed(2); // Calcular porcentaje y formatear a 2 decimales
        });

        percentages.forEach(function() {
            backgroundColor.push(getRandomColor());
            borderColor.push(getRandomColor());
        });

        var oilData = {
            labels: labels,
            datasets: [{
                label: 'Porcentaje',
                data: percentages,
                backgroundColor: backgroundColor,
                borderColor: borderColor,
                borderWidth: 1
            }]
        };

        // Crear la gráfica
        new Chart(ctx, {
            type: 'bar',
            data: oilData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + "%"; // Añadir símbolo de porcentaje a las etiquetas del eje y
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y + '%'; // Añadir símbolo de porcentaje al tooltip
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });

    // Función para generar un color aleatorio
    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
});
