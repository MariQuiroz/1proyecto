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

                <?php if (!$es_lector): ?>
                <a href="<?php echo site_url('publicaciones/agregar'); ?>" class="btn btn-primary mb-3">Agregar Nueva Publicación</a>
                <?php endif; ?>

                <table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
                    <thead>
                        <tr>
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
                            <td><?php echo $publicacion->titulo; ?></td>
                            <td><?php echo $publicacion->nombreEditorial; ?></td>
                            <td><?php echo $publicacion->nombreTipo; ?></td>
                            <td><?php echo $publicacion->fechaPublicacion; ?></td>
                            <td><?php echo $this->publicacion_model->obtener_nombre_estado($publicacion->estado); ?></td>
                            <td>
                                <a href="<?php echo site_url('publicaciones/ver/'.$publicacion->idPublicacion); ?>" class="btn btn-info btn-sm">Ver</a>
                                <?php if (!$es_lector): ?>
                                    <a href="<?php echo site_url('publicaciones/editar/'.$publicacion->idPublicacion); ?>" class="btn btn-primary btn-sm">Editar</a>
                                    <a href="<?php echo site_url('publicaciones/eliminar/'.$publicacion->idPublicacion); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta publicación?');">Eliminar</a>
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

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->
                    </div>   
                </div> <!-- container -->
             </div> <!-- content -->

   