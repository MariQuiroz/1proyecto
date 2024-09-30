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
                                Aquí se muestran todas las publicaciones disponibles en la hemeroteca.
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
                                            <span class="badge <?php echo $publicacion->estado == ESTADO_PUBLICACION_DISPONIBLE ? 'badge-success' : 'badge-warning'; ?>">
                                                <?php echo $this->publicacion_model->obtener_nombre_estado($publicacion->estado); ?>
                                            </span>
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
                                            <?php if ($this->session->userdata('rol') == 'lector' && $publicacion->estado == ESTADO_PUBLICACION_DISPONIBLE): ?>
                                                <a href="<?php echo site_url('solicitudes/crear/'.$publicacion->idPublicacion); ?>" class="btn btn-success btn-sm" title="Solicitar préstamo">
                                                    <i class="mdi mdi-book-open-page-variant"></i> Solicitar
                                                </a>
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