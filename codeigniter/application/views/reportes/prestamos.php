<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <!-- Título y filtros -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
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
                                    <label>Fecha Inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $filtros['fecha_inicio']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label>Fecha Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control" value="<?php echo $filtros['fecha_fin']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label>Estado</label>
                                    <select name="estado" class="form-control">
                                        <option value="">Todos</option>
                                        <option value="activo" <?php echo $filtros['estado'] == 'activo' ? 'selected' : ''; ?>>Activos</option>
                                        <option value="devuelto" <?php echo $filtros['estado'] == 'devuelto' ? 'selected' : ''; ?>>Devueltos</option>
                                        <option value="vencido" <?php echo $filtros['estado'] == 'vencido' ? 'selected' : ''; ?>>Vencidos</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary mt-3">Filtrar</button>
                                    <a href="<?php echo site_url('reportes/exportar_prestamos').'?'.http_build_query($filtros); ?>" 
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
                                <i class="fe-book-open text-success display-4"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-uppercase font-weight-medium text-truncate mb-2">Préstamos Activos</p>
                                <h2 class="mb-0"><?php echo $estadisticas->activos; ?></h2>
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
                                <h2 class="mb-0"><?php echo $estadisticas->devueltos; ?></h2>
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
                                <h2 class="mb-0"><?php echo $estadisticas->vencidos; ?></h2>
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
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th>Publicación</th>
                                        <th>Estado</th>
                                        <th>Encargado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($prestamos as $prestamo): ?>
                                        <tr>
                                            <td><?php echo $prestamo->idPrestamo; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($prestamo->fechaPrestamo)); ?></td>
                                            <td><?php echo $prestamo->nombres.' '.$prestamo->apellidoPaterno; ?></td>
                                            <td><?php echo $prestamo->titulo; ?></td>
                                            <td>
                                                <span class="badge badge-<?php 
                                                    echo $prestamo->estado_prestamo == 'Activo' ? 'success' : 
                                                        ($prestamo->estado_prestamo == 'Devuelto' ? 'primary' : 'danger'); 
                                                ?>">
                                                    <?php echo $prestamo->estado_prestamo; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $prestamo->nombre_encargado.' '.$prestamo->apellido_encargado; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>