
<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid overflow-auto" style="max-height: 500px;">

            <div class="container">
                <h2 class="mb-4">Notificaciones</h2>

                <?php if (empty($notificaciones)): ?>
                    <div class="alert alert-info" role="alert">
                        No tienes notificaciones en este momento.
                    </div>
                <?php else: ?>
                    <div class="list-group">
                    <?php foreach ($notificaciones as $notificacion): ?>
    <tr>
        <td><?php echo $notificacion->idNotificacion; ?></td>
        <td><?php echo $notificacion->mensaje; ?></td>
        <td>
    <?php 
    $tipo = isset($notificacion->tipo) ? $notificacion->tipo : 0;
    switch($tipo) {
        case NOTIFICACION_SOLICITUD_PRESTAMO:
            echo 'Solicitud de Préstamo';
            break;
        case NOTIFICACION_APROBACION_PRESTAMO:
            echo 'Aprobación de Préstamo';
            break;
        case NOTIFICACION_RECHAZO_PRESTAMO:
            echo 'Rechazo de Préstamo';
            break;
        case NOTIFICACION_DEVOLUCION:
            echo 'Devolución';
            break;
        case NOTIFICACION_DISPONIBILIDAD:
            echo 'Disponibilidad';
            break;
        case NOTIFICACION_NUEVA_SOLICITUD:
            echo 'Nueva Solicitud';
            break;
        case NOTIFICACION_VENCIMIENTO:
            echo 'Vencimiento';
            break;
        default:
            echo 'Desconocido';
    }
    ?>
</td>
        <td><?php echo $notificacion->fechaEnvio; ?></td>
        <td><?php echo $notificacion->leida ? 'Sí' : 'No'; ?></td>
        <td>
            <?php if (!$notificacion->leida): ?>
                <a href="<?php echo site_url('notificaciones/marcar_leida/'.$notificacion->idNotificacion); ?>" class="btn btn-sm btn-primary">Marcar como leída</a>
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
                        
    </div> <!-- container -->
    
</div> <!-- content -->