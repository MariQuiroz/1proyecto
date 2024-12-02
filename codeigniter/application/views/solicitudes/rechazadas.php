<div class="content-page">
    <div class="content">
        <div class="container-fluid"> 
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Solicitudes Rechazadas</h4>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Listado de Solicitudes Rechazadas</h4>
                            <p class="text-muted font-13 mb-4">
                                Aquí se muestran todas las solicitudes de préstamo que han sido rechazadas.
                            </p>

                            <table id="basic-datatable" class="table dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>CI</th>
                                        <th>Lector</th>
                                        <th>Publicación</th>
                                        <th>Fecha Solicitud</th>
                                        <th>Fecha Rechazo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $contador = 1; foreach ($solicitudes as $solicitud): ?>
                                    <tr>
                                        <td><?php echo $contador++; ?></td>
                                        <td><?php echo htmlspecialchars($solicitud->carnet); ?></td>
                                        <td><?php echo $solicitud->nombres . ' ' . $solicitud->apellidoPaterno. ' ' . $solicitud->apellidoMaterno; ?></td>
                                        <td><?php echo $solicitud->titulo; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($solicitud->fechaSolicitud)); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($solicitud->fechaAprobacionRechazo)); ?></td>
                                        <td>
                                            <a href="<?php echo site_url('solicitudes/detalle/' . $solicitud->idSolicitud); ?>" class="btn btn-info btn-sm">Detalles</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

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
