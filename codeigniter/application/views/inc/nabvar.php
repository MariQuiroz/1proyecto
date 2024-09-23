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
            <a class="nav-link dropdown-toggle  waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <i class="fe-bell noti-icon"></i>
                <span class="badge badge-danger rounded-circle noti-icon-badge">5</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-lg">
                <!-- Contenido de notificaciones (sin cambios) -->
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