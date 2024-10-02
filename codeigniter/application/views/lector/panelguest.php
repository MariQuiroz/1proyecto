<body>
    <div id="wrapper">
        <!-- Topbar Start -->
        <div class="navbar-custom">
            <!-- LOGO -->
            <div class="logo-box">
                <a href="<?php echo site_url('usuarios/lector'); ?>" class="logo text-center">
                    <span class="logo-lg">
                        <img src="<?php echo base_url('assets/images/logo-light.png'); ?>" alt="" height="16">
                    </span>
                    <span class="logo-sm">
                        <img src="<?php echo base_url('assets/images/logo-sm.png'); ?>" alt="" height="24">
                    </span>
                </a>
            </div>

            <ul class="list-unstyled topnav-menu float-right mb-0">
                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="<?php echo base_url('assets/images/users/avatar-1.jpg'); ?>" alt="user-image" class="rounded-circle">
                        <span class="pro-user-name ml-1">
                            <?php echo $this->session->userdata('nombres'); ?> <i class="mdi mdi-chevron-down"></i> 
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                        <div class="dropdown-item noti-title">
                            <h6 class="m-0">¡Bienvenido!</h6>
                        </div>

                        <a href="<?php echo site_url('usuarios/perfil'); ?>" class="dropdown-item notify-item">
                            <i class="fe-user"></i>
                            <span>Mi Perfil</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <a href="<?php echo site_url('usuarios/logout'); ?>" class="dropdown-item notify-item">
                            <i class="fe-log-out"></i>
                            <span>Cerrar Sesión</span>
                        </a>
                    </div>
                </li>
            </ul>

            <div class="clearfix"></div>
        </div>
        <!-- end Topbar -->

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
</body>