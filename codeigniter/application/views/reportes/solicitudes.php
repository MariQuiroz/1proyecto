<div class="content-page">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Análisis de Estado de Solicitudes</h4>
                    
                    <!-- Filtros -->
                    <form method="GET" class="mt-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fecha Inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control" 
                                           value="<?php echo isset($filtros['fecha_inicio']) ? $filtros['fecha_inicio'] : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fecha Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control"
                                           value="<?php echo isset($filtros['fecha_fin']) ? $filtros['fecha_fin'] : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">Filtrar</button>
                                        <a href="?export=pdf" class="btn btn-danger">Exportar PDF</a>
                                        <a href="?export=excel" class="btn btn-success">Exportar Excel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="card-body">
                    <!-- Gráfico de líneas para tendencias -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <canvas id="solicitudesChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Tabla de datos -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Mes/Año</th>
                                            <th>Total</th>
                                            <th>Aprobadas</th>
                                            <th>Rechazadas</th>
                                            <th>Pendientes</th>
                                            <th>Finalizadas</th>
                                            <th>% Aprobación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($estadisticas as $est): ?>
                                        <tr>
                                            <td><?php echo $est->mes . '/' . $est->anio; ?></td>
                                            <td class="text-right"><?php echo $est->total_solicitudes; ?></td>
                                            <td class="text-right"><?php echo $est->aprobadas; ?></td>
                                            <td class="text-right"><?php echo $est->rechazadas; ?></td>
                                            <td class="text-right"><?php echo $est->pendientes; ?></td>
                                            <td class="text-right"><?php echo $est->finalizadas; ?></td>
                                            <td class="text-right"><?php echo number_format($est->porcentaje_aprobacion, 1); ?>%</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Indicadores de Rendimiento -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>Tasa promedio de aprobación</h5>
                                    <h2><?php 
                                        $prom_aprobacion = array_sum(array_column($estadisticas, 'porcentaje_aprobacion')) / count($estadisticas);
                                        echo number_format($prom_aprobacion, 1);
                                    ?>%</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>Total Solicitudes</h5>
                                    <h2><?php 
                                        echo array_sum(array_column($estadisticas, 'total_solicitudes'));
                                    ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>Pendientes Actuales</h5>
                                    <h2><?php 
                                        echo array_sum(array_column($estadisticas, 'pendientes'));
                                    ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('solicitudesChart').getContext('2d');
    
    var labels = <?php echo json_encode(array_map(function($est) { 
        return $est->mes . '/' . $est->anio; 
    }, $estadisticas)); ?>;
    
    var data = {
        labels: labels,
        datasets: [{
            label: 'Aprobadas',
            data: <?php echo json_encode(array_column($estadisticas, 'aprobadas')); ?>,
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        },
        {
            label: 'Rechazadas',
            data: <?php echo json_encode(array_column($estadisticas, 'rechazadas')); ?>,
            borderColor: 'rgba(255, 99, 132, 1)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
        },
        {
            label: '% Aprobación',
            data: <?php echo json_encode(array_column($estadisticas, 'porcentaje_aprobacion')); ?>,
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            tension: 0.1,
            yAxisID: 'porcentaje'
        }]
    };

    new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left'
                },
                porcentaje: {
                    beginAtZero: true,
                    position: 'right',
                    max: 100,
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
});
</script>