<div class="content-page">
    <div class="content">
        <div class="container-fluid"> 
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Historial de Préstamos</h4>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Historial Completo de Préstamos</h4>
                            <p class="text-muted font-13 mb-4">
                                Aquí se muestra el historial completo de todos los préstamos.
                            </p>
                            <div class="table-responsive">
                            <table class="table table-striped" id="tabla-detalle">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>CI</th>
                                        <th>Lector</th>
                                        <th>Publicación</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Estado</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $contador = 1; foreach ($prestamos as $prestamo): ?>
                                    <tr>
                                        <td><?php echo $contador++; ?></td>
                                        <td><?php echo htmlspecialchars($prestamo->carnet); ?></td> 
                                        <td><?php echo htmlspecialchars($prestamo->nombres . ' ' . $prestamo->apellidoPaterno. ' ' . $prestamo->apellidoMaterno); ?></td>
                                        <td><?php echo htmlspecialchars($prestamo->titulo); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($prestamo->fechaPrestamo)); ?></td>
                                        <td><?php echo $prestamo->horaDevolucion ? date('d/m/Y H:i', strtotime($prestamo->horaDevolucion)) : 'N/A'; ?></td>
                                        <td>
                                            <?php
                                            switch($prestamo->estadoPrestamo) {
                                                case ESTADO_PRESTAMO_ACTIVO:
                                                    echo '<span class="badge badge-warning">Activo</span>';
                                                    break;
                                                case ESTADO_PRESTAMO_FINALIZADO:
                                                    echo '<span class="badge badge-success">Finalizado</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge badge-secondary">Desconocido</span>';
                                            }
                                            ?>
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
