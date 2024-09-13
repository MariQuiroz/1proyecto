<!-- application/views/reportes/prestamos_por_periodo.php -->
<div class="container mt-4">
    <h2>Reporte de Préstamos por Período</h2>
    
    <?php if (empty($prestamos)): ?>
        <p>No hay datos disponibles para el período seleccionado.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Total de Préstamos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestamos as $prestamo): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($prestamo->fecha)) ?></td>
                        <td><?= $prestamo->total_prestamos ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="grafico-prestamos" style="width: 100%; height: 400px;"></div>

        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            var options = {
                chart: {
                    type: 'line'
                },
                series: [{
                    name: 'Préstamos',
                    data: [<?= implode(',', array_column($prestamos, 'total_prestamos')) ?>]
                }],
                xaxis: {
                    categories: [<?= "'" . implode("','", array_column($prestamos, 'fecha')) . "'" ?>]
                }
            }

            var chart = new ApexCharts(document.querySelector("#grafico-prestamos"), options);
            chart.render();
        </script>
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