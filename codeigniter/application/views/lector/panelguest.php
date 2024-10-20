<div id="wrapper">
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Panel de Lector</h4>
                        </div>
                    </div>
                </div>     
                <!-- end page title --> 
                <!-- Mis Préstamos Activos -->
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card-box">
                            <h4 class="header-title mb-3">Mis Préstamos Activos</h4>

                            <div class="table-responsive">
                                <table class="table table-hover table-centered m-0">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th>Fecha de Préstamo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($prestamos_activos as $prestamo): ?>
                                        <tr>
                                            <td><?php echo $prestamo->titulo; ?></td>
                                            <td><?php echo $prestamo->fechaCreacion; ?></td>
                                            <td><span class="badge badge-success">Activo</span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Mis Solicitudes Pendientes -->
                    <div class="col-xl-6">
                        <div class="card-box">
                            <h4 class="header-title mb-3">Mis Solicitudes Pendientes</h4>

                            <div class="table-responsive">
                                <table class="table table-hover table-centered m-0">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th>Fecha de Solicitud</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($solicitudes_pendientes as $solicitud): ?>
                                        <tr>
                                            <td><?php echo $solicitud->titulo; ?></td>
                                            <td><?php echo $solicitud->fechaSolicitud; ?></td>
                                            <td><span class="badge badge-warning">Pendiente</span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- container -->

        </div> <!-- content -->
    </div>
</div>
