
<body>
    <div id="wrapper">
        <!-- Topbar Start -->
        <div class="navbar-custom">
            <!-- LOGO -->
            <div class="logo-box">
                <a href="<?php echo site_url('lector/panel'); ?>" class="logo text-center">
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
                        <!-- item-->
                        <div class="dropdown-item noti-title">
                            <h6 class="m-0">
                                ¡Bienvenido!
                            </h6>
                        </div>

                        <!-- item-->
                        <a href="<?php echo site_url('usuarios/perfil'); ?>" class="dropdown-item notify-item">
                            <i class="fe-user"></i>
                            <span>Mi Perfil</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <!-- item-->
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
                                <h4 class="page-title">Panel de Lectorrrrrrrrrrr</h4>
                            </div>
                        </div>
                    </div>     
                    <!-- end page title --> 

                    

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card-box">
                                <h4 class="header-title mb-3">Publicaciones Disponibles</h4>

                                <div class="table-responsive">
                                    <table class="table table-hover table-centered m-0">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Autor</th>
                                                <th>Tipo</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($publicaciones as $publicacion): ?>
                                            <tr>
                                                <td><?php echo $publicacion->titulo; ?></td>
                                                <td><?php echo $publicacion->autor; ?></td>
                                                <td><?php echo $publicacion->tipo; ?></td>
                                                <td>
                                                    <a href="<?php echo site_url('publicaciones/ver/'.$publicacion->idPublicacion); ?>" class="btn btn-xs btn-info">Ver</a>
                                                    <a href="<?php echo site_url('prestamos/solicitar/'.$publicacion->idPublicacion); ?>" class="btn btn-xs btn-primary">Solicitar</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                                <th>Fecha de Devolución</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($prestamos_activos as $prestamo): ?>
                                            <tr>
                                                <td><?php echo $prestamo->titulo; ?></td>
                                                <td><?php echo $prestamo->fechaInicio; ?></td>
                                                <td><?php echo $prestamo->fechaDevolucion; ?></td>
                                                <td><span class="badge badge-success">Activo</span></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="card-box">
                                <h4 class="header-title mb-3">Mis Reservas Pendientes</h4>

                                <div class="table-responsive">
                                    <table class="table table-hover table-centered m-0">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Fecha de Reserva</th>
                                                <th>Estado</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($reservas_pendientes as $reserva): ?>
                                            <tr>
                                                <td><?php echo $reserva->titulo; ?></td>
                                                <td><?php echo $reserva->fechaReserva; ?></td>
                                                <td><span class="badge badge-warning">Pendiente</span></td>
                                                <td>
                                                    <a href="<?php echo site_url('reservas/cancelar/'.$reserva->idReserva); ?>" class="btn btn-xs btn-danger">Cancelar</a>
                                                </td>
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

            