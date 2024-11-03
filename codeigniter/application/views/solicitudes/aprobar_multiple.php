<!-- application/views/solicitudes/aprobar_multiple.php -->
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Aprobar Solicitud de Préstamo Múltiple</h4>
                            
                            <div class="alert alert-info">
                                <strong>Solicitante:</strong> <?php echo $solicitud->nombres . ' ' . $solicitud->apellidoPaterno; ?><br>
                                <strong>Fecha de solicitud:</strong> <?php echo date('d/m/Y H:i', strtotime($solicitud->fechaSolicitud)); ?>
                            </div>

                            <div class="table-responsive mb-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th>Editorial</th>
                                            <th>Tipo</th>
                                            <th>Ubicación</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($detalles_solicitud as $detalle): ?>
                                            <tr>
                                                <td><?php echo $detalle->titulo; ?></td>
                                                <td><?php echo $detalle->nombreEditorial; ?></td>
                                                <td><?php echo $detalle->nombreTipo; ?></td>
                                                <td><?php echo $detalle->ubicacionFisica; ?></td>
                                                <td>
                                                    <span class="badge <?php echo $detalle->estado == ESTADO_PUBLICACION_DISPONIBLE ? 'badge-success' : 'badge-danger'; ?>">
                                                        <?php echo $detalle->estado == ESTADO_PUBLICACION_DISPONIBLE ? 'Disponible' : 'No Disponible'; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if ($todas_disponibles): ?>
                                <div class="text-end">
                                    <a href="<?php echo site_url('solicitudes/rechazar/'.$solicitud->idSolicitud); ?>" 
                                       class="btn btn-danger me-2" 
                                       onclick="return confirm('¿Está seguro de rechazar esta solicitud?');">
                                        <i class="mdi mdi-close"></i> Rechazar
                                    </a>
                                    <a href="<?php echo site_url('solicitudes/aprobar/'.$solicitud->idSolicitud); ?>" 
                                       class="btn btn-success">
                                        <i class="mdi mdi-check"></i> Aprobar y Generar Préstamo
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="mdi mdi-alert"></i> No se puede aprobar la solicitud porque una o más publicaciones no están disponibles.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>