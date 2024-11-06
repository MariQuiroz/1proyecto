<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Solicitud de Préstamo</h4>
                            
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
                            
                             <!-- Mensaje informativo -->
                             <div class="alert alert-info">
                                <i class="mdi mdi-information-outline mr-2"></i>
                                Puede seleccionar hasta 5 publicaciones para su solicitud.
                            </div>
                            
                            <!-- Publicaciones seleccionadas -->
                            <?php if (!empty($publicaciones)): ?>
                                <div class="row mb-4">
                                    <?php foreach ($publicaciones as $publicacion): ?>
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100">
                                                <?php if ($publicacion->portada && file_exists(FCPATH . 'uploads/portadas/' . $publicacion->portada)): ?>
                                                    <img src="<?php echo base_url('uploads/portadas/' . $publicacion->portada); ?>" 
                                                         class="card-img-top" 
                                                         alt="Portada de <?php echo htmlspecialchars($publicacion->titulo); ?>"
                                                         style="height: 250px; object-fit: cover;">
                                                <?php else: ?>
                                                    <img src="<?php echo base_url('assets/images/no-portada.jpg'); ?>" 
                                                         class="card-img-top" 
                                                         alt="Sin portada"
                                                         style="height: 250px; object-fit: cover;">
                                                <?php endif; ?>
                                                
                                                <div class="card-body">
                                                    <h5 class="card-title"><?php echo htmlspecialchars($publicacion->titulo); ?></h5>
                                                    <p class="card-text">
                                                        <strong>Editorial:</strong> <?php echo htmlspecialchars($publicacion->nombreEditorial); ?><br>
                                                        <strong>Tipo:</strong> <?php echo htmlspecialchars($publicacion->nombreTipo); ?><br>
                                                        <strong>Ubicación:</strong> <?php echo htmlspecialchars($publicacion->ubicacionFisica); ?>
                                                    </p>
                                                    <p class="card-text">
                                                        <small class="text-muted">
                                                            Fecha de publicación: <?php echo date('d/m/Y', strtotime($publicacion->fechaPublicacion)); ?>
                                                        </small>
                                                    </p>
                                                </div>
                                                <div class="card-footer text-center">
                                                    <a href="<?php echo site_url('solicitudes/remover/' . $publicacion->idPublicacion); ?>" 
                                                       class="btn btn-danger btn-sm">
                                                        <i class="fe-trash-2 mr-1"></i> Remover de la solicitud
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Botones de acción -->
                                <div class="mt-4 text-center">
                                    <?php if (count($publicaciones) < 5): ?>
                                        <a href="<?php echo site_url('publicaciones'); ?>" class="btn btn-info btn-lg">
                                            <i class="mdi mdi-plus mr-1"></i>Añadir Más Publicaciones
                                        </a>
                                    <?php endif; ?>

                                    <a href="<?php echo site_url('solicitudes/confirmar'); ?>" class="btn btn-primary btn-lg">
                                        <i class="mdi mdi-check-circle mr-1"></i>Confirmar Solicitud
                                    </a>

                                    <a href="<?php echo site_url('solicitudes/cancelar'); ?>" class="btn btn-secondary btn-lg">
                                        <i class="mdi mdi-close-circle mr-1"></i>Cancelar
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="mdi mdi-alert mr-2"></i>
                                    No hay publicaciones seleccionadas. 
                                    <a href="<?php echo site_url('publicaciones'); ?>" class="alert-link">
                                        Seleccione algunas publicaciones
                                    </a>.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>