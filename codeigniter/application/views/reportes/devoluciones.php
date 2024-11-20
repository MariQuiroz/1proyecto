<div class="content-page">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Análisis de Devoluciones</h4>
                    
                    <!-- Filtros -->
                    <form method="GET" class="mt-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fecha Inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control" 
                                           value="<?php echo isset($filtros['fecha_inicio']) ? $filtros['fecha_inicio'] : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fecha Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control"
                                           value="<?php echo isset($filtros['fecha_fin']) ? $filtros['fecha_fin'] : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">Filtrar</button>
                                        <a href="?export=pdf" class="btn btn-danger">Exportar PDF</a>
                                        <a href="?export=excel" class="btn btn-success">Exportar Excel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="card-body">
                    <!-- Indicadores clave -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>Estado Bueno</h5>
                                    <h2><?php echo array_sum(array_column($estadisticas, 'estado_bueno')); ?></h2>
                                    <small>Total devoluciones en buen estado</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>Estado Dañado</h5>
                                    <h2><?php echo array_sum(array_column($estadisticas, 'estado_dañado')); ?></h2>
                                    <small>Total devoluciones con daños</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5>Estado Perdido</h5>
                                    <h2><?php echo array_sum(array_column($estadisticas, 'estado_perdido')); ?></h2>
                                    <small>Total publicaciones perdidas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>Devoluciones Tardías</h5>
                                    <h2><?php echo array_sum(array_column($estadisticas, 'devoluciones_tardias')); ?></h2>
                                    <small>Total devoluciones fuera de tiempo</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gráfico de tendencias -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <canvas id="devolucionesChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Tabla de datos -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Mes/Año</th>
                                    <th>Total Devoluciones</th>
                                    <th>Estado Bueno</th>
                                    <th>Estado Dañado</th>
                                    <th>Estado Perdido</th>
                                    <th>Prom. Días</th>
                                    <th>Devoluciones Tardías</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estadisticas as $est): ?>
                                    <tr>
                                        <td><?php echo $est->mes . '/' . $est->anio; ?></td>
                                        <td class="text-right"><?php echo $est->total_devoluciones; ?></td>
                                        <td class="text-right"><?php echo $est->estado_bueno; ?></td>
                                        <td class="text-right"><?php echo $est->estado_dañado; ?></td>
                                        <td class="text-right"><?php echo $est->estado_perdido; ?></td>
                                        <td class="text-right"><?php echo number_format($est->promedio_dias_prestamo, 1); ?></td>
                                        <td class="text-right"><?php echo $est->devoluciones_tardias; ?></td>
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
