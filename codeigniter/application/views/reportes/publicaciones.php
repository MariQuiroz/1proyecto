<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <!-- Título y filtros -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Publicaciones Más Solicitadas</h4>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="get" action="<?php echo site_url('reportes/publicaciones'); ?>" class="row align-items-center">
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
                                                <?php echo $filtros['tipo'] == $tipo->idTipo ? 'selected' : ''; ?>>
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

            <!-- Estadísticas -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card widget-box-three">
                        <div class="card-body">
                            <div class="float-right mt-2">
                                <i class="fe-book text-info display-4"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-uppercase font-weight-medium text-truncate mb-2">Total Publicaciones</p>
                                <h2 class="mb-0"><?php echo $estadisticas->total_publicaciones; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card widget-box-three">
                        <div class="card-body">
                            <div class="float-right mt-2">
                                <i class="fe-file-text text-success display-4"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-uppercase font-weight-medium text-truncate mb-2">Total Solicitudes</p>
                                <h2 class="mb-0"><?php echo $estadisticas->total_solicitudes; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card widget-box-three">
                        <div class="card-body">
                            <div class="float-right mt-2">
                                <i class="fe-award text-warning display-4"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-uppercase font-weight-medium text-truncate mb-2">Publicación Más Solicitada</p>
                                <h4 class="mb-0"><?php echo $estadisticas->publicacion_mas_solicitada; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de resultados -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
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
                                                <span class="badge badge-pill badge-success">
                                                    <?php echo $pub->prestamos_activos; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-pill badge-info">
                                                    <?php echo $pub->prestamos_completados; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $pub->fecha_publicacion; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Continuación del card-body anterior -->
                        
                    </div>
                </div>
            </div>

            <!-- Gráfico de tendencias -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Tendencia de Solicitudes por Mes</h4>
                            <div class="chart-container" style="height: 300px;">
                                <canvas id="trendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para el gráfico
    var tendencias = <?php echo json_encode($tendencias); ?>;
    var labels = tendencias.map(item => {
        var fecha = new Date(item.mes + '-01');
        return fecha.toLocaleDateString('es-ES', { month: 'short', year: 'numeric' });
    });
    var datos = tendencias.map(item => item.total_solicitudes);

    // Configuración del gráfico
    var ctx = document.getElementById('trendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Solicitudes por Mes',
                data: datos,
                borderColor: '#3bafda',
                backgroundColor: 'rgba(59, 175, 218, 0.1)',
                borderWidth: 2,
                fill: true
            }]
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
            }
        }
    });
});
</script>