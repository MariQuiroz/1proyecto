 <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Xeria</a></li>
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboards</a></li>
                                            <li class="breadcrumb-item active">Dashboard</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Dashboard</h4>
                                </div>
                            </div>
                        </div>     
                        <!-- end page title --> 
                       <?php echo form_open_multipart('usuarios/logout'); ?>
        <button type="submit" name="buton2" class="btn btn-danger">CERRAR SESIÓN</button>
      <?php echo form_close(); ?>

      
                       <!-- application/views/dashboard.php -->
<div class="container mt-4">
    <h2>Dashboard</h2>
    
    <?php if ($this->session->userdata('rol') == 'administrador'): ?>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total de Usuarios</h5>
                        <p class="card-text"><?= $total_usuarios ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total de Publicaciones</h5>
                        <p class="card-text"><?= $total_publicaciones ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Préstamos Activos</h5>
                        <p class="card-text"><?= $prestamos_activos ?></p>
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
                                <td><?= $prestamo->nombres . ' ' . $prestamo->apellidoPaterno ?></td>
                                <td><?= $prestamo->titulo ?></td>
                                <td><?= date('d/m/Y', strtotime($prestamo->fechaDevolucionEsperada)) ?></td>
                                <td><?= floor((time() - strtotime($prestamo->fechaDevolucionEsperada)) / (60 * 60 * 24)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Mis Préstamos Activos</h5>
                        <p class="card-text"><?= $mis_prestamos_activos ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Mis Reservas Pendientes</h5>
                        <p class="card-text"><?= $mis_reservas_pendientes ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h3>Mis Próximas Devoluciones</h3>
            <?php if (empty($mis_proximas_devoluciones)): ?>
                <p>No tienes devoluciones próximas.</p>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Publicación</th>
                            <th>Fecha de Devolución</th>
                            <th>Días Restantes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mis_proximas_devoluciones as $devolucion): ?>
                            <tr>
                                <td><?= $devolucion->titulo ?></td>
                                <td><?= date('d/m/Y', strtotime($devolucion->fechaDevolucionEsperada)) ?></td>
                                <td><?= max(0, floor((strtotime($devolucion->fechaDevolucionEsperada) - time()) / (60 * 60 * 24))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>           
                           

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->



                        </div>
                        
                    </div> <!-- container -->

                </div> <!-- content -->

               