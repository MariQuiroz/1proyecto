<div class="content-page">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Análisis por Tipos de Publicaciones</h4>
                    
                    <!-- Filtros -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
            <form method="get" action="<?php echo site_url('reportes/tipos_publicaciones'); ?>" class="row align-items-center">
                    <div class="col-md-3">
                        <label>Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $filtros['fecha_inicio']; ?>">
                    </div>
                    <div class="col-md-3">
                        <label>Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" value="<?php echo $filtros['fecha_fin']; ?>">
                    </div>
                    <div class="col-md-3">
                        <label>Tipo de Publicación</label>
                        <select name="tipo" class="form-control">
                            <option value="">Todos</option>
                            <?php foreach($tipos as $tipo): ?>
                                <option value="<?php echo $tipo->idTipo; ?>" 
                                    <?php echo isset($filtros['tipo']) && $filtros['tipo'] == $tipo->idTipo ? 'selected' : ''; ?>>
                                    <?php echo $tipo->nombreTipo; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary mt-3">Filtrar</button>
                        <a href="<?php echo site_url('reportes/exportar_publicaciones').'?'.http_build_query($filtros); ?>" 
                           class="btn btn-success mt-3">Exportar Excel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
               <h5>Análisis por Tipo de Publicación</h5>
               <div class="table-responsive">
                   <table class="table table-sm">
                       <thead>
                           <tr>
                               <th>Tipo</th>
                               <th>Tasa de Uso</th>
                               <th>Publicaciones</th>
                               <th>Préstamos</th>
                               <th>Días Promedio</th>
                               <th>Estado</th>
                           </tr>
                       </thead>
                       <tbody>
                           <?php 
                           $max_uso = 0;
                           $tipo_max_uso = '';
                           $min_uso = 100;
                           $tipo_min_uso = '';
                           
                           foreach ($estadisticas as $est):
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
                           ?>
                               <tr>
                                   <td><?php echo htmlspecialchars($est->nombreTipo); ?></td>
                                   <td><?php echo number_format($tasa_uso, 1); ?>%</td>
                                   <td><?php echo $est->total_publicaciones; ?></td>
                                   <td><?php echo $est->total_prestamos; ?></td>
                                   <td><?php echo number_format($est->promedio_dias_prestamo, 1); ?></td>
                                   <td>
                                       <?php if ($tasa_uso > 75): ?>
                                           <span class="badge badge-danger">Alta demanda</span>
                                       <?php elseif ($tasa_uso > 25): ?>
                                           <span class="badge badge-warning">Demanda media</span>
                                       <?php else: ?>
                                           <span class="badge badge-info">Baja demanda</span>
                                       <?php endif; ?>
                                   </td>
                               </tr>
                           <?php endforeach; ?>
                       </tbody>
                   </table>
               </div>

               <h6 class="mt-3">Recomendaciones:</h6>
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
                    <!-- Tabla Detallada después del resumen -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Detalle de Publicaciones</h4>
                <div class="table-responsive">
                    <table class="table table-centered table-striped table-hover mb-0" id="datatable-buttons">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Editorial</th>
                                <th>Total Solicitudes</th>
                                <th>Préstamos Activos</th>
                                <th>Préstamos Completados</th>
                                <th>Fecha Publicación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($publicaciones as $pub): ?>
                                <tr>
                                    <td><?php echo $pub->titulo; ?></td>
                                    <td><?php echo $pub->nombreTipo; ?></td>
                                    <td><?php echo $pub->nombreEditorial; ?></td>
                                    <td>
                                        <span class="badge badge-pill badge-primary">
                                            <?php echo $pub->total_solicitudes; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-pill badge-warning">
                                            <?php echo $pub->prestamos_activos; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-pill badge-success">
                                            <?php echo $pub->prestamos_completados; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $pub->fecha_publicacion; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Historial Detallado de Préstamos</h4>
                <div class="table-responsive">
                    <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Editorial</th>
                                <th>Fecha Solicitud</th>
                                <th>Fecha Préstamo</th>
                                <th>Fecha Devolución</th>
                                <th>Lector</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($historial_prestamos as $prestamo): ?>
                                <tr>
                                    <td><?php echo $prestamo->titulo; ?></td>
                                    <td><?php echo $prestamo->nombreTipo; ?></td>
                                    <td><?php echo $prestamo->nombreEditorial; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($prestamo->fechaSolicitud)); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($prestamo->fechaPrestamo)); ?></td>
                                    <td>
                                        <?php echo $prestamo->fechaDevolucion ? 
                                            date('d/m/Y H:i', strtotime($prestamo->fechaDevolucion)) : 
                                            '-'; ?>
                                    </td>
                                    <td><?php echo $prestamo->nombres . ' ' . $prestamo->apellidoPaterno; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $prestamo->estadoPrestamo == 1 ? 'warning' : 'success'; ?>">
                                            <?php echo $prestamo->estado_texto; ?>
                                        </span>
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