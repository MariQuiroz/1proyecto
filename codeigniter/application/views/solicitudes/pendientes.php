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

                            <table id="basic-datatable" class="table dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>ID Solicitud</th>
                                        <th>Usuario</th>
                                        <th>Publicación</th>
                                        <th>Fecha Solicitud</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($solicitudes as $solicitud): ?>
                                    <tr>
                                        <td><?php echo $solicitud->idSolicitud; ?></td>
                                        <td><?php echo $solicitud->nombres . ' ' . $solicitud->apellidoPaterno; ?></td>
                                        <td><?php echo $solicitud->titulo; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($solicitud->fechaSolicitud)); ?></td>
                                        <td>
                                            <a href="<?php echo site_url('solicitudes/aprobar/' . $solicitud->idSolicitud); ?>" class="btn btn-success btn-sm">Aprobar</a>
                                            <a href="<?php echo site_url('solicitudes/rechazar/' . $solicitud->idSolicitud); ?>" class="btn btn-danger btn-sm">Rechazar</a>
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
// Colocar este script al final de la vista de solicitudes pendientes
<?php if ($this->input->get('pdf')): ?>
    window.open('<?php echo $this->input->get('pdf'); ?>', '_blank');
<?php endif; ?>
</script>