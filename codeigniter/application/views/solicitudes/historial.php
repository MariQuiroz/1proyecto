<div class="content-page">
    <div class="content">
        <div class="container-fluid"> 
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Historial de Solicitudes</h4>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Historial Completo de Solicitudes</h4>
                            <p class="text-muted font-13 mb-4">
                                Aquí se muestra el historial completo de todas las solicitudes de préstamo.
                            </p>

                            <div class="table-responsive">
                            <table class="table table-striped" id="tabla-detalle">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>CI</th>
                                        <th>Lector</th>
                                        <th>Publicación</th>
                                        <th>Fecha Solicitud</th>
                                        <th>Estado</th>
                                        
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $contador = 1; foreach ($solicitudes as $solicitud): ?>
                                    <tr>
                                        <td><?php echo $contador++; ?></td>
                                        <td><?php echo $solicitud->carnet; ?></td>
                                        <td><?php echo $solicitud->nombres . ' ' . $solicitud->apellidoPaterno. ' ' . $solicitud->apellidoMaterno; ?></td>
                                        <td><?php echo $solicitud->titulo; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($solicitud->fechaSolicitud)); ?></td>
                                        <td>
                                            <?php
                                            switch($solicitud->estadoSolicitud) {
                                                case ESTADO_SOLICITUD_PENDIENTE:
                                                    echo '<span class="badge badge-warning">Pendiente</span>';
                                                    break;
                                                case ESTADO_SOLICITUD_APROBADA:
                                                    echo '<span class="badge badge-success">Aprobada</span>';
                                                    break;
                                                case ESTADO_SOLICITUD_RECHAZADA:
                                                    echo '<span class="badge badge-danger">Rechazada</span>';
                                                    break;
                                                case ESTADO_SOLICITUD_CANCELADA:
                                                    echo '<span class="badge badge-danger">Cancelada</span>';
                                                    break;
                                                case ESTADO_SOLICITUD_FINALIZADA:
                                                    echo '<span class="badge badge-info">Finalizada</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge badge-secondary">Desconocido</span>';
                                            }
                                            ?>
                                        </td>
                                        
                                        <td>
                                            <a href="<?php echo site_url('solicitudes/detalle/' . $solicitud->idSolicitud); ?>" class="btn btn-info btn-sm">Detalles</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div> 
                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div><!-- end col-->
            </div>
            <!-- end row-->
        </div>
    </div>
</div>

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
