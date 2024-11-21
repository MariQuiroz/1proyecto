<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Análisis por Profesión de Lectores</h4>
                        
                        <!-- Filtros -->
                        <form method="GET" class="mt-3" id="formFiltros">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha Inicio</label>
                                        <input type="date" name="fecha_inicio" class="form-control" 
                                               value="<?php echo $filtros['fecha_inicio'] ?? date('Y-m-d', strtotime('-1 month')); ?>"
                                               max="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha Fin</label>
                                        <input type="date" name="fecha_fin" class="form-control"
                                               value="<?php echo $filtros['fecha_fin'] ?? date('Y-m-d'); ?>"
                                               max="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search"></i> Filtrar
                                            </button>
                                            <button type="button" class="btn btn-danger" onclick="exportarPDF()">
                                                <i class="fa fa-file-pdf"></i> PDF
                                            </button>
                                            <button type="button" class="btn btn-success" onclick="exportarExcel()">
                                                <i class="fa fa-file-excel"></i> Excel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card-body">
                        <!-- Gráfico y Tabla Resumen -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <canvas id="profesionesChart"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Profesión</th>
                                                <th>Total Préstamos</th>
                                                <th>Prom. Días</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($estadisticas as $est): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($est->profesion); ?></td>
                                                <td class="text-right"><?php echo $est->total_prestamos; ?></td>
                                                <td class="text-right"><?php echo $est->promedio_dias_prestamo; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Análisis de Datos -->
                        <div class="row">
                            <div class="col-12">
                                <h5>Análisis de Datos del Período</h5>
                                <?php
                                if (!empty($estadisticas)) {
                                    $max_prestamos = max(array_column($estadisticas, 'total_prestamos'));
                                    $min_prestamos = min(array_column($estadisticas, 'total_prestamos'));
                                    $prom_dias = array_sum(array_column($estadisticas, 'promedio_dias_prestamo')) / count($estadisticas);
                                ?>
                                <div class="alert alert-info">
                                    <p><strong>Hallazgos principales:</strong></p>
                                    <ul>
                                        <li>La profesión con más préstamos registra <?php echo $max_prestamos; ?> solicitudes.</li>
                                        <li>El promedio general de días de préstamo es de <?php echo number_format($prom_dias, 1); ?> días.</li>
                                        <li>Se observa una diferencia de <?php echo $max_prestamos - $min_prestamos; ?> préstamos entre la profesión más y menos activa.</li>
                                    </ul>
                                </div>
                                <?php } else { ?>
                                <div class="alert alert-warning">
                                    No se encontraron datos para el período seleccionado.
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Detalle -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="card-title">Detalle de Préstamos por Profesión</h4>
                        
                        <!-- Filtro de Profesión -->
                        <div class="form-group mt-3">
                            <select name="profesion" class="form-control" id="selectProfesion">
                                <option value="">Todas las profesiones</option>
                                <?php foreach ($estadisticas as $est): ?>
                                    <option value="<?php echo htmlspecialchars($est->profesion); ?>"
                                            <?php echo ($profesion_seleccionada == $est->profesion) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($est->profesion); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="tabla-detalle">
                                <thead>
                                    <tr>
                                        <th>Lector</th>
                                        <th>Profesión</th>
                                        <th>Publicación</th>
                                        <th>Tipo</th>
                                        <th>Editorial</th>
                                        <th>Fecha Public.</th>
                                        <th>Fecha Préstamo</th>
                                        <th>Fecha Devolución</th>
                                        <th>Estado</th>
                                        <th>Días</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($detalles)): ?>
                                        <?php foreach ($detalles as $detalle): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($detalle->nombres . ' ' . $detalle->apellidoPaterno); ?></td>
                                                <td><?php echo htmlspecialchars($detalle->profesion); ?></td>
                                                <td><?php echo htmlspecialchars($detalle->titulo_publicacion); ?></td>
                                                <td><?php echo htmlspecialchars($detalle->tipo_publicacion); ?></td>
                                                <td><?php echo htmlspecialchars($detalle->nombreEditorial); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($detalle->fechaPublicacion)); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($detalle->fechaPrestamo)); ?></td>
                                                <td>
                                                    <?php 
                                                    echo $detalle->fechaDevolucion 
                                                        ? date('d/m/Y H:i', strtotime($detalle->fechaDevolucion))
                                                        : 'En préstamo';
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php 
                                                        echo $detalle->estado_devolucion == 'Bueno' ? 'badge-success' :
                                                            ($detalle->estado_devolucion == 'Dañado' ? 'badge-warning' :
                                                                ($detalle->estado_devolucion == 'Perdido' ? 'badge-danger' : 'badge-info')); 
                                                    ?>">
                                                        <?php echo $detalle->estado_devolucion; ?>
                                                    </span>
                                                </td>
                                                <td class="text-right"><?php echo $detalle->dias_prestamo; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('profesionesChart').getContext('2d');
    
    var data = {
        labels: <?php echo json_encode(array_column($estadisticas, 'profesion')); ?>,
        datasets: [{
            label: 'Total Préstamos',
            data: <?php echo json_encode(array_column($estadisticas, 'total_prestamos')); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        },
        {
            label: 'Promedio Días',
            data: <?php echo json_encode(array_column($estadisticas, 'promedio_dias_prestamo')); ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.5)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    };

    new Chart(ctx, {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});

// DataTables y filtros
$(document).ready(function() {
    var tabla = $('#tabla-detalle').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[6, "desc"]],
        "pageLength": 10,
        "responsive": true
    });

    // Manejar cambio en select de profesión
    $('#selectProfesion').change(function() {
        $('#formFiltros').submit();
    });

    // Validación de fechas
    $('input[name="fecha_fin"]').change(function() {
        var fechaInicio = $('input[name="fecha_inicio"]').val();
        var fechaFin = $(this).val();
        
        if (fechaInicio && fechaFin && fechaInicio > fechaFin) {
            alert('La fecha final no puede ser menor a la fecha inicial');
            $(this).val('');
        }
    });
});

// Funciones de exportación
function exportarPDF() {
    var form = document.getElementById('formFiltros');
    var profesion = document.getElementById('selectProfesion').value;
    form.action = '?export=pdf&profesion=' + encodeURIComponent(profesion);
    form.submit();
    form.action = '';
}

function exportarExcel() {
    var form = document.getElementById('formFiltros');
    var profesion = document.getElementById('selectProfesion').value;
    form.action = '?export=excel&profesion=' + encodeURIComponent(profesion);
    form.submit();
    form.action = '';
}
</script>