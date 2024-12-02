<div class="content-page">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Análisis de Devoluciones</h4>
                    
                    <!-- Filtros -->
                    <form method="GET" action="<?php echo site_url('reportes/devoluciones'); ?>" class="mt-3">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Fecha Inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control" 
                                           value="<?php echo $filtros['fecha_inicio'] ?? date('Y-m-d', strtotime('-1 month')); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Fecha Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control"
                                           value="<?php echo $filtros['fecha_fin'] ?? date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Estado de Devolución</label>
                                    <select name="estado_devolucion" class="form-control">
                                        <option value="">Todos</option>
                                        <option value="1" <?php echo (isset($filtros['estado_devolucion']) && $filtros['estado_devolucion'] == 1) ? 'selected' : ''; ?>>Bueno</option>
                                        <option value="2" <?php echo (isset($filtros['estado_devolucion']) && $filtros['estado_devolucion'] == 2) ? 'selected' : ''; ?>>Dañado</option>
                                        <option value="3" <?php echo (isset($filtros['estado_devolucion']) && $filtros['estado_devolucion'] == 3) ? 'selected' : ''; ?>>Perdido</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter"></i> Filtrar
                                        </button>
                                        <?php 
                                        $params = array_merge($_GET, ['export' => 'pdf']);
                                        $excel_params = array_merge($_GET, ['export' => 'excel']);
                                        ?>
                                        <a href="<?php echo site_url('reportes/devoluciones?' . http_build_query($params)); ?>" class="btn btn-danger">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                        <a href="<?php echo site_url('reportes/devoluciones?' . http_build_query($excel_params)); ?>" class="btn btn-success">
                                            <i class="fas fa-file-excel"></i> Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="card-body">
                    <?php if(empty($estadisticas)): ?>
                        <div class="alert alert-info">
                            No se encontraron datos para los filtros seleccionados
                        </div>
                    <?php else: ?>
               <!-- Indicadores clave -->
               <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5>Estado Bueno</h5>
                                        <h2><?php echo number_format($estadisticas[0]->estado_bueno ?? 0); ?></h2>
                                        <small>Total devoluciones en buen estado</small>
                                    </div>
                                </div>
                            </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>Estado Dañado</h5>
                                    <h2><?php echo number_format($estadisticas[0]->estado_dañado ?? 0); ?></h2>
                                    <small>Total devoluciones con daños</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5>Estado Perdido</h5>
                                    <h2><?php echo number_format($estadisticas[0]->estado_perdido ?? 0); ?></h2>
                                    <small>Total publicaciones perdidas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>Devoluciones Tardías</h5>
                                    <h2><?php echo number_format($estadisticas[0]->devoluciones_tardias ?? 0); ?></h2>
                                    <small>Total devoluciones fuera de tiempo</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gráfico de tendencias -->
                    <div class="row mb-4">
                            <div class="col-12">
                                <canvas id="devolucionesChart"></canvas>
                            </div>
                        </div>
                        
                        <!-- Tabla de resumen -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Resumen Mensual</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Mes/Año</th>
                                                <th class="text-right">Total</th>
                                                <th class="text-right">Bueno</th>
                                                <th class="text-right">Dañado</th>
                                                <th class="text-right">Perdido</th>
                                                <th class="text-right">Prom. Días</th>
                                                <th class="text-right">Tardías</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($estadisticas)): ?>
                                                <?php foreach ($estadisticas as $est): ?>
                                                    <tr>
                                                        <td><?php echo $est->mes . '/' . $est->anio; ?></td>
                                                        <td class="text-right"><?php echo number_format($est->total_devoluciones); ?></td>
                                                        <td class="text-right"><?php echo number_format($est->estado_bueno); ?></td>
                                                        <td class="text-right"><?php echo number_format($est->estado_dañado); ?></td>
                                                        <td class="text-right"><?php echo number_format($est->estado_perdido); ?></td>
                                                        <td class="text-right"><?php echo number_format($est->promedio_dias_prestamo, 1); ?></td>
                                                        <td class="text-right"><?php echo number_format($est->devoluciones_tardias); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No hay datos disponibles</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                  <!-- Tabla de detalle -->
                  <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Detalle de Devoluciones</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="tabla-detalle">
                                        <thead>
                                            <tr>
                                                <th>N°</th>
                                                <th>Fecha Dev.</th>
                                                <th>Lector</th>
                                                <th>Publicación</th>
                                                <th>Editorial</th>
                                                <th>Estado</th>
                                                <th>Días Préstamo</th>
                                                <th>Estado Entrega</th>
                                                <th>Encargado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($detalle_devoluciones)): ?>
                                                <?php $contador = 1; foreach ($detalle_devoluciones as $dev): ?>
                                                    <tr>
                                                        <td><?php echo $contador++; ?></td>
                                                        <td><?php echo date('d/m/Y', strtotime($dev->fechaDevolucion)); ?></td>
                                                        <td><?php echo htmlspecialchars($dev->nombres . ' ' . $dev->apellidoPaterno); ?></td>
                                                        <td><?php echo htmlspecialchars($dev->titulo); ?></td>
                                                        <td><?php echo htmlspecialchars($dev->nombreEditorial); ?></td>
                                                        <td>
                                                            <?php 
                                                            $clase = '';
                                                            $texto = '';
                                                            switch($dev->estadoDevolucion) {
                                                                case 1:
                                                                    $clase = 'badge badge-success';
                                                                    $texto = 'Bueno';
                                                                    break;
                                                                case 2:
                                                                    $clase = 'badge badge-warning';
                                                                    $texto = 'Dañado';
                                                                    break;
                                                                case 3:
                                                                    $clase = 'badge badge-danger';
                                                                    $texto = 'Perdido';
                                                                    break;
                                                            }
                                                            ?>
                                                            <span class="<?php echo $clase; ?>"><?php echo $texto; ?></span>
                                                        </td>
                                                        <td class="text-right"><?php echo $dev->dias_prestamo; ?></td>
                                                        <td>
                                                            <span class="badge <?php echo $dev->estado_entrega == 'Tardía' ? 'badge-danger' : 'badge-success'; ?>">
                                                                <?php echo htmlspecialchars($dev->estado_entrega); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($dev->nombre_encargado . ' ' . $dev->apellido_encargado); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="8" class="text-center">No se encontraron devoluciones</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Solo inicializar el gráfico si hay datos
    <?php if (!empty($estadisticas)): ?>
        var ctx = document.getElementById('devolucionesChart').getContext('2d');
        var data = {
            labels: <?php echo json_encode(array_map(function($est) { 
                return $est->mes . '/' . $est->anio;
            }, $estadisticas)); ?>,
            datasets: [{
                label: 'Estado Bueno',
                backgroundColor: 'rgba(40, 167, 69, 0.5)',
                borderColor: '#28a745',
                data: <?php echo json_encode(array_map(function($est) { 
                    return $est->estado_bueno ?? 0; 
                }, $estadisticas)); ?>
            }, {
                label: 'Estado Dañado',
                backgroundColor: 'rgba(255, 193, 7, 0.5)',
                borderColor: '#ffc107',
                data: <?php echo json_encode(array_map(function($est) { 
                    return $est->estado_dañado ?? 0; 
                }, $estadisticas)); ?>
            }, {
                label: 'Estado Perdido',
                backgroundColor: 'rgba(220, 53, 69, 0.5)',
                borderColor: '#dc3545',
                data: <?php echo json_encode(array_map(function($est) { 
                    return $est->estado_perdido ?? 0; 
                }, $estadisticas)); ?>
            }, {
                label: 'Devoluciones Tardías',
                backgroundColor: 'rgba(23, 162, 184, 0.5)',
                borderColor: '#17a2b8',
                data: <?php echo json_encode(array_map(function($est) { 
                    return $est->devoluciones_tardias ?? 0; 
                }, $estadisticas)); ?>
            }]
        };

        new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Tendencias de Devoluciones por Mes'
                    }
                }
            }
        });
    <?php endif; ?>

    // Inicializar DataTables
    $('#tabla-detalle').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[0, "desc"]],
        "pageLength": 10,
        "responsive": true,
        "dom": '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
        "buttons": [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm mr-1',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: ':visible'
                },
                customize: function(doc) {
                    doc.defaultStyle.fontSize = 8;
                    doc.styles.tableHeader.fontSize = 9;
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            }
        ]
    });
});
</script>
<script>
    $(document).ready(function() {
        // Inicializar DataTable con configuración para mantener la numeración correcta
        $('#datatable-buttons').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.22/i18n/Spanish.json"
            },
            "buttons": ["copy", "excel", "pdf"],
            // Asegurar que la numeración se mantenga correcta incluso con la paginación
            "drawCallback": function(settings) {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart;
                api.column(0, {page: 'current'}).nodes().each(function(cell, i) {
                    cell.innerHTML = startIndex + i + 1;
                });
            }
        });

        // Inicializar tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
