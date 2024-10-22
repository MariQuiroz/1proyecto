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
                                            <a href="<?php echo site_url('editoriales/editar/'.$editorial->idEditorial); ?>" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Editar" aria-label="Editar Editorial">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm" data-toggle="tooltip" title="Deshabilitar" aria-label="Deshabilitar Editorial" onclick="confirmDelete('<?php echo site_url('editoriales/eliminar/'.$editorial->idEditorial); ?>')">
                                                 <i class="fe-trash-2"></i> 
                                            </button>
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

<!-- Vendor js -->
<script src="<?php echo base_url('adminXeria/dist/assets/js/vendor.min.js'); ?>"></script>

<!-- App js -->
<script src="<?php echo base_url('adminXeria/dist/assets/js/app.min.js'); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable
    var dataTable = new DataTable('#datatable-buttons', {
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.22/i18n/Spanish.json"
        },
        buttons: ["copy", "excel", "pdf"]
    });

    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Función para confirmar eliminación
    window.confirmDelete = function(url) {
        const modalHtml = `
            <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmDeleteLabel">Confirmar Eliminación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ¿Está seguro de eliminar esta editorial?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <a href="${url}" class="btn btn-danger">Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        var modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        modal.show();
        
        document.getElementById('confirmDeleteModal').addEventListener('hidden.bs.modal', function () {
            this.remove();
        });
    };

    // Aquí puedes agregar la inicialización de ApexCharts si es necesario
    // Ejemplo:
    // var options = {
    //     // ... opciones del gráfico
    // };
    // var chart = new ApexCharts(document.querySelector("#id-del-grafico"), options);
    // chart.render();
});
</script>
