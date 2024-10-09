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

                            <table id="basic-datatable" class="table dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>ID Préstamo</th>
                                        <th>Publicación</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($prestamos as $prestamo): ?>
                                    <tr>
                                        <td><?php echo $prestamo->idPrestamo; ?></td>
                                        <td><?php echo $prestamo->titulo; ?></td>
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
                                        <td>
                                            <a href="<?php echo site_url('prestamos/detalle/' . $prestamo->idPrestamo); ?>" class="btn btn-info btn-sm">Detalles</a>
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
    $('#mis-prestamos-table').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
        }
    });
});
</script>