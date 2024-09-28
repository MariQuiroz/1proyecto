<div class="container">
    <h2>Notificaciones</h2>
    <ul class="list-group">
        <?php foreach ($notificaciones as $notificacion): ?>
            <li class="list-group-item">
                <?php echo $notificacion->mensaje; ?>
                <small class="text-muted"><?php echo $notificacion->fechaNotificacion; ?></small>
            </li>
        <?php endforeach; ?>
    </ul>
</div>