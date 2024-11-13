<!-- views/reportes/prestamos.php -->
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <!-- Título y descripción -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo site_url('usuarios/panel'); ?>">Inicio</a></li>
                                <li class="breadcrumb-item active">Reportes de Préstamos</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Reporte de Préstamos</h4>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="get" action="<?php echo site_url('reportes/prestamos'); ?>" class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha Inicio</label>
                                        <input type="date" name="fecha_inicio" class="form-control" 
                                               value="<?php echo $filtros['fecha_inicio']; ?>"
                                               max="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha Fin</label>
                                        <input type="date" name="fecha_fin" class="form-control" 
                                               value="<?php echo $filtros['fecha_fin']; ?>"
                                               max="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select name="estado" class="form-control">
                                            <option value="">Todos</option>
                                            <?php foreach($estados_prestamo as $key => $value): ?>
                                                <option value="<?php echo $key; ?>" 
                                                    <?php echo $filtros['estado'] == $key ? 'selected' : ''; ?>>
                                                    <?php echo $value; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Encargado</label>
                                        <select name="id_encargado" class="form-control">
                                            <option value="">Todos</option>
                                            <?php foreach($encargados as $encargado): ?>
                                                <option value="<?php echo $encargado->idUsuario; ?>"
                                                    <?php echo $filtros['id_encargado'] == $encargado->idUsuario ? 'selected' : ''; ?>>
                                                    <?php echo $encargado->nombres . ' ' . $encargado->apellidoPaterno; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fe-filter mr-1"></i> Filtrar
                                        </button>
                                        <a href="<?php echo site_url('reportes/exportar_prestamos').'?'.http_build_query($filtros); ?>" 
                                           class="btn btn-success">
                                            <i class="fe-download mr-1"></i> Exportar
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas en Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card widget-box-three">
                        <div class="card-body">
                            <div class="float-right mt-2">
                                <i class="fe-book text-success display-4"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-uppercase font-weight-medium text-truncate mb-2">Préstamos Activos</p>
                                <h2 class="mb-0 mt-0">
                                    <span data-plugin="counterup"><?php echo $estadisticas->activos; ?></span>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card widget-box-three">
                        <div class="card-body">
                            <div class="float-right mt-2">
                                <i class="fe-check-circle text-primary display-4"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-uppercase font-weight-medium text-truncate mb-2">Préstamos Devueltos</p>
                                <h2 class="mb-0 mt-0">
                                    <span data-plugin="counterup"><?php echo $estadisticas->devueltos; ?></span>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card widget-box-three">
                        <div class="card-body">
                            <div class="float-right mt-2">
                                <i class="fe-alert-triangle text-danger display-4"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-uppercase font-weight-medium text-truncate mb-2">Préstamos Vencidos</p>
                                <h2 class="mb-0 mt-0">
                                    <span data-plugin="counterup"><?php echo $estadisticas->vencidos; ?></span>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Estado de Préstamos</h4>
                            <div class="mt-3">
                                <canvas id="estadoPrestamosChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Tendencia Mensual de Préstamos</h4>
                            <div class="mt-3">
                                <canvas id="tendenciaMensualChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Resultados -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4">Lista Detallada de Préstamos</h4>

                            <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th>Publicación</th>
                                        <th>Estado</th>
                                        <th>Encargado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($prestamos as $prestamo): ?>
                                        <tr>
                                            <td><?php echo $prestamo->idPrestamo; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($prestamo->fechaPrestamo)); ?></td>
                                            <td><?php echo htmlspecialchars($prestamo->nombres.' '.$prestamo->apellidoPaterno); ?></td>
                                            <td><?php echo htmlspecialchars($prestamo->titulo); ?></td>
                                            <td>
                                                <span class="badge badge-<?php 
                                                    echo $prestamo->estado_prestamo == 'Activo' ? 'success' : 
                                                        ($prestamo->estado_prestamo == 'Devuelto' ? 'primary' : 'danger'); 
                                                ?>">
                                                    <?php echo $prestamo->estado_prestamo; ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($prestamo->nombre_encargado.' '.$prestamo->apellido_encargado); ?></td>
                                            <td>
                                                <a href="<?php echo site_url('prestamos/detalle/'.$prestamo->idPrestamo); ?>" 
                                                   class="btn btn-sm btn-info" data-toggle="tooltip" title="Ver Detalle">
                                                    <i class="fe-eye"></i>
                                                </a>
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

<!-- Scripts para los gráficos -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuración de los gráficos
    const configGraficoPastel = {
        type: 'doughnut',
        data: {
            labels: ['Activos', 'Devueltos', 'Vencidos'],
            datasets: [{
                data: [
                    <?php echo $estadisticas->activos; ?>,
                    <?php echo $estadisticas->devueltos; ?>,
                    <?php echo $estadisticas->vencidos; ?>
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 99, 132, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: {
                    display: true,
                    text: 'Distribución de Estados de Préstamos'
                }
            }
        }
    };

    const configGraficoLineas = {
        type: 'line',
        data: {
            labels: [<?php echo "'" . implode("','", array_map(function($stat) {
                return date('M Y', strtotime($stat->año . '-' . $stat->mes . '-01'));
            }, $estadisticas_mensuales)) . "'"; ?>],
            datasets: [{
                label: 'Activos',
                data: [<?php echo implode(',', array_column($estadisticas_mensuales, 'activos')); ?>],
                borderColor: 'rgba(54, 162, 235, 1)',
                fill: false
            }, {
                label: 'Devueltos',
                data: [<?php echo implode(',', array_column($estadisticas_mensuales, 'devueltos')); ?>],
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: false
            }, {
                label: 'Vencidos',
                data: [<?php echo implode(',', array_column($estadisticas_mensuales, 'vencidos')); ?>],
                borderColor: 'rgba(255, 99, 132, 1)',
                fill: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: {
                    display: true,
                    text: 'Tendencia de Préstamos en los Últimos 6 Meses'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    };

    // Inicializar gráficos
    new Chart(document.getElementById('estadoPrestamosChart').getContext('2d'), configGraficoPastel);
    new Chart(document.getElementById('tendenciaMensualChart').getContext('2d'), configGraficoLineas);

    // Inicializar DataTables
    $('#datatable-buttons').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        }
    });
});
</script>