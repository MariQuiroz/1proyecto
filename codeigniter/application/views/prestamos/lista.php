<div class="container mt-4">
    <h2>Lista de Préstamos</h2>
    
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>

    <?php if (empty($prestamos)): ?>
        <p>No hay préstamos registrados.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Publicación</th>
                    <th>Fecha de Préstamo</th>
                    <th>Fecha de Devolución Esperada</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestamos as $prestamo): ?>
                    <tr>
                        <td><?= htmlspecialchars($prestamo->nombres . ' ' . $prestamo->apellidoPaterno) ?></td>
                        <td><?= htmlspecialchars($prestamo->titulo) ?></td>
                        <td><?= date('d/m/Y', strtotime($prestamo->fechaPrestamo)) ?></td>
                        <td><?= date('d/m/Y', strtotime($prestamo->fechaDevolucionEsperada)) ?></td>
                        <td><?= $prestamo->estado == 1 ? 'Activo' : 'Devuelto' ?></td>
                        <td>
                            <?php if ($prestamo->estado == 1): ?>
                                <a href="<?= site_url('prestamos/devolver/'.$prestamo->idPrestamo) ?>" class="btn btn-sm btn-primary">Registrar Devolución</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <!-- Botón para volver -->
    <button onclick="goBack()" class="btn btn-secondary mb-3">Volver</button>
</div>

<script>
function goBack() {
    window.history.back();
}
</script>
