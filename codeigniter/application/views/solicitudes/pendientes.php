<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Solicitudes Pendientes</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Gestión de Solicitudes Pendientes</h4>
                            <p class="text-muted font-13 mb-4">
                                Aquí puedes ver y gestionar todas las solicitudes pendientes de préstamo de publicaciones.
                            </p>

                            <div class="table-responsive">
                                <table id="basic-datatable" class="table dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre Usuario</th>
                                            <th>Carnet</th>
                                            <th>Título Publicación</th>
                                            <th>Ubicación</th>
                                            <th>Fecha Solicitud</th>
                                            <th>Tiempo Restante</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($solicitudes as $solicitud): 
                                            $tiempo_expiracion = strtotime($solicitud->fechaExpiracionReserva);
                                            $tiempo_actual = time();
                                            $tiempo_restante = $tiempo_expiracion - $tiempo_actual;
                                            $solicitud_activa = ($tiempo_restante > 0 && $solicitud->estadoSolicitud == ESTADO_SOLICITUD_PENDIENTE);
                                        ?>
                                        <tr>
                                            <td><?php echo $solicitud->idSolicitud; ?></td>
                                            
                                            <td>
                                                <strong><?php echo htmlspecialchars($solicitud->nombres . ' ' . $solicitud->apellidoPaterno); ?></strong>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($solicitud->carnet); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($solicitud->titulo); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($solicitud->ubicacionFisica); ?>
                                            </td>

                                            <td>
                                                <?php echo date('d/m/Y H:i', strtotime($solicitud->fechaSolicitud)); ?>
                                            </td>

                                            <td class="text-center">
                                                <?php if ($solicitud->estadoSolicitud == ESTADO_SOLICITUD_PENDIENTE): ?>
                                                    <?php if ($tiempo_restante > 0): ?>
                                                        <span class="badge badge-warning">
                                                            <?php 
                                                            $minutos = floor($tiempo_restante / 60);
                                                            $segundos = $tiempo_restante % 60;
                                                            echo sprintf('%02d:%02d', $minutos, $segundos);
                                                            ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Expirado</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-center">
                                                <?php
                                                switch($solicitud->estadoSolicitud) {
                                                    case ESTADO_SOLICITUD_PENDIENTE:
                                                        if ($tiempo_restante > 0) {
                                                            echo '<span class="badge badge-warning">Pendiente</span>';
                                                        } else {
                                                            echo '<span class="badge badge-secondary">Por Expirar</span>';
                                                        }
                                                        break;
                                                    case ESTADO_SOLICITUD_EXPIRADA:
                                                        echo '<span class="badge badge-secondary">Expirada</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge badge-secondary">Desconocido</span>';
                                                }
                                                ?>
                                            </td>

                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <?php if ($solicitud_activa): ?>
                                                        <a href="<?php echo site_url('solicitudes/aprobar/' . $solicitud->idSolicitud); ?>" 
                                                           class="btn btn-success btn-sm" title="Aprobar">
                                                            <i class="mdi mdi-check"></i>
                                                        </a>
                                                        <a href="<?php echo site_url('solicitudes/rechazar/' . $solicitud->idSolicitud); ?>" 
                                                           class="btn btn-danger btn-sm"
                                                           onclick="return confirm('¿Está seguro de rechazar esta solicitud?');"
                                                           title="Rechazar">
                                                            <i class="mdi mdi-close"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="<?php echo site_url('solicitudes/detalle/' . $solicitud->idSolicitud); ?>" 
                                                       class="btn btn-info btn-sm"
                                                       title="Ver Detalles">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Leyenda de estados y tiempos -->
                            <div class="mt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Estados:</h5>
                                        <span class="badge badge-warning mr-2">Pendiente</span>
                                        <span class="badge badge-secondary mr-2">Por Expirar</span>
                                        <span class="badge badge-secondary">Expirada</span>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Información:</h5>
                                        <p class="text-muted mb-0">
                                            <small>• Las solicitudes expiran después de <?php echo $tiempo_limite; ?> minutos</small><br>
                                            <small>• Las solicitudes expiradas no pueden ser aprobadas</small>
                                        </p>
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

<?php if ($this->input->get('pdf')): ?>
<script>
    window.open('<?php echo $this->input->get('pdf'); ?>', '_blank');
</script>
<?php endif; ?>