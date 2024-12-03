<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Solicitudes Pendientes</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Gestión de Solicitudes Pendientes</h4>
                            <p class="text-muted font-13 mb-4">
                                Aquí puedes ver y gestionar todas las solicitudes pendientes de préstamo de publicaciones.
                            </p>

                            <div class="table-responsive">
                            <table class="table table-striped" id="tabla-detalle">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>CI</th>
                                            <th>Lector</th>
                                            <th>Título Publicación</th>
                                            <th>Ubicación</th>
                                            <th>Fecha Solicitud</th>
                                            <th>Tiempo Restante</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $contador = 1; foreach ($solicitudes as $solicitud): 
                                            $tiempo_expiracion = strtotime($solicitud->fechaExpiracionReserva);
                                            $tiempo_actual = time();
                                            $tiempo_restante = $tiempo_expiracion - $tiempo_actual;
                                            $solicitud_activa = ($tiempo_restante > 0 && $solicitud->estadoSolicitud == ESTADO_SOLICITUD_PENDIENTE);
                                        ?>
                                        <tr>
                                        <td><?php echo $contador++; ?></td>
                                        <td>
                                                <?php echo htmlspecialchars($solicitud->carnet); ?>
                                            </td>

                                            
                                            <td>
                                                <strong><?php echo htmlspecialchars($solicitud->nombres . ' ' . $solicitud->apellidoPaterno. ' ' . $solicitud->apellidoMaterno); ?></strong>
                                            </td>


                                            <td>
                                                <?php echo htmlspecialchars($solicitud->titulo); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($solicitud->ubicacionFisica); ?>
                                            </td>

                                            <td>
                                                <?php echo date('d/m/Y H:i', strtotime($solicitud->fechaSolicitud)); ?>
                                            </td>

                                            <td class="text-center">
                                                <?php if ($solicitud->estadoSolicitud == ESTADO_SOLICITUD_PENDIENTE): ?>
                                                    <?php if ($tiempo_restante > 0): ?>
                                                        <span class="badge badge-warning">
                                                            <?php 
                                                            $horas = floor($tiempo_restante / 3600);
                                                            $minutos = floor(($tiempo_restante % 3600) / 60);
                                                            echo sprintf('%02dh %02dm', $horas, $minutos);
                                                            ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Expirado</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-center">
                                                <?php
                                                switch($solicitud->estadoSolicitud) {
                                                    case ESTADO_SOLICITUD_PENDIENTE:
                                                        if ($tiempo_restante > 0) {
                                                            echo '<span class="badge badge-warning">Pendiente</span>';
                                                        } else {
                                                            echo '<span class="badge badge-secondary">Por Expirar</span>';
                                                        }
                                                        break;
                                                    case ESTADO_SOLICITUD_EXPIRADA:
                                                        echo '<span class="badge badge-secondary">Expirada</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge badge-secondary">Desconocido</span>';
                                                }
                                                ?>
                                            </td>

                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <?php if ($solicitud_activa): ?>
                                                        <a href="<?php echo site_url('solicitudes/aprobar/' . $solicitud->idSolicitud); ?>" 
                                                           class="btn btn-success btn-sm" title="Aprobar">
                                                            <i class="mdi mdi-check"></i>
                                                        </a>
                                                        <a href="<?php echo site_url('solicitudes/rechazar/' . $solicitud->idSolicitud); ?>" 
                                                           class="btn btn-danger btn-sm"
                                                           onclick="return confirm('¿Está seguro de rechazar esta solicitud?');"
                                                           title="Rechazar">
                                                            <i class="mdi mdi-close"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="<?php echo site_url('solicitudes/detalle/' . $solicitud->idSolicitud); ?>" 
                                                       class="btn btn-info btn-sm"
                                                       title="Ver Detalles">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Leyenda de estados y tiempos -->
                            <div class="mt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Estados:</h5>
                                        <span class="badge badge-warning mr-2">Pendiente</span>
                                       
                                        <span class="badge badge-secondary">Expirada</span>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Información:</h5>
                                        <p class="text-muted mb-0">
                                            <small>• Las solicitudes expiran después de <?php echo $tiempo_limite; ?> minutos</small><br>
                                            <small>• Las solicitudes expiradas no pueden ser aprobadas</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($this->input->get('pdf')): ?>
<script>
    window.open('<?php echo $this->input->get('pdf'); ?>', '_blank');
</script>
<?php endif; ?>

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