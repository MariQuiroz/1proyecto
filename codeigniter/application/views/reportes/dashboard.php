<div class="content-page">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Panel de Reportes Analíticos</h4>
                </div>
                <div class="card-body">
                    <!-- Métricas Generales -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>Usuarios Activos</h5>
                                    <h2><?php echo $metricas['usuarios_activos']; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>Tasa de Aprobación</h5>
                                    <h2><?php echo $metricas['tasa_aprobacion']; ?>%</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>Promedio Días Préstamo</h5>
                                    <h2><?php echo $metricas['promedio_dias_prestamo']; ?> días</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enlaces a Reportes -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Análisis por Ocupación</h5>
                                    <p>Comportamiento de lectores según su profesión</p>
                                    <a href="<?php echo site_url('reportes/por_profesion'); ?>" class="btn btn-primary">Ver Reporte</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Estado de Solicitudes</h5>
                                    <p>Análisis de aprobaciones y rechazos</p>
                                    <a href="<?php echo site_url('reportes/solicitudes'); ?>" class="btn btn-primary">Ver Reporte</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Tipos de Publicaciones</h5>
                                    <p>Análisis por tipo de material</p>
                                    <a href="<?php echo site_url('reportes/tipos_publicaciones'); ?>" class="btn btn-primary">Ver Reporte</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Devoluciones</h5>
                                    <p>Análisis del comportamiento de devoluciones</p>
                                    <a href="<?php echo site_url('reportes/devoluciones'); ?>" class="btn btn-primary">Ver Reporte</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>