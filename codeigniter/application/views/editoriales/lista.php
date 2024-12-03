<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Lista de Editoriales</h4>
                            <p class="text-muted font-13 mb-4">Aquí se muestran todas las editoriales disponibles.</p>

                            <?php if($this->session->flashdata('mensaje')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('mensaje'); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <?php if($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('error'); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <a href="<?php echo site_url('editoriales/agregar'); ?>" class="btn btn-primary mb-3">Agregar Nueva Editorial</a>
                            <div class="table-responsive">
                            <table class="table table-striped" id="tabla-detalle">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Nombre de la Editorial</th>
                                      
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $contador = 1; foreach ($editoriales as $editorial): ?>
                                    <tr>
                                        <td><?php echo $contador++; ?></td>
                                        <td><?php echo $editorial->nombreEditorial; ?></td>
                                       
                                        <td>
                                            <a href="<?php echo site_url('editoriales/editar/'.$editorial->idEditorial); ?>" 
                                               class="btn btn-primary btn-sm" 
                                               data-toggle="tooltip" 
                                               title="Editar">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            
                                            <button type="button" 
                                                    class="btn <?php echo $editorial->estado ? 'btn-danger' : 'btn-success'; ?> btn-sm"
                                                    onclick="confirmarAccion(<?php echo $editorial->idEditorial; ?>, <?php echo $editorial->estado; ?>)"
                                                    data-toggle="tooltip" 
                                                    title="<?php echo $editorial->estado ? 'Deshabilitar' : 'Habilitar'; ?>">
                                                <i class="fe-<?php echo $editorial->estado ? 'trash-2' : 'refresh-cw'; ?>"></i>
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
            </div>
        </div>
    </div>
</div>

<script>
    function confirmarAccion(idEditorial, estadoActual) {
    const accion = estadoActual ? 'deshabilitar' : 'habilitar';
    const mensaje = `¿Está seguro de ${accion} esta editorial?`;
    
    if (confirm(mensaje)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo site_url('editoriales/eliminar/'); ?>' + idEditorial;
        
        // Agregar token CSRF
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?php echo $this->security->get_csrf_token_name(); ?>';
        csrfInput.value = '<?php echo $this->security->get_csrf_hash(); ?>';
        form.appendChild(csrfInput);
        
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'estado';
        hiddenInput.value = estadoActual ? '0' : '1';
        
        form.appendChild(hiddenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

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