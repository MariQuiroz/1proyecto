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

                            <?php if($this->session->flashdata('mensaje')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('mensaje'); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <a href="<?php echo site_url('tipos/agregar'); ?>" class="btn btn-primary mb-3">Agregar Nuevo Tipo</a>

                            <div class="table-responsive">
                            <table class="table table-striped" id="tabla-detalle">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Nombre del Tipo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $contador = 1;
                                    foreach ($tipos as $tipo): 
                                    ?>
                                    <tr>
                                        <td><?php echo $contador++; ?></td>
                                        <td><?php echo $tipo->nombreTipo; ?></td>
                                        <td>
                                            <a href="<?php echo site_url('tipos/editar/'.$tipo->idTipo); ?>" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Editar">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <a href="<?php echo site_url('tipos/eliminar/'.$tipo->idTipo); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este tipo?');" data-toggle="tooltip" title="Deshabilitar">
                                                <i class="fe-trash-2"></i>
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
            </div>
        </div> <!-- container -->
    </div> <!-- content -->
</div>

<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->

<script>
    $(document).ready(function() {
        // Inicializar DataTable con configuración para mantener la numeración correcta
        $('#datatable-buttons').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.22/i18n/Spanish.json"
            },
            "buttons": ["copy", "excel", "pdf"],
            // Asegurar que la numeración se mantenga correcta incluso con la paginación
            "drawCallback": function(settings) {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart;
                api.column(0, {page: 'current'}).nodes().each(function(cell, i) {
                    cell.innerHTML = startIndex + i + 1;
                });
            }
        });

        // Inicializar tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    $('#tabla-detalle').DataTable({
        "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":           "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        "order": [[3, "desc"]],
        "pageLength": 10
    });
});
</script>
<!-- Vendor js -->
<script src="<?php echo base_url('adminXeria/dist/assets/js/vendor.min.js'); ?>"></script>

<!-- App js -->
<script src="<?php echo base_url('adminXeria/dist/assets/js/app.min.js'); ?>"></script>