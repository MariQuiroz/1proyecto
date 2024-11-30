<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Total Usuarios -->
                <div class="col-xl-3 col-md-6">
                    <div class="widget-rounded-circle card-box bg-primary">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-lg rounded-circle bg-soft-light border-light border">
                                    <i class="fe-users font-22 avatar-title text-white"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="text-white mb-1 mt-1"><span data-plugin="counterup"><?php echo $total_usuarios; ?></span></h4>
                                <p class="text-white mb-1">Total Usuarios</p>
                                <p class="text-white-50 mb-0">Usuarios Registrados</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Publicaciones -->
                <div class="col-xl-3 col-md-6">
                    <div class="widget-rounded-circle card-box bg-success">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-lg rounded-circle bg-soft-light border-light border">
                                    <i class="fe-book font-22 avatar-title text-white"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="text-white mb-1 mt-1"><span data-plugin="counterup"><?php echo $total_publicaciones; ?></span></h4>
                                <p class="text-white mb-1">Total Publicaciones</p>
                                <p class="text-white-50 mb-0">Publicaciones Disponibles</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Préstamos Activos -->
                <div class="col-xl-3 col-md-6">
                    <div class="widget-rounded-circle card-box bg-warning">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-lg rounded-circle bg-soft-light border-light border">
                                    <i class="fe-bookmark font-22 avatar-title text-white"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="text-white mb-1 mt-1"><span data-plugin="counterup"><?php echo $prestamos_activos; ?></span></h4>
                                <p class="text-white mb-1">Préstamos Activos</p>
                                <p class="text-white-50 mb-0">En Circulación</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Préstamos No Devueltos -->
                <div class="col-xl-3 col-md-6">
                    <div class="widget-rounded-circle card-box bg-danger">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-lg rounded-circle bg-soft-light border-light border">
                                    <i class="fe-alert-triangle font-22 avatar-title text-white"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="text-white mb-1 mt-1"><span data-plugin="counterup"><?php echo $prestamos_no_devueltos; ?></span></h4>
                                <p class="text-white mb-1">No Devueltos</p>
                                <p class="text-white-50 mb-0">Préstamos Pendientes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Solicitudes Pendientes -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="widget-rounded-circle card-box bg-purple">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-lg rounded-circle bg-soft-light border-light border">
                                    <i class="fe-clock font-22 avatar-title text-white"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="text-white mb-1 mt-1"><span data-plugin="counterup"><?php echo $solicitudes_pendientes; ?></span></h4>
                                <p class="text-white mb-1">Solicitudes Pendientes</p>
                                <p class="text-white-50 mb-0">En Espera de Aprobación</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <h4 class="header-title mb-4">Acciones Rápidas</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <a href="<?php echo site_url('publicaciones/agregar'); ?>" class="btn btn-primary btn-lg btn-block waves-effect waves-light">
                                    <i class="fe-plus-circle mr-1"></i> Agregar Publicación
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo site_url('usuarios/agregar'); ?>" class="btn btn-success btn-lg btn-block waves-effect waves-light">
                                    <i class="fe-user-plus mr-1"></i> Agregar Lector
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo site_url('solicitudes/pendientes'); ?>" class="btn btn-warning btn-lg btn-block waves-effect waves-light">
                                    <i class="fe-list mr-1"></i> Ver Solicitudes
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo site_url('prestamos/nuevo'); ?>" class="btn btn-info btn-lg btn-block waves-effect waves-light">
                                    <i class="fe-bookmark mr-1"></i> Nuevo Préstamo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>