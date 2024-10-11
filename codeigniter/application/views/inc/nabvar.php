<!-- Topbar Start -->
<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-right mb-0">

        <li class="d-none d-sm-block">
            <form class="app-search">
                <div class="app-search-box">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search...">
                        <div class="input-group-append">
                            <button class="btn" type="submit">
                                <i class="fe-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </li>

        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <i class="fe-bell noti-icon"></i>
                <?php 
                $notificaciones_no_leidas = $this->Notificacion_model->contar_notificaciones_no_leidas($this->session->userdata('idUsuario'));
                if ($notificaciones_no_leidas > 0):
                ?>
                <span class="badge badge-danger rounded-circle noti-icon-badge"><?php echo $notificaciones_no_leidas; ?></span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-lg">
                <div class="dropdown-item noti-title">
                    <h5 class="m-0">
                        <span class="float-right">
                            <a href="<?php echo site_url('notificaciones/marcar_todas_leidas'); ?>" class="text-dark">
                                <small>Marcar todas como leídas</small>
                            </a>
                        </span>Notificaciones
                    </h5>
                </div>

                <div class="slimscroll noti-scroll">
                    <?php 
                    $notificaciones = $this->Notificacion_model->obtener_ultimas_notificaciones($this->session->userdata('idUsuario'), 5);
                    foreach ($notificaciones as $notificacion):
                    ?>
                    <a href="<?php echo site_url('notificaciones/ver/' . $notificacion->idNotificacion); ?>" class="dropdown-item notify-item <?php echo $notificacion->leida ? '' : 'active'; ?>">
                        <div class="notify-icon bg-primary">
                            <i class="mdi mdi-comment-account-outline"></i>
                        </div>
                        <p class="notify-details"><?php echo $notificacion->mensaje; ?>
                            <small class="text-muted"><?php echo time_elapsed_string($notificacion->fechaEnvio); ?></small>
                        </p>
                    </a>
                    <?php endforeach; ?>

                    <?php if (empty($notificaciones)): ?>
                    <p class="text-center">No tienes notificaciones nuevas</p>
                    <?php endif; ?>
                </div>

                <a href="<?php echo site_url('notificaciones'); ?>" class="dropdown-item text-center text-primary notify-item notify-all">
                    Ver todas
                    <i class="fi-arrow-right"></i>
                </a>
            </div>
        </li>

        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <img src="<?php echo base_url(); ?>adminXeria/light/dist/assets/images/users/user-1.jpg" alt="user-image" class="rounded-circle">
                <span class="pro-user-name ml-1">
                    <?php echo $this->session->userdata('nombres') . ' ' . $this->session->userdata('apellidoPaterno'); ?> <i class="mdi mdi-chevron-down"></i> 
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                <div class="dropdown-header noti-title">
                    <h6 class="text-overflow m-0">Bienvenido !</h6>
                </div>

                <?php echo form_open_multipart('usuarios/perfil', ['class' => 'dropdown-item notify-item']); ?>
                    <button type="submit" class="btn btn-link p-0">
                        <i class="fe-user"></i>
                        <span>Mi Perfil</span>
                    </button>
                <?php echo form_close(); ?>

                <?php if ($this->session->userdata('rol') == 'administrador'): ?>
                    <?php echo form_open_multipart('admin/configuracion', ['class' => 'dropdown-item notify-item']); ?>
                        <button type="submit" class="btn btn-link p-0">
                            <i class="fe-settings"></i>
                            <span>Configuración</span>
                        </button>
                    <?php echo form_close(); ?>
                <?php endif; ?>

                <div class="dropdown-divider"></div>

                <?php echo form_open_multipart('usuarios/logout', ['class' => 'dropdown-item notify-item']); ?>
                    <button type="submit" class="btn btn-link p-0">
                        <i class="fe-log-out"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                <?php echo form_close(); ?>
            </div>
        </li>

        <li class="dropdown notification-list">
            <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect">
                <i class="fe-settings noti-icon"></i>
            </a>
        </li>

    </ul>

    <!-- LOGO -->
    <div class="logo-box">
        <a href="index.html" class="logo text-center">
            <span class="logo-lg">
                <img src="<?php echo base_url(); ?>adminXeria/light/dist/assets/images/logo-light.png" alt="" height="16">
            </span>
            <span class="logo-sm">
                <img src="<?php echo base_url(); ?>adminXeria/light/dist/assets/images/logo-sm.png" alt="" height="18">
            </span>
        </a>
    </div>

    <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
        <li>
            <button class="button-menu-mobile waves-effect">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </li>
    </ul>
</div>
<!-- end Topbar -->