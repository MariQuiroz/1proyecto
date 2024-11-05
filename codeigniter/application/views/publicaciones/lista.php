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
                            <h4 class="header-title">Lista de Publicaciones</h4>
                            <p class="text-muted font-13 mb-4">
                                Aquí se muestran todas las publicaciones de la hemeroteca, con su estado actual.
                            </p>

                            <?php if ($this->session->userdata('rol') == 'administrador' || $this->session->userdata('rol') == 'encargado'): ?>
                                <a href="<?php echo site_url('publicaciones/agregar'); ?>" class="btn btn-primary mb-3">
                                    <i class="mdi mdi-plus"></i> Agregar Nueva Publicación
                                </a>
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
                                    <?php foreach ($publicaciones as $publicacion): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($publicacion->portada)): ?>
                                                <img src="<?php echo base_url('uploads/portadas/' . $publicacion->portada); ?>" alt="Portada" class="img-thumbnail" style="max-width: 50px;">
                                            <?php else: ?>
                                                <span class="text-muted">Sin portada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $publicacion->titulo; ?></td>
                                        <td><?php echo $publicacion->nombreEditorial; ?></td>
                                        <td><?php echo $publicacion->nombreTipo; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($publicacion->fechaPublicacion)); ?></td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            $estado_texto = '';
                                            
                                            if ($this->session->userdata('rol') == 'lector') {
                                                $estado_personalizado = $this->publicacion_model->obtener_estado_personalizado($publicacion->idPublicacion, $this->session->userdata('idUsuario'));
                                                switch($estado_personalizado) {
                                                    case 'En préstamo por ti':
                                                        $badge_class = 'badge-primary';
                                                        $estado_texto = 'En préstamo por ti';
                                                        break;
                                                    case 'En consulta':
                                                        $badge_class = 'badge-warning';
                                                        $estado_texto = 'En consulta';
                                                        break;
                                                    default:
                                                        $estado_texto = $this->publicacion_model->obtener_nombre_estado($publicacion->estado);
                                                        switch($publicacion->estado) {
                                                            case ESTADO_PUBLICACION_DISPONIBLE:
                                                                $badge_class = 'badge-success';
                                                                break;
                                                            case ESTADO_PUBLICACION_EN_MANTENIMIENTO:
                                                                $badge_class = 'badge-danger';
                                                                break;
                                                            default:
                                                                $badge_class = 'badge-secondary';
                                                        }
                                                }
                                            } else {
                                                $estado_texto = $this->publicacion_model->obtener_nombre_estado($publicacion->estado);
                                                switch($publicacion->estado) {
                                                    case ESTADO_PUBLICACION_DISPONIBLE:
                                                        $badge_class = 'badge-success';
                                                        break;
                                                    case ESTADO_PUBLICACION_EN_CONSULTA:
                                                        $badge_class = 'badge-warning';
                                                        break;
                                                    case ESTADO_PUBLICACION_EN_MANTENIMIENTO:
                                                        $badge_class = 'badge-danger';
                                                        break;
                                                    default:
                                                        $badge_class = 'badge-secondary';
                                                }
                                            }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>"><?php echo $estado_texto; ?></span>
                                        </td>
                                        <td>
                                            <a href="<?php echo site_url('publicaciones/ver/'.$publicacion->idPublicacion); ?>" class="btn btn-info btn-sm" title="Ver detalles">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <?php if ($this->session->userdata('rol') == 'administrador' || $this->session->userdata('rol') == 'encargado'): ?>
                                                <a href="<?php echo site_url('publicaciones/modificar/'.$publicacion->idPublicacion); ?>" class="btn btn-primary btn-sm" title="Editar">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <a href="<?php echo site_url('publicaciones/eliminar/'.$publicacion->idPublicacion); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta publicación?');" title="Eliminar">
                                                    <i class="mdi mdi-delete"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($this->session->userdata('rol') == 'lector'): ?>
                                                <?php if ($estado_texto == 'Disponible'): ?>
                                                    <a href="<?php echo site_url('solicitudes/crear/'.$publicacion->idPublicacion); ?>" class="btn btn-success btn-sm" title="Solicitar préstamo">
                                                        <i class="mdi mdi-book-open-page-variant"></i> Solicitar
                                                    </a>
                                                    <?php elseif ($estado_texto != 'En préstamo por ti'): ?>
                                                    <?php 
                                                    $estado_interes = $this->Notificacion_model->obtener_estado_interes($this->session->userdata('idUsuario'), $publicacion->idPublicacion);
                                                    if ($estado_interes == ESTADO_INTERES_SOLICITADO): 
                                                    ?>
                                                        <button class="btn btn-secondary btn-sm" disabled title="Notificación solicitada">
                                                            <i class="mdi mdi-bell-check"></i> Notificación Solicitada
                                                        </button>
                                                    <?php elseif ($estado_interes == ESTADO_INTERES_NOTIFICADO): ?>
                                                        <button class="btn btn-info btn-sm" disabled title="Ya has sido notificado">
                                                            <i class="mdi mdi-bell-ring"></i> Notificado
                                                        </button>
                                                    <?php else: ?>
                                                        <a href="<?php echo site_url('notificaciones/agregar_interes/'.$publicacion->idPublicacion); ?>" class="btn btn-warning btn-sm" title="Solicitar notificación">
                                                            <i class="mdi mdi-bell"></i> Notificarme
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
