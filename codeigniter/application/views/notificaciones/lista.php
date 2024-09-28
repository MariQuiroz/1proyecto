<!-- application/views/notificaciones/lista.php -->
<div class="container">
    <h2>Mis Notificaciones</h2>
    <ul class="list-group">
        <?php foreach ($notificaciones as $notificacion): ?>
            <li class="list-group-item <?php echo $notificacion->leida ? 'list-group-item-secondary' : ''; ?>">
                <?php echo $notificacion->mensaje; ?>
                <small class="text-muted"><?php echo $notificacion->fechaNotificacion; ?></small>
                <?php if (!$notificacion->leida): ?>
                    <a href="<?php echo site_url('notificaciones/marcar_como_leida/'.$notificacion->idNotificacion); ?>" class="btn btn-sm btn-primary float-right">Marcar como leída</a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>