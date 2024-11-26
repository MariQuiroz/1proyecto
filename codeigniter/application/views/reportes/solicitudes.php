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
                                            <a href="?export=pdf<?php echo (isset($filtros['fecha_inicio']) ? '&fecha_inicio='.$filtros['fecha_inicio'] : '').(isset($filtros['fecha_fin']) ? '&fecha_fin='.$filtros['fecha_fin'] : ''); ?>" 
                                               class="btn btn-danger">Exportar PDF</a>
                                            <a href="?export=excel<?php echo (isset($filtros['fecha_inicio']) ? '&fecha_inicio='.$filtros['fecha_inicio'] : '').(isset($filtros['fecha_fin']) ? '&fecha_fin='.$filtros['fecha_fin'] : ''); ?>" 
                                               class="btn btn-success">Exportar Excel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card-body">
                        <?php if (empty($estadisticas)): ?>
                            <div class="alert alert-info">
                                No hay datos disponibles para el período seleccionado
                            </div>
                        <?php else: ?>
                            <!-- Gráfico de líneas para tendencias -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <canvas id="solicitudesChart" style="min-height: 300px;"></canvas>
                                </div>
                            </div>
                            
                            <!-- Tabla de datos -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-dark">
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
                                                    <td class="text-right">
                                                        <?php 
                                                        echo ($est->total_solicitudes > 0) ? 
                                                            number_format(($est->aprobadas / $est->total_solicitudes) * 100, 1) : 
                                                            '0.0';
                                                        ?>%
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot class="table-info">
                                                <tr>
                                                    <th>Totales</th>
                                                    <th class="text-right">
                                                        <?php echo array_sum(array_column($estadisticas, 'total_solicitudes')); ?>
                                                    </th>
                                                    <th class="text-right">
                                                        <?php echo array_sum(array_column($estadisticas, 'aprobadas')); ?>
                                                    </th>
                                                    <th class="text-right">
                                                        <?php echo array_sum(array_column($estadisticas, 'rechazadas')); ?>
                                                    </th>
                                                    <th class="text-right">
                                                        <?php echo array_sum(array_column($estadisticas, 'pendientes')); ?>
                                                    </th>
                                                    <th class="text-right">
                                                        <?php echo array_sum(array_column($estadisticas, 'finalizadas')); ?>
                                                    </th>
                                                    <th class="text-right">
                                                        <?php
                                                        $total_general = array_sum(array_column($estadisticas, 'total_solicitudes'));
                                                        $total_aprobadas = array_sum(array_column($estadisticas, 'aprobadas'));
                                                        echo $total_general > 0 ? 
                                                            number_format(($total_aprobadas / $total_general) * 100, 1) : 
                                                            '0.0';
                                                        ?>%
                                                    </th>
                                                </tr>
                                            </tfoot>
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
                                                $total_registros = count($estadisticas);
                                                $suma_porcentajes = array_sum(array_map(function($est) {
                                                    return $est->total_solicitudes > 0 ? 
                                                        ($est->aprobadas / $est->total_solicitudes) * 100 : 0;
                                                }, $estadisticas));
                                                echo $total_registros > 0 ? 
                                                    number_format($suma_porcentajes / $total_registros, 1) : 
                                                    '0.0';
                                            ?>%</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <h5>Total Solicitudes</h5>
                                            <h2><?php echo array_sum(array_column($estadisticas, 'total_solicitudes')); ?></h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body">
                                            <h5>Pendientes Actuales</h5>
                                            <h2><?php echo array_sum(array_column($estadisticas, 'pendientes')); ?></h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('solicitudesChart').getContext('2d');
    
    <?php if (!empty($estadisticas)): ?>
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
                data: <?php echo json_encode(array_map(function($est) {
                    return $est->total_solicitudes > 0 ? 
                        ($est->aprobadas / $est->total_solicitudes) * 100 : 0;
                }, $estadisticas)); ?>,
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
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                stacked: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Evolución de Solicitudes por Período'
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                    },
                    porcentaje: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    <?php else: ?>
        // Mostrar mensaje cuando no hay datos
        var noDataText = 'No hay datos disponibles para el período seleccionado';
        ctx.font = '14px Arial';
        ctx.fillStyle = '#666';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(noDataText, ctx.canvas.width/2, ctx.canvas.height/2);
    <?php endif; ?>
});
</script>