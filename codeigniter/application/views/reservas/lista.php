<div class="container mt-4">
    <h2>Lista de Reservas</h2>
    
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>

    <?php if (empty($reservas)): ?>
        <p>No hay reservas disponibles.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Publicación</th>
                    <th>Fecha de Reserva</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?= $reserva->nombres . ' ' . $reserva->apellidoPaterno ?></td>
                        <td><?= $reserva->titulo ?></td>
                        <td><?= date('d/m/Y', strtotime($reserva->fechaReserva)) ?></td>
                        <td><?= $reserva->estado == 1 ? 'Activa' : 'Finalizada' ?></td>
                        <td>
                            <?php if ($reserva->estado == 1): ?>
                                <a href="<?= site_url('prestamos/agregar/'.$reserva->idReserva) ?>" class="btn btn-sm btn-primary">Realizar Préstamo</a>
                                <a href="<?= site_url('reservas/cancelar/'.$reserva->idReserva) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de cancelar esta reserva?')">Cancelar</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($reserva->estado == 1): ?>
                                <?php echo form_open_multipart("prestamos/confirmar"); ?>
                                <input type="hidden" name="idReserva" value="<?php echo $reserva->idReserva; ?>">
                                <input type="submit" name="button" value="Crear Préstamo" class="btn btn-sm btn-primary">
                                <?php echo form_close(); ?>

                                <?php echo form_open_multipart("reservas/cancelar"); ?>
                                <input type="hidden" name="idReserva" value="<?php echo $reserva->idReserva; ?>">
                                <input type="submit" name="button" value="Cancelar" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de cancelar esta reserva?')">
                                <?php echo form_close(); ?>
                            <?php endif; ?>
                        </td>
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<br>
<button onclick="goBack()" class="btn btn-secondary mb-3">Volver</button>
<script>
function goBack() {
    window.history.back();
}
</script>