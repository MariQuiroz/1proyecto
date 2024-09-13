<!-- application/views/usuario/notificaciones.php -->
<div class="container mt-4">
    <h2>Mis Notificaciones</h2>
    
    <?php if (empty($notificaciones)): ?>
        <p>No tienes notificaciones en este momento.</p>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($notificaciones as $notificacion): ?>
                <a href="<?= site_url('notificaciones/ver/' . $notificacion->id) ?>" class="list-group-item list-group-item-action <?= $notificacion->leida ? '' : 'active' ?>">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?= $notificacion->titulo ?></h5>
                        <small><?= date('d/m/Y H:i', strtotime($notificacion->fecha_creacion)) ?></small>
                    </div>
                    <p class="mb-1"><?= substr($notificacion->mensaje, 0, 100) . (strlen($notificacion->mensaje) > 100 ? '...' : '') ?></p>
                    <?php if (!$notificacion->leida): ?>
                        <span class="badge badge-primary">Nueva</span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
Last edited hace 17 minutos