<div class="container-fluid"> 
    <h2>Préstamos Vencidos</h2>
    
    <?php if (empty($prestamos)): ?>
        <div class="alert alert-info" role="alert">
            No hay préstamos vencidos.
        </div>
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
                        <td><?= htmlspecialchars($prestamo->nombres . ' ' . $prestamo->apellidoPaterno) ?></td>
                        <td><?= htmlspecialchars($prestamo->titulo) ?></td>
                        <td><?= date('d/m/Y', strtotime($prestamo->fechaPrestamo)) ?></td>
                        <td><?= date('d/m/Y', strtotime($prestamo->fechaDevolucionEsperada)) ?></td>
                        <td class="<?= (floor((time() - strtotime($prestamo->fechaDevolucionEsperada)) / (60 * 60 * 24)) > 5) ? 'text-danger' : '' ?>">
                            <?= floor((time() - strtotime($prestamo->fechaDevolucionEsperada)) / (60 * 60 * 24)) ?>
                        </td>
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
