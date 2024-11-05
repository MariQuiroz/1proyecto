<div class="container-fluid"> 
    <h2>Notificaciones</h2>
    
    <?php if (empty($notificaciones)): ?>
        <div class="alert alert-info" role="alert">
            No tienes notificaciones en este momento.
        </div>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($notificaciones as $notificacion): ?>
                <li class="list-group-item">
                    <?php echo htmlspecialchars($notificacion->mensaje); ?>
                    <small class="text-muted float-end"><?php echo date('d/m/Y H:i', strtotime($notificacion->fechaNotificacion)); ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
