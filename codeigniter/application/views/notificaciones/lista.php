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
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($notificacion->mensaje); ?></strong>
                                        <small class="text-muted d-block"><?php echo date('d/m/Y H:i', strtotime($notificacion->fechaEnvio)); ?></small>
                                        <span class="badge bg-<?php echo $notificacion->leida ? 'success' : 'warning'; ?>">
                                            <?php echo $notificacion->leida ? 'Leída' : 'No leída'; ?>
                                        </span>
                                    </div>
                                    <div>
                                        <?php if (!$notificacion->leida): ?>
                                            <a href="<?php echo site_url('notificaciones/marcar_leida/'.$notificacion->idNotificacion); ?>" class="btn btn-sm btn-primary">Marcar como leída</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div> <!-- container -->
    </div> <!-- content -->
</div>
