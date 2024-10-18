<div class="content-page">
    <div class="content">
        <div class="container-fluid overflow-auto" style="max-height: 500px;">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Mis Solicitudes</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                            <table id="basic-datatable" class="table dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                         
                                            <th>Publicación</th>
                                            <th>Fecha Solicitud</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($solicitudes as $solicitud): ?>
                                        <tr>
                                            
                                            <td><?php echo $solicitud->titulo; ?></td>
                                            <td><?php echo date('d/m/Y H:i:s', strtotime($solicitud->fechaSolicitud)); ?></td>
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
                                                    case ESTADO_SOLICITUD_FINALIZADA:
                                                        echo '<span class="badge badge-info">Finalizada</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge badge-secondary">Desconocido</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo site_url('solicitudes/detalle/' . $solicitud->idSolicitud); ?>" class="btn btn-info btn-sm">Ver Detalles</a>
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