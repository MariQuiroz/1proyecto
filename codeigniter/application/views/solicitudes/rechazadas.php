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
                                      
                                        <th>Usuario</th>
                                        <th>Publicación</th>
                                        <th>Fecha Solicitud</th>
                                        <th>Fecha Rechazo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($solicitudes as $solicitud): ?>
                                    <tr>
                                      
                                        <td><?php echo $solicitud->nombres . ' ' . $solicitud->apellidoPaterno; ?></td>
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

