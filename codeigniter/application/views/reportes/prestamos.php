<!-- views/reportes/prestamos.php -->
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <!-- Título y breadcrumb se mantienen igual -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo site_url('usuarios/panel'); ?>">Inicio</a></li>
                                <li class="breadcrumb-item active">Reportes de Préstamos</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Reporte de Préstamos</h4>
                    </div>
                </div>
            </div>

            <!-- Cards de Resumen -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card-box bg-success text-white">
                        <div class="card-body">
                            <div class="float-right">
                                <i class="fe-book font-24"></i>
                            </div>
                            <h5 class="text-white-50 text-uppercase mb-3">Préstamos Activos</h5>
                            <h2 class="mb-3 text-white counter"><?php echo isset($estadisticas->activos) ? $estadisticas->activos : 0; ?></h2>
                            <p class="text-white-50 mb-1">
                                <i class="fe-info mr-1"></i> Préstamos en curso
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-box bg-info text-white">
                        <div class="card-body">
                            <div class="float-right">
                                <i class="fe-check-circle font-24"></i>
                            </div>
                            <h5 class="text-white-50 text-uppercase mb-3">Préstamos Devueltos</h5>
                            <h2 class="mb-3 text-white counter"><?php echo isset($estadisticas->devueltos) ? $estadisticas->devueltos : 0; ?></h2>
                            <p class="text-white-50 mb-1">
                                <i class="fe-info mr-1"></i> Completados exitosamente
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-box bg-danger text-white">
                        <div class="card-body">
                            <div class="float-right">
                                <i class="fe-alert-triangle font-24"></i>
                            </div>
                            <h5 class="text-white-50 text-uppercase mb-3">Préstamos Vencidos</h5>
                            <h2 class="mb-3 text-white counter"><?php echo isset($estadisticas->vencidos) ? $estadisticas->vencidos : 0; ?></h2>
                            <p class="text-white-50 mb-1">
                                <i class="fe-info mr-1"></i> Requieren atención
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros Mejorados -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?php 
                            $attributes = ['method' => 'get', 'class' => 'row align-items-center', 'id' => 'form-filtros'];
                            echo form_open('reportes/prestamos', $attributes); 
                            ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_inicio">Fecha Inicio</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fe-calendar"></i></span>
                                            </div>
                                            <?php echo form_input([
                                                'type' => 'date',
                                                'name' => 'fecha_inicio',
                                                'id' => 'fecha_inicio',
                                                'class' => 'form-control',
                                                'value' => set_value('fecha_inicio', $filtros['fecha_inicio']),
                                                'max' => date('Y-m-d')
                                            ]); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_fin">Fecha Fin</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fe-calendar"></i></span>
                                            </div>
                                            <?php echo form_input([
                                                'type' => 'date',
                                                'name' => 'fecha_fin',
                                                'id' => 'fecha_fin',
                                                'class' => 'form-control',
                                                'value' => set_value('fecha_fin', $filtros['fecha_fin']),
                                                'max' => date('Y-m-d')
                                            ]); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="estado">Estado</label>
                                        <?php echo form_dropdown('estado', 
                                            array_merge([''=>'Todos'], $estados_prestamo),
                                            set_value('estado', $filtros['estado']),
                                            'class="form-control select2" id="estado"'
                                        ); ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="id_encargado">Encargado</label>
                                        <?php 
                                        $opciones_encargados = array_reduce($encargados, function($carry, $item) {
                                            $carry[$item->idUsuario] = $item->nombres . ' ' . $item->apellidoPaterno;
                                            return $carry;
                                        }, [''=>'Todos']);
                                        echo form_dropdown('id_encargado', 
                                            $opciones_encargados,
                                            set_value('id_encargado', $filtros['id_encargado']),
                                            'class="form-control select2" id="id_encargado"'
                                        ); ?>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fe-filter mr-1"></i> Filtrar
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="btn-limpiar">
                                            <i class="fe-refresh-cw mr-1"></i> Limpiar
                                        </button>
                                        <div class="btn-group">
    <a href="<?php echo site_url('reportes/exportar_prestamos').'?'.http_build_query($filtros).'&formato=pdf'; ?>" 
       class="btn btn-danger">
        <i class="fe-file-text mr-1"></i> Exportar PDF
    </a>
    <a href="<?php echo site_url('reportes/exportar_prestamos').'?'.http_build_query($filtros).'&formato=excel'; ?>" 
       class="btn btn-success">
        <i class="fe-file mr-1"></i> Exportar Excel
    </a>
</div>
                                    </div>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Resultados Mejorada -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title d-inline-block mb-3">Lista de Préstamos</h4>
                            <div class="table-responsive">
                                <table id="prestamos-table" class="table table-striped table-bordered dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th width="5%">ID</th>
                                            <th width="15%">Fecha</th>
                                            <th width="20%">Usuario</th>
                                            <th width="25%">Publicación</th>
                                            <th width="10%">Estado</th>
                                            <th width="15%">Encargado</th>
                                            <th width="10%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($prestamos as $prestamo): ?>
                                            <tr>
                                                <td><?php echo $prestamo->idPrestamo; ?></td>
                                                <td>
                                                    <i class="fe-clock mr-1"></i>
                                                    <?php echo date('d/m/Y H:i', strtotime($prestamo->fechaPrestamo)); ?>
                                                </td>
                                                <td>
                                                    <i class="fe-user mr-1"></i>
                                                    <?php echo htmlspecialchars($prestamo->nombres.' '.$prestamo->apellidoPaterno); ?>
                                                </td>
                                                <td>
                                                    <i class="fe-book-open mr-1"></i>
                                                    <?php echo htmlspecialchars($prestamo->titulo); ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    $badge_class = '';
                                                    $icon_class = '';
                                                    switch($prestamo->estado_prestamo) {
                                                        case 'Activo':
                                                            $badge_class = 'badge-success';
                                                            $icon_class = 'fe-check-circle';
                                                            break;
                                                        case 'Devuelto':
                                                            $badge_class = 'badge-info';
                                                            $icon_class = 'fe-check-square';
                                                            break;
                                                        case 'Vencido':
                                                            $badge_class = 'badge-danger';
                                                            $icon_class = 'fe-alert-triangle';
                                                            break;
                                                        default:
                                                            $badge_class = 'badge-secondary';
                                                            $icon_class = 'fe-help-circle';
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $badge_class; ?>">
                                                        <i class="<?php echo $icon_class; ?> mr-1"></i>
                                                        <?php echo $prestamo->estado_prestamo; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="fe-user-check mr-1"></i>
                                                    <?php echo htmlspecialchars($prestamo->nombre_encargado.' '.$prestamo->apellido_encargado); ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <a href="<?php echo site_url('prestamos/detalle/'.$prestamo->idPrestamo); ?>" 
                                                           class="btn btn-sm btn-info" 
                                                           data-toggle="tooltip" 
                                                           title="Ver Detalle">
                                                            <i class="fe-eye"></i>
                                                        </a>
                                                        <?php if($prestamo->estado_prestamo == 'Activo'): ?>
                                                            <a href="<?php echo site_url('prestamos/devolver/'.$prestamo->idPrestamo); ?>" 
                                                               class="btn btn-sm btn-success"
                                                               data-toggle="tooltip"
                                                               title="Registrar Devolución">
                                                                <i class="fe-check-circle"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
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
</div>

<!-- Scripts específicos para esta vista -->
<script>
$(document).ready(function() {
    // Inicializar Select2
    $('.select2').select2();

    // Inicialización de DataTables mejorada
    if ($.fn.DataTable.isDataTable('#prestamos-table')) {
        $('#prestamos-table').DataTable().destroy();
    }
    
    $('#prestamos-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        },
        responsive: true,
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        buttons: [
            {
                extend: 'collection',
                text: '<i class="fe-download mr-1"></i> Exportar',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fe-file-text mr-1"></i> Excel',
                        className: 'btn-success',
                        exportOptions: {
                            columns: [0,1,2,3,4,5]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fe-file mr-1"></i> PDF',
                        className: 'btn-danger',
                        exportOptions: {
                            columns: [0,1,2,3,4,5]
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fe-printer mr-1"></i> Imprimir',
                        className: 'btn-info',
                        exportOptions: {
                            columns: [0,1,2,3,4,5]
                        }
                    }
                ],
                className: 'btn btn-primary'
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        stateSave: true,
        columnDefs: [
            {
                targets: [-1],
                orderable: false,
                searchable: false
            }
        ],
        drawCallback: function() {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    // Validación de fechas mejorada
    $('#fecha_inicio, #fecha_fin').on('change', function() {
        var fecha_inicio = $('#fecha_inicio').val();
        var fecha_fin = $('#fecha_fin').val();
        
        if(fecha_inicio && fecha_fin) {
            if(fecha_fin < fecha_inicio) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en fechas',
                    text: 'La fecha fin no puede ser menor a la fecha inicio',
                    confirmButtonClass: 'btn btn-danger'
                });
                $(this).val('');
            }
        }
    });

    // Botón limpiar filtros
    $('#btn-limpiar').on('click', function() {
        $('#fecha_inicio').val('');
        $('#fecha_fin').val('');
        $('.select2').val('').trigger('change');
        window.location.href = '<?php echo site_url("reportes/prestamos"); ?>';
    });

    // Animación para contadores
    $('.counter').each(function() {
        $(this).prop('Counter', 0).animate({
            Counter: $(this).text()
        }, {
            duration: 2000,
            easing: 'swing',
            step: function(now) {
                $(this).text(Math.ceil(now));
            }
        });
    });

    // Tooltip mejorado para botones de acción
    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover',
        placement: 'top',
        template: '<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner bg-dark"></div></div>'
    });

    // Manejo de exportación
    $('#btn-exportar').on('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Exportar Reporte',
            text: '¿En qué formato deseas exportar el reporte?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Excel',
            cancelButtonText: 'PDF',
            showDenyButton: true,
            denyButtonText: 'CSV',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger',
            denyButtonClass: 'btn btn-info'
        }).then((result) => {
            var baseUrl = $(this).attr('href');
            var formato = '';
            
            if (result.isConfirmed) {
                formato = 'excel';
            } else if (result.isDenied) {
                formato = 'csv';
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                formato = 'pdf';
            } else {
                return;
            }
            
            window.location.href = baseUrl + '&formato=' + formato;
        });
    });

    // Resaltado de filas según estado
    $('#prestamos-table tbody tr').each(function() {
        var estado = $(this).find('td:eq(4) .badge').text().trim();
        switch(estado) {
            case 'Vencido':
                $(this).addClass('table-danger');
                break;
            case 'Activo':
                $(this).addClass('table-success');
                break;
            case 'Devuelto':
                $(this).addClass('table-info');
                break;
        }
    });

    // Recargar automática de datos
    setInterval(function() {
        var table = $('#prestamos-table').DataTable();
        table.ajax.reload(null, false); // false = mantener la página actual
    }, 300000); // Recargar cada 5 minutos

    // Manejo de errores en la carga de datos
    $.fn.dataTable.ext.errMode = 'none';
    $('#prestamos-table').on('error.dt', function(e, settings, techNote, message) {
        console.error('Error en DataTable:', message);
        Swal.fire({
            icon: 'error',
            title: 'Error de carga',
            text: 'Hubo un error al cargar los datos. Por favor, recarga la página.',
            confirmButtonClass: 'btn btn-danger'
        });
    });

    // Responsive comportamiento personalizado
    $('#prestamos-table').on('responsive-display', function(e, datatable, row, showHide, update) {
        if(showHide) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
});

// Funciones auxiliares
function formatearFecha(fecha) {
    if (!fecha) return '';
    var d = new Date(fecha);
    return ('0' + d.getDate()).slice(-2) + '/' + 
           ('0' + (d.getMonth()+1)).slice(-2) + '/' + 
           d.getFullYear() + ' ' +
           ('0' + d.getHours()).slice(-2) + ':' +
           ('0' + d.getMinutes()).slice(-2);
}

function formatearEstado(estado) {
    switch(estado) {
        case 'Activo':
            return '<span class="badge badge-success"><i class="fe-check-circle mr-1"></i>' + estado + '</span>';
        case 'Devuelto':
            return '<span class="badge badge-info"><i class="fe-check-square mr-1"></i>' + estado + '</span>';
        case 'Vencido':
            return '<span class="badge badge-danger"><i class="fe-alert-triangle mr-1"></i>' + estado + '</span>';
        default:
            return '<span class="badge badge-secondary"><i class="fe-help-circle mr-1"></i>' + estado + '</span>';
    }
}
</script>