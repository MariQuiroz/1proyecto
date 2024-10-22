<!-- Start Page Content here -->
<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="container mt-4">
                <h2>Dashboard</h2>
                
                <?php if ($this->session->userdata('rol') == 'administrador'): ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total de Usuarios</h5>
                                    <p class="card-text"><?= htmlspecialchars($total_usuarios); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total de Publicaciones</h5>
                                    <p class="card-text"><?= htmlspecialchars($total_publicaciones); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Préstamos Activos</h5>
                                    <p class="card-text"><?= htmlspecialchars($prestamos_activos); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h3>Préstamos Vencidos</h3>
                        <?php if (empty($prestamos_vencidos)): ?>
                            <p>No hay préstamos vencidos.</p>
                        <?php else: ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Publicación</th>
                                        <th>Fecha de Vencimiento</th>
                                        <th>Días de Retraso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($prestamos_vencidos as $prestamo): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($prestamo->nombres . ' ' . $prestamo->apellidoPaterno); ?></td>
                                            <td><?= htmlspecialchars($prestamo->titulo); ?></td>
                                            <td><?= date('d/m/Y', strtotime($prestamo->fechaDevolucionEsperada)); ?></td>
                                            <td><?= floor((time() - strtotime($prestamo->fechaDevolucionEsperada)) / (60 * 60 * 24)); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <!-- El contenido para otros roles permanece igual -->
                <?php endif; ?>
            </div>           
        </div> <!-- container -->
    </div> <!-- content -->
</div> <!-- content-page -->
<!-- End Page content -->