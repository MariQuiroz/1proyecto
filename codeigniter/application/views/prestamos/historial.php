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

                            <table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                       
                                        <th>Lector</th>
                                        <th>Publicación</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Estado</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($prestamos as $prestamo): ?>
                                    <tr>
                                     
                                        <td><?php echo htmlspecialchars($prestamo->nombres . ' ' . $prestamo->apellidoPaterno); ?></td>
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

                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div><!-- end col-->
            </div>
            <!-- end row-->
        </div>
    </div>
</div>


