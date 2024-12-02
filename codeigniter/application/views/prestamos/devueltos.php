<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Préstamos Devueltos</h4>
                            
                            <?php if($this->session->flashdata('mensaje')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('mensaje'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('error'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if($this->session->flashdata('pdf_path')): ?>
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <strong>¡Ficha generada!</strong> 
                                    <a href="<?php echo $this->session->flashdata('pdf_path'); ?>" 
                                       target="_blank" 
                                       class="btn btn-info btn-sm ml-2">
                                        <i class="mdi mdi-file-pdf"></i> Descargar Ficha
                                    </a>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <div class="table-responsive">
                            <table class="table table-striped" id="tabla-detalle">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>CI</th>
                                            <th>Lector</th>
                                            <th>Publicación</th>
                                            <th>Fecha Préstamo</th>
                                            <th>Fecha Devolución</th>
                                            <th>Estado</th>
                                            <th>Encargado</th>
                                        
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $contador = 1; foreach ($prestamos as $prestamo): ?>
                                            <tr>
                                                <td><?php echo $contador++; ?></td>
                                                <td><?php echo htmlspecialchars($prestamo->carnet); ?></td> 
                                                <td>
                                                    <?php echo htmlspecialchars($prestamo->nombre_lector . ' ' . 
                                                        $prestamo->apellido_lector); ?>
                                                </td>
 
                                                <td><?php echo htmlspecialchars($prestamo->titulo); ?></td>
                                                <td>
                                                    <?php echo date('d/m/Y H:i', strtotime($prestamo->fechaPrestamo)); ?>
                                                </td>
                                                
                                                <td><?php echo $prestamo->horaDevolucion ? date('d/m/Y H:i', strtotime($prestamo->horaDevolucion)) : 'N/A'; ?></td>
                                                
                                                <td>
                                                    <?php
                                                    $estado_class = 'badge-secondary';
                                                    $nombre_estado = '';
                                                    
                                                    switch ($prestamo->estadoDevolucion) {
                                                        case ESTADO_DEVOLUCION_BUENO:
                                                            $estado_class = 'badge-success';
                                                            $nombre_estado = 'BUENO';
                                                            break;
                                                        case ESTADO_DEVOLUCION_DAÑADO:
                                                            $estado_class = 'badge-warning';
                                                            $nombre_estado = 'DAÑADO';
                                                            break;
                                                        case ESTADO_DEVOLUCION_PERDIDO:
                                                            $estado_class = 'badge-danger';
                                                            $nombre_estado = 'PERDIDO';
                                                            break;
                                                        default:
                                                            $nombre_estado = 'NO DEFINIDO';
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $estado_class; ?>">
                                                        <?php echo $nombre_estado; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($prestamo->nombre_encargado_devolucion . ' ' . 
                                                        $prestamo->apellido_encargado_devolucion); ?>
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
$(document).ready(function() {
    var table = $('#datatable-buttons').DataTable({
        lengthChange: false,
        buttons: ['copy', 'excel', 'pdf', 'colvis'],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        },
        order: [[4, 'desc']],
        pageLength: 10
    });

    table.buttons().container()
        .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');

    // Verificar si hay una ficha PDF recién generada
    if ($('.alert-info').length) {
        setTimeout(function() {
            $('.alert-info').fadeOut('slow');
        }, 10000); // Ocultar después de 10 segundos
    }
});

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
    // Inicializar DataTable
    $('#tabla-detalle').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[3, "desc"]],
        "pageLength": 10
    });
});
</script>