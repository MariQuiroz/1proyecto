<!-- application/views/prestamos/mis_prestamos.php -->
<div class="container mt-4">
    <h2>Mis Préstamos</h2>
    
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>

    <?php if (empty($prestamos)): ?>
        <p>No tienes préstamos activos.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Publicación</th>
                    <th>Fecha de Préstamo</th>
                    <th>Fecha de Devolución Esperada</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestamos as $prestamo): ?>
                    <tr>
                        <td><?= $prestamo->titulo ?></td>
                        <td><?= date('d/m/Y', strtotime($prestamo->fechaPrestamo)) ?></td>
                        <td><?= date('d/m/Y', strtotime($prestamo->fechaDevolucionEsperada)) ?></td>
                        <td><?= $prestamo->estado == 1 ? 'Activo' : 'Devuelto' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>