<div class="container mt-4">
    <h2>Reporte de Préstamos por Período</h2>
    
    <?php if (empty($prestamos)): ?>
        <p>No hay datos disponibles para el período seleccionado.</p>
    <?php else: ?>
        <table class="table table-striped">
            <!-- ... (table content remains the same) ... -->
        </table>

        <div id="grafico-prestamos" style="width: 100%; height: 400px;"></div>

        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var prestamosData = [<?= implode(',', array_column($prestamos, 'total_prestamos')) ?>];
                var fechas = [<?= "'" . implode("','", array_column($prestamos, 'fecha')) . "'" ?>];
                
                var options = {
                    chart: {
                        height: 400,
                        type: 'line',
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        }
                    },
                    colors: ['#f0643b'],
                    series: [{
                        name: 'Préstamos',
                        data: prestamosData
                    }],
                    dataLabels: {
                        enabled: true
                    },
                    stroke: {
                        width: 3,
                        curve: 'smooth'
                    },
                    grid: {
                        row: {
                            colors: ['transparent', 'transparent'],
                            opacity: 0.2
                        },
                        borderColor: '#f1f3fa'
                    },
                    xaxis: {
                        categories: fechas,
                        title: {
                            text: 'Fecha'
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Total de Préstamos'
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        floating: true,
                        offsetY: -25,
                        offsetX: -5
                    },
                    responsive: [{
                        breakpoint: 600,
                        options: {
                            chart: {
                                toolbar: {
                                    show: false
                                }
                            },
                            legend: {
                                show: false
                            },
                        }
                    }]
                };

                try {
                    var chart = new ApexCharts(document.querySelector("#grafico-prestamos"), options);
                    chart.render();
                } catch (error) {
                    console.error("Error al renderizar el gráfico:", error);
                    document.querySelector("#grafico-prestamos").innerHTML = "Error al cargar el gráfico. Por favor, intente nuevamente.";
                }
            });
        </script>
    <?php endif; ?>

    <a href="<?= site_url('reportes/index') ?>" class="btn btn-secondary mt-3">Volver al Menú de Reportes</a>
</div>

<br>
<button onclick="goBack()" class="btn btn-secondary mb-3">Volver</button>
<script>
function goBack() {
    window.history.back();
}
</script>