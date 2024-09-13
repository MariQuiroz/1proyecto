<!-- application/views/prestamos/vencidos.php -->
<div class="container mt-4">
    <h2>Préstamos Vencidos</h2>
    
    <?php if (empty($prestamos)): ?>
        <p>No hay préstamos vencidos.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Publicación</th>
                    <th>Fecha de Préstamo</th>
                    <th>Fecha de Devolución Esperada</th>
                    <th>Días de Retraso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestamos as $prestamo): ?>
                    <tr>
                        <td><?= $prestamo->nombres . ' ' . $prestamo->apellidoPaterno ?></td>
                        <td><?= $prestamo->titulo ?></td>
                        <td><?= date('d/m/Y', strtotime($prestamo->fechaPrestamo)) ?></td>
                        <td><?= date('d/m/Y', strtotime($prestamo->fechaDevolucionEsperada)) ?></td>
                        <td><?= floor((time() - strtotime($prestamo->fechaDevolucionEsperada)) / (60 * 60 * 24)) ?></td>
                        <td>
                            <a href="<?= site_url('prestamos/devolver/'.$prestamo->idPrestamo) ?>" class="btn btn-sm btn-primary">Registrar Devolución</a>
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