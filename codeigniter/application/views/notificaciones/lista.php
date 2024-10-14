<div class="container">
    <h2 class="mb-4">Notificaciones</h2>

    <?php if (empty($notificaciones)): ?>
        <div class="alert alert-info" role="alert">
            No tienes notificaciones en este momento.
        </div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($notificaciones as $notificacion): ?>
                <div class="list-group-item list-group-item-action <?php echo $notificacion->leida ? 'bg-light' : ''; ?>">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <h5 class="mb-1">
                            <?php
                            $icon_class = 'fa-bell';
                            switch($notificacion->tipo) {
                                case 'solicitud_prestamo':
                                    $icon_class = 'fa-book';
                                    break;
                                case 'aprobacion_rechazo':
                                    $icon_class = 'fa-check-circle';
                                    break;
                                case 'nueva_solicitud':
                                    $icon_class = 'fa-exclamation-circle';
                                    break;
                                case 'sistema':
                                    $icon_class = 'fa-cog';
                                    break;
                            }
                            ?>
                            <i class="fas <?php echo $icon_class; ?> mr-2"></i>
                            <?php echo ucfirst($notificacion->tipo); ?>
                        </h5>
                        <small>
                            <?php 
                            if (isset($notificacion->fechaNotificacion)) {
                                echo date('d/m/Y H:i', strtotime($notificacion->fechaNotificacion));
                            } else {
                                echo "Fecha no disponible";
                            }
                            ?>
                        </small>
                    </div>
                    <p class="mb-1"><?php echo $notificacion->mensaje; ?></p>
                    <?php if (($rol == 'administrador' || $rol == 'encargado') && $notificacion->tipo == 'nueva_solicitud'): ?>
                        <a href="<?php echo site_url('solicitudes/ver/' . $notificacion->idPublicacion); ?>" class="btn btn-primary btn-sm mt-2">Ver solicitud</a>
                    <?php endif; ?>
                    <?php if (!$notificacion->leida): ?>
                        <a href="<?php echo site_url('notificaciones/marcar_leida/' . $notificacion->idNotificacion); ?>" class="btn btn-outline-secondary btn-sm mt-2">Marcar como leída</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>