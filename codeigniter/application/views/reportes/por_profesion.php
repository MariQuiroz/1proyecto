<div class="content-page">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Análisis por Profesión de Lectores</h4>
                    
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
                    <!-- Gráfico -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <canvas id="profesionesChart"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Profesión</th>
                                            <th>Total Préstamos</th>
                                            <th>Prom. Días</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($estadisticas as $est): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($est->profesion); ?></td>
                                            <td class="text-right"><?php echo $est->total_prestamos; ?></td>
                                            <td class="text-right"><?php echo $est->promedio_dias_prestamo; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Análisis de Datos -->
                    <div class="row">
                        <div class="col-12">
                            <h5>Análisis de Datos</h5>
                            <?php
                             $max_prestamos = max(array_column($estadisticas, 'total_prestamos'));
                             $min_prestamos = min(array_column($estadisticas, 'total_prestamos'));
                             $prom_dias = array_sum(array_column($estadisticas, 'promedio_dias_prestamo')) / count($estadisticas);
                             ?>
                             <div class="alert alert-info">
                                 <p><strong>Hallazgos principales:</strong></p>
                                 <ul>
                                     <li>La profesión con más préstamos registra <?php echo $max_prestamos; ?> solicitudes.</li>
                                     <li>El promedio general de días de préstamo es de <?php echo number_format($prom_dias, 1); ?> días.</li>
                                     <li>Se observa una diferencia de <?php echo $max_prestamos - $min_prestamos; ?> préstamos entre la profesión más y menos activa.</li>
                                 </ul>
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
     var ctx = document.getElementById('profesionesChart').getContext('2d');
     
     var data = {
         labels: <?php echo json_encode(array_column($estadisticas, 'profesion')); ?>,
         datasets: [{
             label: 'Total Préstamos',
             data: <?php echo json_encode(array_column($estadisticas, 'total_prestamos')); ?>,
             backgroundColor: 'rgba(54, 162, 235, 0.5)',
             borderColor: 'rgba(54, 162, 235, 1)',
             borderWidth: 1
         },
         {
             label: 'Promedio Días',
             data: <?php echo json_encode(array_column($estadisticas, 'promedio_dias_prestamo')); ?>,
             backgroundColor: 'rgba(255, 99, 132, 0.5)',
             borderColor: 'rgba(255, 99, 132, 1)',
             borderWidth: 1
         }]
     };
 
     new Chart(ctx, {
         type: 'bar',
         data: data,
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