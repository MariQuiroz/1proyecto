<!-- Vista: application/views/solicitudes/crear.php -->
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Crear Solicitud de Préstamo</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Detalles de la Publicación</h4>
                            <?php if(isset($publicacion)): ?>
                                <div class="row">
                                    <div class="col-md-3">
                                        <?php if (!empty($publicacion->portada)): ?>
                                            <img src="<?php echo base_url('uploads/portadas/' . $publicacion->portada); ?>" alt="Portada" class="img-fluid">
                                        <?php else: ?>
                                            <div class="text-center p-3 bg-light">
                                                <span class="text-muted">Sin portada</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label>Título:</label>
                                            <p class="form-control-static"><?php echo $publicacion->titulo; ?></p>
                                        </div>
                                        <div class="form-group">
                                            <label>Editorial:</label>
                                            <p class="form-control-static"><?php echo isset($publicacion->nombreEditorial) ? $publicacion->nombreEditorial : 'No especificada'; ?></p>
                                        </div>
                                        <div class="form-group">
                                            <label>Tipo:</label>
                                            <p class="form-control-static"><?php echo isset($publicacion->nombreTipo) ? $publicacion->nombreTipo : 'No especificado'; ?></p>
                                        </div>
                                        <div class="form-group">
                                            <label>Fecha de Publicación:</label>
                                            <p class="form-control-static"><?php echo date('d/m/Y', strtotime($publicacion->fechaPublicacion)); ?></p>
                                        </div>
                                                                                <?php echo form_open('solicitudes/confirmar/' . $publicacion->idPublicacion); ?>
                                            <button type="submit" class="btn btn-primary">Confirmar Solicitud</button>
                                            <a href="<?php echo site_url('publicaciones/index'); ?>" class="btn btn-secondary">Cancelar</a>
                                        <?php echo form_close(); ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p>No se ha seleccionado ninguna publicación. Por favor, seleccione una publicación de la lista.</p>
                                <a href="<?php echo site_url('publicaciones/index'); ?>" class="btn btn-secondary">Volver a la lista de publicaciones</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>