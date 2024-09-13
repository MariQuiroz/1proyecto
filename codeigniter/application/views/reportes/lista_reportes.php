<!-- application/views/reportes/lista_reportes.php -->
<div class="container mt-4">
    <h2>Reportes Generados</h2>
    
    <?php if (empty($reportes)): ?>
        <p>No hay reportes generados.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Fecha de Generación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportes as $reporte): ?>
                    <tr>
                        <td><?= $reporte->id ?></td>
                        <td><?= ucfirst(str_replace('_', ' ', $reporte->tipo)) ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($reporte->fechaGeneracion)) ?></td>
                        <td>
                            <a href="<?= site_url('reportes/ver_reporte/'.$reporte->id) ?>" class="btn btn-sm btn-primary">Ver Reporte</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="<?= site_url('reportes/index') ?>" class="btn btn-secondary mt-3">Volver al Menú de Reportes</a>
</div>
<br>
<button onclick="goBack()" class="btn btn-secondary mb-3">Volver</button>
<script>
function goBack() {
    window.history.back();
}
</script>