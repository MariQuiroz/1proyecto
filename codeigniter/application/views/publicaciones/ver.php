<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Detalles de la Publicación</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <?php if (!empty($publicacion->portada)): ?>
                                        <img src="<?php echo base_url('uploads/portadas/' . $publicacion->portada); ?>" alt="Portada" class="img-fluid mb-3">
                                    <?php else: ?>
                                        <p>No hay portada disponible</p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-8">
                                    <p><strong>Título:</strong> <?php echo isset($publicacion->titulo) ? $publicacion->titulo : 'No disponible'; ?></p>
                                    <p><strong>Editorial:</strong> <?php echo isset($publicacion->nombreEditorial) ? $publicacion->nombreEditorial : 'No disponible'; ?></p>
                                    <p><strong>Tipo:</strong> <?php echo isset($publicacion->nombreTipo) ? $publicacion->nombreTipo : 'No disponible'; ?></p>
                                    <p><strong>Fecha de Publicación:</strong> <?php echo isset($publicacion->fechaPublicacion) ? $publicacion->fechaPublicacion : 'No disponible'; ?></p>
                                    <p><strong>Número de Páginas:</strong> <?php echo isset($publicacion->numeroPaginas) ? $publicacion->numeroPaginas : 'No disponible'; ?></p>
                                    <p><strong>Ubicación Física:</strong> <?php echo isset($publicacion->ubicacionFisica) ? $publicacion->ubicacionFisica : 'No disponible'; ?></p>
                                    <p><strong>Estado:</strong> <?php echo isset($publicacion->estado) ? $this->publicacion_model->obtener_nombre_estado($publicacion->estado) : 'No disponible'; ?></p>
                                    
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>Descripción:</h5>
                                    <p><?php echo isset($publicacion->descripcion) ? $publicacion->descripcion : 'No disponible'; ?></p>
                                </div>
                            </div>
                            <?php if ($this->session->userdata('rol') == 'administrador' || $this->session->userdata('rol') == 'encargado'): ?>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <a href="<?php echo site_url('publicaciones/modificar/'.$publicacion->idPublicacion); ?>" class="btn btn-primary">Editar</a>
                                        <a href="<?php echo site_url('publicaciones/eliminar/'.$publicacion->idPublicacion); ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar esta publicación?');">Eliminar</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('rol') == 'lector'): ?>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <?php if ($publicacion->estado == ESTADO_PUBLICACION_DISPONIBLE): ?>
                                            <a href="<?php echo site_url('solicitudes/crear/'.$publicacion->idPublicacion); ?>" class="btn btn-success">Solicitar Préstamo</a>
                                        <?php elseif (!isset($esta_interesado) || !$esta_interesado): ?>
                                            <a href="<?php echo site_url('publicaciones/solicitar_notificacion/'.$publicacion->idPublicacion); ?>" class="btn btn-info">Notificarme cuando esté disponible</a>
                                        <?php else: ?>
                                            <p class="text-info">Recibirás una notificación cuando esta publicación esté disponible.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <a href="<?php echo site_url('publicaciones/index'); ?>" class="btn btn-secondary">Volver al catálogo</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- container -->
    </div> <!-- content -->
</div>

<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->