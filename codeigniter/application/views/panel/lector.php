<!-- application/views/panel/lector.php -->
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">Mis Préstamos Activos</h4>
                <div class="widget-chart-1">
                    <div class="widget-chart-box-1 float-left" dir="ltr">
                        <input data-plugin="knob" data-width="80" data-height="80" data-fgColor="#3db9dc"
                               data-bgColor="#C3E8F5" value="<?php echo $mis_prestamos_activos; ?>"
                               data-skin="tron" data-angleOffset="180" data-readOnly=true
                               data-thickness=".15"/>
                    </div>
                    <div class="widget-detail-1 text-right">
                        <h2 class="font-weight-normal pt-2 mb-1"> <?php echo $mis_prestamos_activos; ?> </h2>
                        <p class="text-muted mb-1">Préstamos Activos</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">Mis Solicitudes Pendientes</h4>
                <div class="widget-chart-1">
                    <div class="widget-chart-box-1 float-left" dir="ltr">
                        <input data-plugin="knob" data-width="80" data-height="80" data-fgColor="#f05050"
                               data-bgColor="#F9B9B9" value="<?php echo $mis_solicitudes_pendientes; ?>"
                               data-skin="tron" data-angleOffset="180" data-readOnly=true
                               data-thickness=".15"/>
                    </div>
                    <div class="widget-detail-1 text-right">
                        <h2 class="font-weight-normal pt-2 mb-1"> <?php echo $mis_solicitudes_pendientes; ?> </h2>
                        <p class="text-muted mb-1">Solicitudes Pendientes</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">Acciones Rápidas</h4>
                <div class="widget-chart-1">
                    <div class="row mt-3">
                        <div class="col-6 mb-2">
                            <a href="<?php echo site_url('publicaciones/buscar'); ?>" class="btn btn-primary btn-block">Buscar Publicaciones</a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="<?php echo site_url('solicitudes/nueva'); ?>" class="btn btn-success btn-block">Nueva Solicitud</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-12">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">Mis Próximas Devoluciones</h4>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Fecha de Devolución</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mis_proximas_devoluciones as $devolucion): ?>
                            <tr>
                                <td><?php echo $devolucion->titulo; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($devolucion->fecha_devolucion)); ?></td>
                                <td>
                                    <?php
                                    $hoy = new DateTime();
                                    $fecha_devolucion = new DateTime($devolucion->fecha_devolucion);
                                    $diff = $hoy->diff($fecha_devolucion);
                                    if ($hoy > $fecha_devolucion) {
                                        echo '<span class="badge badge-danger">Vencido</span>';
                                    } elseif ($diff->days <= 3) {
                                        echo '<span class="badge badge-warning">Próximo</span>';
                                    } else {
                                        echo '<span class="badge badge-success">En tiempo</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
</div>