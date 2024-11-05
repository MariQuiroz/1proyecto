<!-- application/views/usuario/notificaciones.php -->
<div class="container-fluid"> 
    <h2>Mis Notificaciones</h2>
    
    <?php if (empty($notificaciones)): ?>
        <div class="alert alert-info" role="alert">
            No tienes notificaciones en este momento.
        </div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($notificaciones as $notificacion): ?>
                <a href="<?= site_url('notificaciones/ver/' . $notificacion->id) ?>" class="list-group-item list-group-item-action <?= $notificacion->leida ? '' : 'active' ?>" aria-describedby="notificacion-<?= $notificacion->id ?>">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?= htmlspecialchars($notificacion->titulo) ?></h5>
                        <small><?= date('d/m/Y H:i', strtotime($notificacion->fecha_creacion)) ?></small>
                    </div>
                    <p class="mb-1"><?= htmlspecialchars(substr($notificacion->mensaje, 0, 100)) . (strlen($notificacion->mensaje) > 100 ? '...' : '') ?></p>
                    <?php if (!$notificacion->leida): ?>
                        <span class="badge bg-primary">Nueva</span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
