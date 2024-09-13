<!-- application/views/reportes/ver_reporte.php -->
<div class="container mt-4">
    <h2>Ver Reporte</h2>
    
    <?php if (!$reporte): ?>
        <p>El reporte solicitado no existe.</p>
    <?php else: ?>
        <h3><?= ucfirst(str_replace('_', ' ', $reporte->tipo)) ?></h3>
        <p><strong>Fecha de Generación:</strong> <?= date('d/m/Y H:i:s', strtotime($reporte->fechaGeneracion)) ?></p>
        
        <?php
        $contenido = json_decode($reporte->contenido);
        switch ($reporte->tipo) {
            case 'prestamos':
                ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Total de Préstamos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contenido as $item): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($item->fecha)) ?></td>
                                <td><?= $item->total_prestamos ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php
                break;
            case 'publicaciones_populares':
                ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Total de Préstamos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contenido as $item): ?>
                            <tr>
                                <td><?= $item->titulo ?></td>
                                <td><?= $item->total_prestamos ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php
                break;
            case 'usuarios_activos':
                ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Total de Préstamos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contenido as $item): ?>
                            <tr>
                                <td><?= $item->nombres . ' ' . $item->apellidoPaterno ?></td>
                                <td><?= $item->total_prestamos ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php
                break;
        }
        ?>
    <?php endif; ?>

    <a href="<?= site_url('reportes/listar_reportes') ?>" class="btn btn-secondary mt-3">Volver a la Lista de Reportes</a>
</div>
<br>
<button onclick="goBack()" class="btn btn-secondary mb-3">Volver</button>
<script>
function goBack() {
    window.history.back();
}
</script>


