<div class="container mt-4">
    <h2>Mis Reservas</h2>
    
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>

    <?php if (empty($reservas)): ?>
        <p>No tienes reservas activas.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Publicación</th>
                    <th>Fecha de Reserva</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?= $reserva->titulo ?></td>
                        <td><?= date('d/m/Y', strtotime($reserva->fechaReserva)) ?></td>
                        <td><?= $reserva->estado == 1 ? 'Activa' : 'Finalizada' ?></td>
                        <td>
                            <?php if ($reserva->estado == 1): ?>
                                <a href="<?= site_url('reservas/cancelar/'.$reserva->idReserva) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de cancelar esta reserva?')">Cancelar</a>
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


