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
                            <h4 class="header-title">Lista de Editoriales</h4>
                            <p class="text-muted font-13 mb-4">
                                Aquí se muestran todas las editoriales disponibles.
                            </p>

                            <?php if($this->session->flashdata('mensaje')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('mensaje'); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <a href="<?php echo site_url('editoriales/agregar'); ?>" class="btn btn-primary mb-3">Agregar Nueva Editorial</a>

                            <table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>Nombre de la Editorial</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($editoriales as $editorial): ?>
                                    <tr>
                                        <td><?php echo $editorial->nombreEditorial; ?></td>
                                        <td>
                                            <a href="<?php echo site_url('editoriales/editar/'.$editorial->idEditorial); ?>" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Editar">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <a href="<?php echo site_url('editoriales/eliminar/'.$editorial->idEditorial); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta editorial?');" data-toggle="tooltip" title="Deshabilitar">
                                                <i class="mdi mdi-account-off"></i>
                                            </a>
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

<script>
    $(document).ready(function() {
        // Inicializar DataTable
        $('#datatable-buttons').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.22/i18n/Spanish.json"
            },
            "buttons": ["copy", "excel", "pdf"]
        });

        // Inicializar tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<!-- Vendor js -->
<script src="<?php echo base_url('adminXeria/dist/assets/js/vendor.min.js'); ?>"></script>

<!-- App js -->
<script src="<?php echo base_url('adminXeria/dist/assets/js/app.min.js'); ?>"></script>