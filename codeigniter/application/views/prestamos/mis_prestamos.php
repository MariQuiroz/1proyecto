<div class="content-page">
    <div class="content">
        <div class="container-fluid"> 
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Mis Préstamos</h4>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Listado de Mis Préstamos</h4>
                            <p class="text-muted font-13 mb-4">
                                Aquí se muestran todos tus préstamos, tanto activos como históricos.
                            </p>

                            <?php if (empty($prestamos)): ?>
                                <div class="alert alert-info">
                                    No tienes préstamos registrados.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                <table class="table table-striped" id="tabla-detalle">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Publicación</th>
                                            <th>Editorial</th>
                                            <th>Tipo</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Devolución</th>
                                            <th>Estado</th>
                                          
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $contador = 1; foreach ($prestamos as $prestamo): ?>
                                        <tr>
                                            <td><?php echo $contador++; ?></td>
                                            <td><?= htmlspecialchars($prestamo->titulo); ?></td>
                                            <td><?= htmlspecialchars($prestamo->nombreEditorial); ?></td>
                                            <td><?= htmlspecialchars($prestamo->nombreTipo); ?></td>
                                            <td>
                                                <?= date('d/m/Y', strtotime($prestamo->fechaPrestamo)); ?>
                                                <br>
                                                <small class="text-muted">
                                                    <?= date('H:i', strtotime($prestamo->horaInicio)); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if ($prestamo->horaDevolucion): ?>
                                                    <?= date('d/m/Y H:i', strtotime($prestamo->horaDevolucion)); ?>
                                                <?php else: ?>
                                                    <span class="text-warning">En préstamo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $badge_class = 'badge-secondary';
                                                $estado_texto = 'Desconocido';
                                                
                                                switch($prestamo->estadoPrestamo) {
                                                    case ESTADO_PRESTAMO_ACTIVO:
                                                        $badge_class = 'badge-warning';
                                                        $estado_texto = 'En Préstamo';
                                                        break;
                                                    case ESTADO_PRESTAMO_FINALIZADO:
                                                        $badge_class = 'badge-success';
                                                        $estado_texto = 'Devuelto';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $badge_class ?>">
                                                    <?= $estado_texto ?>
                                                </span>
                                            </td>
                                            
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
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
