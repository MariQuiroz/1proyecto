<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <!-- Título y filtros -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Usuarios Activos y Frecuencia de Uso</h4>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="get" action="<?php echo site_url('reportes/usuarios'); ?>" class="row align-items-center">
                                <div class="col-md-3">
                                    <label>Fecha Inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $filtros['fecha_inicio']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label>Fecha Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control" value="<?php echo $filtros['fecha_fin']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label>Mínimo de Préstamos</label>
                                    <input type="number" name="min_prestamos" class="form-control" min="0" value="<?php echo $filtros['min_prestamos']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary mt-3">Filtrar</button>
                                    <a href="<?php echo site_url('reportes/exportar_usuarios').'?'.http_build_query($filtros); ?>" 
                                       class="btn btn-success mt-3">Exportar Excel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card widget-box-three">
                        <div class="card-body">
                            <div class="float-right mt-2">
                                <i class="fe-users text-info display-4"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-uppercase font-weight-medium text-truncate mb-2">Total Usuarios Activos</p>
                                <h2 class="mb-0"><?php echo $estadisticas->total_usuarios; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card widget-box-three">
                        <div class="card-body">
                            <div class="float-right mt-2">
                                <i class="fe-activity text-success display-4"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-uppercase font-weight-medium text-truncate mb-2">Promedio Préstamos/Usuario</p>
                                <h2 class="mb-0"><?php echo $estadisticas->promedio_prestamos; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card widget-box-three">
                        <div class="card-body">
                            <div class="float-right mt-2">
                                <i class="fe-award text-warning display-4"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-uppercase font-weight-medium text-truncate mb-2">Máximo Préstamos/Usuario</p>
                                <h2 class="mb-0"><?php echo $estadisticas->max_prestamos; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de actividad mensual -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Actividad Mensual de Usuarios</h4>
                            <div class="chart-container" style="height: 300px;">
                                <canvas id="activityChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de usuarios -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Profesión</th>
                                        <th>Total Solicitudes</th>
                                        <th>Préstamos Activos</th>
                                        <th>Préstamos Completados</th>
                                        <th>Última Actividad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($usuarios as $usuario): ?>
                                        <tr>
                                            <td><?php echo $usuario->nombres . ' ' . $usuario->apellidoPaterno; ?></td>
                                            <td><?php echo $usuario->profesion; ?></td>
                                            <td>
                                                <span class="badge badge-pill badge-primary">
                                                    <?php echo $usuario->total_solicitudes; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-pill badge-success">
                                                    <?php echo $usuario->prestamos_activos; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-pill badge-info">
                                                    <?php echo $usuario->prestamos_completados; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo date('d/m/Y', strtotime($usuario->ultima_actividad)); ?>
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

<!-- Script para el gráfico -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para el gráfico de actividad
    var actividadMensual = <?php echo json_encode($actividad_mensual); ?>;
    var labels = actividadMensual.map(item => {
        var fecha = new Date(item.mes + '-01');
        return fecha.toLocaleDateString('es-ES', { month: 'short', year: 'numeric' });
    });
    
    // Configuración del gráfico
    var ctx = document.getElementById('activityChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Usuarios Activos',
                    data: actividadMensual.map(item => item.usuarios_activos),
                    backgroundColor: 'rgba(59, 175, 218, 0.5)',
                    borderColor: '#3bafda',
                    borderWidth: 1
                },
                {
                    label: 'Total Solicitudes',
                    data: actividadMensual.map(item => item.total_solicitudes),
                    backgroundColor: 'rgba(28, 132, 198, 0.5)',
                    borderColor: '#1c84c6',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                    position: 'top'
                }
            }
        }
    });

    // Inicializar DataTables con botones de exportación
    $('#datatable-buttons').DataTable({
        lengthChange: false,
        buttons: ['copy', 'excel', 'pdf', 'colvis'],
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        }
    });
});
</script>