<!-- application/views/prestamos/seleccionar_reserva.php -->
<div class="container">
    <h2>Seleccionar Reserva para Préstamo</h2>
    <?php if(isset($reservas_pendientes) && !empty($reservas_pendientes)): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Publicación</th>
                    <th>Fecha de Reserva</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reservas_pendientes as $reserva): ?>
                    <tr>
                        <td><?php echo $reserva->nombre_usuario; ?></td>
                        <td><?php echo $reserva->titulo_publicacion; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($reserva->fechaReserva)); ?></td>
                        <td>
                            <a href="<?php echo site_url('prestamos/confirmar/'.$reserva->idReserva); ?>" class="btn btn-primary">Confirmar Préstamo</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay reservas pendientes para confirmar.</p>
    <?php endif; ?>
</div>