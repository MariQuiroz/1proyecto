<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Detalle de Solicitud</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Solicitud #<?php echo $solicitud->idSolicitud; ?></h4>
                            
                            <div class="form-group">
                                <label>Publicación:</label>
                                <p><?php echo $solicitud->titulo; ?></p>
                            </div>
                            
                            <div class="form-group">
                                <label>Solicitante:</label>
                                <p><?php echo $solicitud->nombres . ' ' . $solicitud->apellidoPaterno; ?></p>
                            </div>
                            
                            <div class="form-group">
                                <label>Fecha de Solicitud:</label>
                                <p><?php echo date('d/m/Y H:i:s', strtotime($solicitud->fechaSolicitud)); ?></p>
                            </div>
                            
                            <div class="form-group">
                                <label>Estado:</label>
                                <p>
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
                                    }
                                    ?>
                                </p>
                            </div>
                            
                            <?php if($this->session->userdata('rol') == 'administrador' || $this->session->userdata('rol') == 'encargado'): ?>
                                <?php if($solicitud->estadoSolicitud == ESTADO_SOLICITUD_PENDIENTE): ?>
                                    <a href="<?php echo site_url('solicitudes/aprobar/' . $solicitud->idSolicitud); ?>" class="btn btn-success">Aprobar</a>
                                    <a href="<?php echo site_url('solicitudes/rechazar/' . $solicitud->idSolicitud); ?>" class="btn btn-danger">Rechazar</a>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <a onclick="window.history.back()" class="btn btn-secondary" style="cursor:pointer">
                                <i class="fe-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>