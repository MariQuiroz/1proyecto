<div class="container">
    <h2>Solicitudes Pendientes</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Publicación</th>
                <th>Fecha de Solicitud</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($solicitudes as $solicitud): ?>
                <tr>
                    <td><?php echo $solicitud->nombreUsuario; ?></td>
                    <td><?php echo $solicitud->tituloPublicacion; ?></td>
                    <td><?php echo $solicitud->fechaSolicitud; ?></td>
                    <td>
                        <a href="<?php echo site_url('solicitudes/aprobar/'.$solicitud->idSolicitud); ?>" class="btn btn-success">Aprobar</a>
                        <a href="<?php echo site_url('solicitudes/rechazar/'.$solicitud->idSolicitud); ?>" class="btn btn-danger">Rechazar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>