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
                <h4 class="header-title">Lista de Tipos</h4>
                <p class="text-muted font-13 mb-4">
                    Aquí se muestran todos los tipos de publicaciones disponibles.
                </p>

                <a href="<?php echo site_url('tipos/agregar'); ?>" class="btn btn-primary mb-3">Agregar Nuevo Tipo</a>

                <table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>Nombre del Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tipos as $tipo): ?>
                        <tr>
                            <td><?php echo $tipo->nombreTipo; ?></td>
                            <td>
                                <a href="<?php echo site_url('tipos/editar/'.$tipo->idTipo); ?>" class="btn btn-primary btn-sm">Editar</a>
                                <a href="<?php echo site_url('tipos/eliminar/'.$tipo->idTipo); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este tipo?');">Eliminar</a>
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