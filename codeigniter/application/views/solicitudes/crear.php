<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Confirmar Solicitud de Préstamo</h4>
                            
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline mr-2"></i>
                                Verifique los detalles de la publicación antes de confirmar la solicitud.
                            </div>
                            
                            <!-- Detalles de la publicación -->
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Portada</th>
                                            <th>Título</th>
                                            <th>Editorial</th>
                                            <th>Tipo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <?php if (!empty($publicacion->portada)): ?>
                                                    <img src="<?php echo base_url('uploads/portadas/' . $publicacion->portada); ?>" 
                                                         alt="Portada" class="img-thumbnail" style="max-width: 50px;">
                                                <?php else: ?>
                                                    <span class="text-muted">Sin portada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $publicacion->titulo; ?></td>
                                            <td><?php echo $publicacion->nombreEditorial; ?></td>
                                            <td><?php echo $publicacion->nombreTipo; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Botones de acción -->
                            <div class="mt-3">
                                <a href="<?php echo site_url('solicitudes/confirmar/' . $publicacion->idPublicacion); ?>" class="btn btn-primary">
                                    <i class="mdi mdi-check-circle mr-1"></i>Confirmar Solicitud
                                </a>
                                
                                <a href="<?php echo site_url('publicaciones'); ?>" class="btn btn-info">
                                    <i class="mdi mdi-plus mr-1"></i>Añadir Más Publicaciones
                                </a>

                                <a href="<?php echo site_url('publicaciones'); ?>" class="btn btn-secondary">
                                    <i class="mdi mdi-close-circle mr-1"></i>Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>