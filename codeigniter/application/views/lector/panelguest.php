
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
                        <h4 class="header-title mb-3">Préstamos Activos</h4>

<?php if (!empty($prestamos_activos)): ?>
    <div class="table-responsive">
        <table class="table table-centered table-nowrap table-hover mb-0">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Editorial</th>
                    <th>Tipo</th>
                    <th>Fecha y Hora</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestamos_activos as $prestamo): ?>
                    <tr>
                        <td><?= htmlspecialchars($prestamo->titulo) ?></td>
                        <td><?= htmlspecialchars($prestamo->nombreEditorial) ?></td>
                        <td><?= htmlspecialchars($prestamo->nombreTipo) ?></td>
                        <td>
                            <div class="font-weight-medium">
                                <?= date('d/m/Y', strtotime($prestamo->fechaPrestamo)) ?>
                            </div>
                            <div class="text-muted">
                                <small>
                                    <i class="mdi mdi-clock-outline"></i>
                                    <?= date('H:i', strtotime($prestamo->horaInicio)) ?> hrs
                                </small>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-success">
                                <i class="mdi mdi-check-circle"></i> Activo
                            </span>
                        </td>
                        <td>
                            <a href="<?= site_url('prestamos/detalle/'.$prestamo->idPrestamo) ?>" 
                               class="btn btn-info btn-sm">
                                <i class="mdi mdi-eye"></i> Ver detalles
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info mb-0">
        <div class="d-flex align-items-center">
            <i class="mdi mdi-information-outline h2 mb-0 mr-2"></i>
            <div>
                <h5 class="mt-0">Sin préstamos activos</h5>
                <p class="mb-0">No tienes préstamos activos en este momento.</p>
            </div>
        </div>
    </div>
<?php endif; ?>
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
