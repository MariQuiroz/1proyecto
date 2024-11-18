<!-- application/views/reportes/prestamos.php -->
<div class="content-page">
    <section class="content-header">
        <h1>Reporte de Préstamos</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Filtros de Búsqueda</h3>
                    </div>
                    <div class="box-body">
                        <!-- Formulario de filtros -->
                        <form method="GET" action="<?php echo site_url('reportes/prestamos'); ?>" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha Inicio:</label>
                                        <input type="date" name="fecha_inicio" class="form-control" 
                                               value="<?php echo set_value('fecha_inicio', isset($filtros['fecha_inicio']) ? $filtros['fecha_inicio'] : ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha Fin:</label>
                                        <input type="date" name="fecha_fin" class="form-control"
                                               value="<?php echo set_value('fecha_fin', isset($filtros['fecha_fin']) ? $filtros['fecha_fin'] : ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Estado:</label>
                                        <select name="estado" class="form-control">
                                            <option value="">Todos</option>
                                            <?php foreach($estados_prestamo as $key => $value): ?>
                                                <option value="<?php echo $key; ?>" 
                                                    <?php echo isset($filtros['estado']) && $filtros['estado'] == $key ? 'selected' : ''; ?>>
                                                    <?php echo $value; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <?php if(isset($encargados) && $this->session->userdata('rol') === 'administrador'): ?>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Encargado:</label>
                                            <select name="id_encargado" class="form-control">
                                                <option value="">Todos</option>
                                                <?php foreach($encargados as $encargado): ?>
                                                    <option value="<?php echo $encargado->idUsuario; ?>"
                                                        <?php echo isset($filtros['id_encargado']) && $filtros['id_encargado'] == $encargado->idUsuario ? 'selected' : ''; ?>>
                                                        <?php echo $encargado->nombres . ' ' . $encargado->apellidoPaterno; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fa fa-search"></i> Filtrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Resumen de Estadísticas -->
                        <div class="row">
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <h3><?php echo $estadisticas->activos ?? 0; ?></h3>
                                        <p>Préstamos Activos</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-book"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <h3><?php echo $estadisticas->devueltos ?? 0; ?></h3>
                                        <p>Préstamos Devueltos</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-check"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3><?php echo $estadisticas->vencidos ?? 0; ?></h3>
                                        <p>Préstamos Vencidos</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-red">
                                    <div class="inner">
                                        <h3><?php echo isset($estadisticas->promedio_horas_prestamo) ? 
                                            round($estadisticas->promedio_horas_prestamo, 1) : 0; ?></h3>
                                        <p>Promedio Horas</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-clock"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de Préstamos -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th>Publicación</th>
                                        <th>Estado</th>
                                        <th>Encargado</th>
                                        <th>Tiempo</th>
                                        <?php if($this->session->userdata('rol') === 'administrador'): ?>
                                            <th>Acciones</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($prestamos)): ?>
                                        <?php foreach($prestamos as $prestamo): ?>
                                            <tr>
                                                <td><?php echo $prestamo->idPrestamo; ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($prestamo->fechaPrestamo)); ?></td>
                                                <td><?php echo $prestamo->nombres . ' ' . $prestamo->apellidoPaterno; ?></td>
                                                <td><?php echo $prestamo->titulo; ?></td>
                                                <td>
                                                    <span class="badge <?php 
                                                        echo $prestamo->estado_prestamo == 'Activo' ? 'bg-green' : 
                                                            ($prestamo->estado_prestamo == 'Vencido' ? 'bg-red' : 'bg-blue'); 
                                                    ?>">
                                                        <?php echo $prestamo->estado_prestamo; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $prestamo->nombre_encargado . ' ' . $prestamo->apellido_encargado; ?></td>
                                                <td><?php echo $prestamo->tiempo_prestamo; ?> minutos</td>
                                                <?php if($this->session->userdata('rol') === 'administrador'): ?>
                                                    <td>
                                                        <button type="button" class="btn btn-xs btn-info" 
                                                                onclick="verDetalle(<?php echo $prestamo->idPrestamo; ?>)">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No se encontraron registros</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Botones de Exportación -->
                        <?php if(!empty($prestamos)): ?>
                            <div class="mt-3">
                                <a href="<?php echo site_url('reportes/exportar_prestamos?formato=pdf&' . http_build_query($filtros)); ?>" 
                                   class="btn btn-danger">
                                    <i class="fa fa-file-pdf"></i> Exportar PDF
                                </a>
                                <a href="<?php echo site_url('reportes/exportar_prestamos?formato=excel&' . http_build_query($filtros)); ?>" 
                                   class="btn btn-success">
                                    <i class="fa fa-file-excel"></i> Exportar Excel
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Estilos CSS adicionales -->
<style>
.small-box {
    border-radius: 3px;
    position: relative;
    display: block;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.small-box > .inner {
    padding: 10px;
}

.small-box .icon {
    position: absolute;
    top: 5px;
    right: 10px;
    font-size: 64px;
    color: rgba(0,0,0,0.15);
}

.small-box h3 {
    font-size: 38px;
    margin: 0 0 10px 0;
    white-space: nowrap;
    padding: 0;
}

.bg-aqua { background-color: #00c0ef !important; color: #fff !important; }
.bg-green { background-color: #00a65a !important; color: #fff !important; }
.bg-yellow { background-color: #f39c12 !important; color: #fff !important; }
.bg-red { background-color: #dd4b39 !important; color: #fff !important; }

.badge {
    padding: 5px 10px;
    border-radius: 3px;
    font-size: 12px;
}

.bg-blue { background-color: #0073b7 !important; color: #fff !important; }

.table-responsive {
    margin-top: 20px;
}

.btn-xs {
    padding: 1px 5px;
    font-size: 12px;
    line-height: 1.5;
    border-radius: 3px;
}
</style>

<!-- Scripts específicos -->
<script>
$(document).ready(function() {
    $('.datatable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[0, "desc"]],
        "pageLength": 25
    });
});

function verDetalle(idPrestamo) {
    window.location.href = '<?php echo site_url("prestamos/detalle/"); ?>' + idPrestamo;
}
</script>