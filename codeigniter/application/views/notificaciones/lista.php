<div class="content-page">
    <div class="content">
        <div class="container-fluid overflow-auto" style="max-height: 500px;">
            <div class="container">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="float-left">Notificaciones</h2>
                        <div class="float-right">
                            <?php if (!empty($notificaciones)): ?>
                                <a href="<?php echo site_url('notificaciones/marcar_todas_leidas'); ?>" 
                                   class="btn btn-primary mr-2">
                                    <i class="fe-check-circle"></i> Marcar todas como leídas
                                </a>
                                <a href="<?php echo site_url('notificaciones/eliminar_leidas'); ?>" 
                                   class="btn btn-danger"
                                   onclick="return confirm('¿Está seguro de que desea eliminar todas las notificaciones leídas?');">
                                    <i class="fe-trash-2"></i> Eliminar leídas
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if ($this->session->flashdata('mensaje')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $this->session->flashdata('mensaje'); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $this->session->flashdata('error'); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (empty($notificaciones)): ?>
                    <div class="alert alert-info" role="alert">
                        No tienes notificaciones en este momento.
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($notificaciones as $notificacion): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <strong><?php echo htmlspecialchars($notificacion->mensaje); ?></strong>
                                        <small class="text-muted d-block">
                                            <?php echo date('d/m/Y H:i', strtotime($notificacion->fechaEnvio)); ?>
                                        </small>
                                        <span class="badge bg-<?php echo $notificacion->leida ? 'success' : 'warning'; ?>">
                                            <?php echo $notificacion->leida ? 'Leída' : 'No leída'; ?>
                                        </span>
                                    </div>
                                    <div class="btn-group">
                                        <?php if (!$notificacion->leida): ?>
                                            <a href="<?php echo site_url('notificaciones/marcar_leida/'.$notificacion->idNotificacion); ?>" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fe-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo site_url('notificaciones/eliminar/'.$notificacion->idNotificacion); ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('¿Está seguro de que desea eliminar esta notificación?');">
                                            <i class="fe-trash-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>