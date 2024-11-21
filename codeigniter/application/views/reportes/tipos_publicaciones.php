<div class="content-page">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Análisis por Tipos de Publicaciones</h4>
                    
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
                    <!-- Gráficos -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <canvas id="tiposDonutChart"></canvas>
                            <h5 class="text-center mt-3">Distribución de Solicitudes por Tipo</h5>
                        </div>
                        <div class="col-md-6">
                            <canvas id="tiposBarChart"></canvas>
                            <h5 class="text-center mt-3">Promedio de Días de Préstamo por Tipo</h5>
                        </div>
                    </div>
                    
                    <!-- Tabla de datos -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tipo de Publicación</th>
                                    <th>Total Publicaciones</th>
                                    <th>Total Solicitudes</th>
                                    <th>Total Préstamos</th>
                                    <th>Promedio Días</th>
                                    <th>Tasa de Uso (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estadisticas as $est): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($est->nombreTipo); ?></td>
                                        <td class="text-right"><?php echo $est->total_publicaciones; ?></td>
                                        <td class="text-right"><?php echo $est->total_solicitudes; ?></td>
                                        <td class="text-right"><?php echo $est->total_prestamos; ?></td>
                                        <td class="text-right"><?php echo number_format($est->promedio_dias_prestamo, 1); ?></td>
                                        <td class="text-right">
                                            <?php 
                                            $tasa_uso = ($est->total_publicaciones > 0) 
                                                ? ($est->total_prestamos / $est->total_publicaciones) * 100 
                                                : 0;
                                            echo number_format($tasa_uso, 1);
                                            ?>%
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Análisis y Recomendaciones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Análisis y Recomendaciones</h5>
                                    <?php
                                    $max_uso = 0;
                                    $tipo_max_uso = '';
                                    $min_uso = 100;
                                    $tipo_min_uso = '';
                                    
                                    foreach ($estadisticas as $est) {
                                        $tasa_uso = ($est->total_publicaciones > 0) 
                                            ? ($est->total_prestamos / $est->total_publicaciones) * 100 
                                            : 0;
                                        
                                        if ($tasa_uso > $max_uso) {
                                            $max_uso = $tasa_uso;
                                            $tipo_max_uso = $est->nombreTipo;
                                        }
                                        if ($tasa_uso < $min_uso && $est->total_publicaciones > 0) {
                                            $min_uso = $tasa_uso;
                                            $tipo_min_uso = $est->nombreTipo;
                                        }
                                    }
                                    ?>
                                    <ul>
                                        <li><strong>Mayor demanda:</strong> El tipo "<?php echo htmlspecialchars($tipo_max_uso); ?>" 
                                            muestra la mayor tasa de uso (<?php echo number_format($max_uso, 1); ?>%).</li>
                                        <li><strong>Menor demanda:</strong> El tipo "<?php echo htmlspecialchars($tipo_min_uso); ?>" 
                                            muestra la menor tasa de uso (<?php echo number_format($min_uso, 1); ?>%).</li>
                                        <li><strong>Recomendación:</strong> Considerar adquirir más material del tipo 
                                            "<?php echo htmlspecialchars($tipo_max_uso); ?>" debido a su alta demanda.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
<!-- Tabla Detallada de Publicaciones -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Detalle de Publicaciones por Tipo</h4>
                
                <!-- Filtro adicional -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select name="tipo" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos los tipos</option>
                            <?php foreach($tipos as $tipo): ?>
                                <option value="<?php echo $tipo->idTipo; ?>" 
                                    <?php echo ($filtros['tipo'] == $tipo->idTipo) ? 'selected' : ''; ?>>
                                    <?php echo $tipo->nombreTipo; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Editorial</th>
                                <th>Fecha Publicación</th>
                                <th>Total Solicitudes</th>
                                <th>Préstamos Activos</th>
                                <th>Préstamos Completados</th>
                                <th>Promedio Días Préstamo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($detalles_publicaciones as $pub): ?>
                                <tr>
                                    <td><?php echo $pub->titulo; ?></td>
                                    <td><?php echo $pub->nombreTipo; ?></td>
                                    <td><?php echo $pub->nombreEditorial; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($pub->fechaPublicacion)); ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">
                                            <?php echo $pub->total_solicitudes; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-warning">
                                            <?php echo $pub->prestamos_activos; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success">
                                            <?php echo $pub->prestamos_completados; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $pub->promedio_dias_prestamo ?? 'N/A'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de dona para distribución de solicitudes
    var ctxDonut = document.getElementById('tiposDonutChart').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($estadisticas, 'nombreTipo')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($estadisticas, 'total_solicitudes')); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico de barras para promedio de días
    var ctxBar = document.getElementById('tiposBarChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($estadisticas, 'nombreTipo')); ?>,
            datasets: [{
                label: 'Promedio de Días',
                data: <?php echo json_encode(array_column($estadisticas, 'promedio_dias_prestamo')); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>