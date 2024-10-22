
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <!-- Start Page Title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Panel de Lector</h4>
                        </div>
                    </div>
                </div>     
                <!-- End Page Title --> 

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
                                        <?php if (empty($prestamos_activos)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center">No tienes préstamos activos.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($prestamos_activos as $prestamo): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($prestamo->titulo); ?></td>
                                                <td><?php echo htmlspecialchars($prestamo->fechaCreacion); ?></td>
                                                <td><span class="badge badge-success">Activo</span></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
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
                                        <?php if (empty($solicitudes_pendientes)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center">No tienes solicitudes pendientes.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($solicitudes_pendientes as $solicitud): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($solicitud->titulo); ?></td>
                                                <td><?php echo htmlspecialchars($solicitud->fechaSolicitud); ?></td>
                                                <td><span class="badge badge-warning">Pendiente</span></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Row -->

            </div> <!-- End container -->

        </div> <!-- End content -->
    </div>
