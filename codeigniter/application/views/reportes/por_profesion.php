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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha Inicio</label>
                                        <input type="date" name="fecha_inicio" class="form-control" 
                                               value="<?php echo $filtros['fecha_inicio']; ?>"
                                               max="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha Fin</label>
                                        <input type="date" name="fecha_fin" class="form-control"
                                               value="<?php echo $filtros['fecha_fin']; ?>"
                                               max="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Ocupación</label>
                                        <select name="profesion" class="form-control" id="selectProfesion">
                                            <option value="">Todas las ocupaciones</option>
                                            <option value="ESTUDIANTE UMSS" <?php echo $profesion_seleccionada == 'ESTUDIANTE UMSS' ? 'selected' : ''; ?>>Estudiante Umss</option>
                                            <option value="DOCENTE UMSS" <?php echo $profesion_seleccionada == 'DOCENTE' ? 'selected' : ''; ?>>Docente Umss</option>
                                            <option value="INVESTIGADOR" <?php echo $profesion_seleccionada == 'INVESTIGADOR' ? 'selected' : ''; ?>>Investigador</option>
                                            <option value="OTRO" <?php echo $profesion_seleccionada == 'OTRO' ? 'selected' : ''; ?>>Otro</option>
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
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
                        <!-- Resumen General -->
                        <div class="alert alert-info">
                            <h5>Resumen General del Período</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Total Préstamos:</strong> <?php echo $totales['prestamos']; ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Total Lectores:</strong> <?php echo $totales['lectores']; ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Promedio Días/Préstamo:</strong> <?php echo $totales['promedio_dias']; ?>
                                </div>
                            </div>
                        </div>

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
                                                <th>Ocupación</th>
                                                <th>Préstamos</th>
                                                <th>Prom. Días</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($estadisticas as $est): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($est->profesion); ?></td>
                                                <td class="text-right"><?php echo $est->total_prestamos; ?></td>
                                                <td class="text-right"><?php echo number_format($est->promedio_dias_prestamo, 1); ?></td>
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
        <h5>Análisis de Datos</h5>
        <?php
        if (!empty($estadisticas) && $estadisticas[0]->total_prestamos > 0) {
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
                <p><strong>No hay datos disponibles para el período y filtros seleccionados.</strong></p>
            </div>
        <?php } ?>
    </div>
</div>
                        <!-- Tabla de Detalle -->
                        <div class="table-responsive">
                            <table class="table table-striped" id="tabla-detalle">
                                <thead>
                                    <tr>
                                        <th>CI</th>
                                        <th>Lector</th>
                                        <th>Ocupación</th>
                                        <th>Publicación</th>
                                        <th>Fecha Publicación</th>
                                        <th>Fecha Préstamo</th>
                                        <th>Fecha Devolución</th>
                                        <th>Estado</th>
                                        <th>Días</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($detalles as $detalle): ?>
                                    <tr>
                                        <td><?php echo $detalle->carnet; ?></td>
                                        <td><?php echo htmlspecialchars($detalle->nombres . ' ' . $detalle->apellidoPaterno); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->profesion); ?></td>
                                        <td><?php echo htmlspecialchars($detalle->titulo_publicacion); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($detalle->fechaPublicacion)); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($detalle->fechaPrestamo)); ?></td>
                                        <td><?php echo $detalle->fechaDevolucion ? date('d/m/Y', strtotime($detalle->fechaDevolucion)) : 'En préstamo'; ?></td>
                                        <td>
                                            <span class="badge <?php 
                                                echo $detalle->estado_devolucion == 'Bueno' ? 'badge-success' :
                                                    ($detalle->estado_devolucion == 'Dañado' ? 'badge-warning' : 'badge-danger'); 
                                            ?>">
                                                <?php echo $detalle->estado_devolucion; ?>
                                            </span>
                                        </td>
                                        <td class="text-right"><?php echo $detalle->dias_prestamo; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
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
 </script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable
    $('#tabla-detalle').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[3, "desc"]],
        "pageLength": 10
    });

    // Configurar gráfico
    var ctx = document.getElementById('profesionesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($estadisticas, 'profesion')); ?>,
            datasets: [{
                label: 'Total Préstamos',
                data: <?php echo json_encode(array_column($estadisticas, 'total_prestamos')); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
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

// Funciones de exportación
function exportarPDF() {
    var form = document.getElementById('formFiltros');
    var url = new URL(window.location.href);
    url.searchParams.set('export', 'pdf');
    window.location.href = url.toString();
}

function exportarExcel() {
    var form = document.getElementById('formFiltros');
    var url = new URL(window.location.href);
    url.searchParams.set('export', 'excel');
    window.location.href = url.toString();
}
</script>
<script>
// Validación de fechas
function validarFechas() {
    var fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
    var fechaFin = document.querySelector('input[name="fecha_fin"]').value;
    
    if (fechaInicio && fechaFin) {
        if (new Date(fechaFin) < new Date(fechaInicio)) {
            alert('La fecha final no puede ser menor a la fecha inicial');
            return false;
        }
    }
    return true;
}

// Mejorar las funciones de exportación
function exportarPDF() {
    if (!validarFechas()) return;
    
    var form = document.getElementById('formFiltros');
    var profesion = document.getElementById('selectProfesion').value;
    
    // Crear form temporal para envío
    var tempForm = document.createElement('form');
    tempForm.method = 'GET';
    tempForm.action = form.action;
    
    // Agregar campos actuales
    var fechaInicio = document.createElement('input');
    fechaInicio.type = 'hidden';
    fechaInicio.name = 'fecha_inicio';
    fechaInicio.value = document.querySelector('input[name="fecha_inicio"]').value;
    tempForm.appendChild(fechaInicio);
    
    var fechaFin = document.createElement('input');
    fechaFin.type = 'hidden';
    fechaFin.name = 'fecha_fin';
    fechaFin.value = document.querySelector('input[name="fecha_fin"]').value;
    tempForm.appendChild(fechaFin);
    
    if (profesion) {
        var profInput = document.createElement('input');
        profInput.type = 'hidden';
        profInput.name = 'profesion';
        profInput.value = profesion;
        tempForm.appendChild(profInput);
    }
    
    // Agregar campo de exportación
    var exportField = document.createElement('input');
    exportField.type = 'hidden';
    exportField.name = 'export';
    exportField.value = 'pdf';
    tempForm.appendChild(exportField);
    
    document.body.appendChild(tempForm);
    tempForm.submit();
    document.body.removeChild(tempForm);
}

function exportarExcel() {
    if (!validarFechas()) return;
    
    var form = document.getElementById('formFiltros');
    var profesion = document.getElementById('selectProfesion').value;
    
    // Similar a exportarPDF pero cambiando el tipo de exportación
    var tempForm = document.createElement('form');
    tempForm.method = 'GET';
    tempForm.action = form.action;
    
    // Copiar campos existentes
    var fechaInicio = document.createElement('input');
    fechaInicio.type = 'hidden';
    fechaInicio.name = 'fecha_inicio';
    fechaInicio.value = document.querySelector('input[name="fecha_inicio"]').value;
    tempForm.appendChild(fechaInicio);
    
    var fechaFin = document.createElement('input');
    fechaFin.type = 'hidden';
    fechaFin.name = 'fecha_fin';
    fechaFin.value = document.querySelector('input[name="fecha_fin"]').value;
    tempForm.appendChild(fechaFin);
    
    if (profesion) {
        var profInput = document.createElement('input');
        profInput.type = 'hidden';
        profInput.name = 'profesion';
        profInput.value = profesion;
        tempForm.appendChild(profInput);
    }
    
    var exportField = document.createElement('input');
    exportField.type = 'hidden';
    exportField.name = 'export';
    exportField.value = 'excel';
    tempForm.appendChild(exportField);
    
    document.body.appendChild(tempForm);
    tempForm.submit();
    document.body.removeChild(tempForm);
}

// Inicialización de DataTables con opciones mejoradas
$(document).ready(function() {
    var tabla = $('#tabla-detalle').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "pageLength": 10,
        "order": [[3, "desc"]],
        "responsive": true,
        "dom": 'Bfrtip',
        "buttons": [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        "initComplete": function(settings, json) {
            // Ajustar columnas automáticamente
            this.api().columns.adjust();
        }
    });
});
</script>