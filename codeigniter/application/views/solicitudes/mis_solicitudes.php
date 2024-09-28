<!-- application/views/solicitudes/mis_solicitudes.php -->
<div class="container">
    <h2>Mis Solicitudes</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Título</th>
                <th>Fecha de Solicitud</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($solicitudes as $solicitud): ?>
                <tr>
                    <td><?php echo $solicitud->titulo; ?></td>
                    <td><?php echo $solicitud->fechaSolicitud; ?></td>
                    <td><?php echo $solicitud->estadoSolicitud; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>