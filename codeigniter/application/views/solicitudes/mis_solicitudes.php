<div class="content-page">
    <div class="content">
        <div class="container-fluid">
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
                                            <th>Tiempo Restante</th>
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
                                                    case ESTADO_SOLICITUD_EXPIRADA:
                                                        echo '<span class="badge badge-secondary">Expirada</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge badge-secondary">Desconocido</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($solicitud->estadoSolicitud == ESTADO_SOLICITUD_PENDIENTE): ?>
                                                    <?php
                                                    $tiempo_expiracion = strtotime($solicitud->fechaExpiracionReserva);
                                                    $tiempo_actual = time();
                                                    $tiempo_restante = $tiempo_expiracion - $tiempo_actual;
                                                    
                                                    if ($tiempo_restante > 0) {
                                                        $minutos = floor($tiempo_restante / 60);
                                                        $segundos = $tiempo_restante % 60;
                                                        echo '<span class="text-warning">'.
                                                             sprintf('%02d:%02d', $minutos, $segundos).
                                                             '</span>';
                                                    } else {
                                                        echo '<span class="text-danger">Expirado</span>';
                                                    }
                                                    ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo site_url('solicitudes/detalle/'.$solicitud->idSolicitud); ?>" 
                                                   class="btn btn-info btn-sm">
                                                    <i class="mdi mdi-eye"></i> Ver Detalles
                                                </a>
                                                <?php if ($solicitud->estadoSolicitud == ESTADO_SOLICITUD_PENDIENTE): ?>
                                                    <a href="<?php echo site_url('solicitudes/cancelar/'.$solicitud->idSolicitud); ?>" 
                                                       class="btn btn-danger btn-sm ml-1"
                                                       onclick="return confirm('¿Está seguro de cancelar esta solicitud?');">
                                                        <i class="mdi mdi-close"></i> Cancelar
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Leyenda de estados -->
                            <div class="mt-3">
                                <h5>Estados de solicitud:</h5>
                                <span class="badge badge-warning mr-2">Pendiente</span>
                                <span class="badge badge-success mr-2">Aprobada</span>
                                <span class="badge badge-danger mr-2">Rechazada</span>
                                <span class="badge badge-info mr-2">Finalizada</span>
                                <span class="badge badge-secondary">Expirada</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Agregar JavaScript para actualización de tiempo restante -->
<script>
function actualizarTiempoRestante() {
    const ahora = Math.floor(Date.now() / 1000);
    document.querySelectorAll('[data-expiracion]').forEach(elemento => {
        const expiracion = parseInt(elemento.dataset.expiracion);
        const restante = expiracion - ahora;
        
        if (restante > 0) {
            const minutos = Math.floor(restante / 60);
            const segundos = restante % 60;
            elemento.textContent = `${String(minutos).padStart(2, '0')}:${String(segundos).padStart(2, '0')}`;
        } else {
            elemento.textContent = 'Expirado';
            elemento.classList.remove('text-warning');
            elemento.classList.add('text-danger');
        }
    });
}

// Actualizar cada segundo
setInterval(actualizarTiempoRestante, 1000);
</script>