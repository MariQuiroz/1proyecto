<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Lista de Publicaciones</h4>
                            <p class="text-muted font-13 mb-4">
                                Aquí se muestran todas las publicaciones de la hemeroteca, con su estado actual.
                            </p>

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

                            <!-- Leyenda de estados -->
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="float-right">
                                            <span class="mr-3"><i class="mdi mdi-circle text-success"></i> Disponible</span>
                                            <span class="mr-3"><i class="mdi mdi-circle text-warning"></i> En consulta</span>
                                            <span class="mr-3"><i class="mdi mdi-circle text-primary"></i> En préstamo por ti</span>
                                            <span class="mr-3"><i class="mdi mdi-circle text-info"></i> En reserva</span>
                                            <span><i class="mdi mdi-circle text-danger"></i> En mantenimiento</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if ($this->session->userdata('rol') == 'administrador' || $this->session->userdata('rol') == 'encargado'): ?>
                                <a href="<?php echo site_url('publicaciones/agregar'); ?>" class="btn btn-primary mb-3">
                                    <i class="mdi mdi-plus"></i> Agregar Nueva Publicación
                                </a>
                            <?php endif; ?>

                            <!-- Resumen de publicaciones seleccionadas para lectores -->
                            <?php 
                            $publicaciones_seleccionadas = $this->session->userdata('publicaciones_seleccionadas') ?: array();
                            if ($this->session->userdata('rol') == 'lector' && !empty($publicaciones_seleccionadas)): 
                            ?>
                                <div class="alert alert-info mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="m-0">
                                            <i class="mdi mdi-information-outline mr-2"></i>
                                            Publicaciones seleccionadas: <?php echo count($publicaciones_seleccionadas); ?>/5
                                        </h5>
                                        <div>
                                            <a href="<?php echo site_url('solicitudes/crear/0'); ?>" class="btn btn-info btn-sm">
                                                <i class="mdi mdi-eye"></i> Ver seleccionadas
                                            </a>
                                            <?php if (count($publicaciones_seleccionadas) > 0): ?>
                                                <a href="<?php echo site_url('solicitudes/confirmar'); ?>" class="btn btn-success btn-sm ml-2">
                                                    <i class="mdi mdi-check"></i> Confirmar solicitud
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>Portada</th>
                                        <th>Título</th>
                                        <th>Editorial</th>
                                        <th>Tipo</th>
                                        <th>Fecha de Publicación</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($publicaciones as $publicacion): 
                                        $ya_seleccionada = in_array($publicacion->idPublicacion, $publicaciones_seleccionadas);
                                        $estado_personalizado = $this->publicacion_model->obtener_estado_personalizado(
                                            $publicacion->idPublicacion, 
                                            $this->session->userdata('idUsuario')
                                        );
                                        
                                        // Solo mostrar si no está seleccionada o no es un lector
                                        if (!($this->session->userdata('rol') == 'lector' && $ya_seleccionada)):
                                    ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($publicacion->portada) && file_exists(FCPATH . 'uploads/portadas/' . $publicacion->portada)): ?>
                                                    <img src="<?php echo base_url('uploads/portadas/' . $publicacion->portada); ?>" 
                                                         alt="Portada" 
                                                         class="img-thumbnail" 
                                                         style="max-width: 50px;">
                                                <?php else: ?>
                                                    <span class="text-muted">Sin portada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($publicacion->titulo); ?></td>
                                            <td><?php echo htmlspecialchars($publicacion->nombreEditorial); ?></td>
                                            <td><?php echo htmlspecialchars($publicacion->nombreTipo); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($publicacion->fechaPublicacion)); ?></td>
                                            <td>
                                                <?php $badge_class = get_badge_class($estado_personalizado); ?>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <i class="<?php echo get_estado_icon($estado_personalizado); ?> mr-1"></i>
                                                    <?php echo $estado_personalizado; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo site_url('publicaciones/ver/'.$publicacion->idPublicacion); ?>" 
                                                   class="btn btn-info btn-sm" 
                                                   title="Ver detalles">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>

                                                <?php if ($this->session->userdata('rol') == 'administrador' || 
                                                         $this->session->userdata('rol') == 'encargado'): ?>
                                                    
                                                    <a href="<?php echo site_url('publicaciones/modificar/'.$publicacion->idPublicacion); ?>" 
                                                       class="btn btn-primary btn-sm" 
                                                       title="Editar">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>

                                                    <?php if ($estado_personalizado == 'Disponible'): ?>
                                                        <a href="<?php echo site_url('publicaciones/eliminar/'.$publicacion->idPublicacion); ?>" 
                                                           class="btn btn-danger btn-sm" 
                                                           onclick="return confirm('¿Está seguro de eliminar esta publicación?');" 
                                                           title="Eliminar">
                                                            <i class="mdi mdi-delete"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                <?php endif; ?>

                                                <?php if ($this->session->userdata('rol') == 'lector'): ?>
                                                    <?php if ($estado_personalizado == 'Disponible'): ?>
                                                        <a href="<?php echo site_url('solicitudes/crear/'.$publicacion->idPublicacion); ?>" 
                                                           class="btn btn-success btn-sm" 
                                                           title="Solicitar préstamo">
                                                            <i class="mdi mdi-book-open-page-variant"></i> Solicitar
                                                        </a>
                                                    <?php elseif ($estado_personalizado == 'En consulta'): ?>
                                                        <?php 
                                                        $estado_interes = $this->Notificacion_model->obtener_estado_interes(
                                                            $this->session->userdata('idUsuario'), 
                                                            $publicacion->idPublicacion
                                                        );
                                                        
                                                        if ($estado_interes == ESTADO_INTERES_SOLICITADO): ?>
                                                            <button class="btn btn-secondary btn-sm" disabled>
                                                                <i class="mdi mdi-bell-check"></i> Notificación Solicitada
                                                            </button>
                                                        <?php else: ?>
                                                            <a href="<?php echo site_url('notificaciones/agregar_interes/'.$publicacion->idPublicacion); ?>" 
                                                               class="btn btn-warning btn-sm">
                                                                <i class="mdi mdi-bell"></i> Notificarme
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>