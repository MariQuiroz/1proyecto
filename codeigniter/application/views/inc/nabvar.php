<!-- Topbar Start -->
<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-right mb-0">
       

        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <i class="fe-bell noti-icon"></i>
                <?php 
                $idUsuario = $this->session->userdata('idUsuario');
                $rol = $this->session->userdata('rol');
                $notificaciones_no_leidas = $this->notificacion_model->contar_notificaciones_no_leidas($idUsuario, $rol);
                if ($notificaciones_no_leidas > 0):
                ?>
                <span class="badge badge-danger rounded-circle noti-icon-badge">
                    <?php echo $notificaciones_no_leidas; ?>
                </span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-lg">
                <div class="dropdown-item noti-title bg-primary">
                    <h5 class="m-0 text-white">
                        Notificaciones
                        <?php if ($notificaciones_no_leidas > 0): ?>
                            <a href="<?php echo site_url('notificaciones/marcar_todas_leidas'); ?>" 
                               class="float-right">
                                <small class="text-white">
                                    <i class="fe-check-circle mr-1"></i>Marcar todas como leídas
                                </small>
                            </a>
                        <?php endif; ?>
                    </h5>
                </div>

                <div class="slimscroll noti-scroll">
                    <?php 
                    $notificaciones = $this->notificacion_model->obtener_ultimas_notificaciones($idUsuario, $rol, 5);
                    if (!empty($notificaciones)):
                        foreach ($notificaciones as $notificacion):
                            // Determinar el icono basado en el tipo de notificación
                            $icon_class = 'fe-bell'; // icono por defecto
                            switch($notificacion->tipo) {
                                case NOTIFICACION_SOLICITUD_PRESTAMO:
                                    $icon_class = 'fe-bookmark';
                                    break;
                                case NOTIFICACION_APROBACION_PRESTAMO:
                                    $icon_class = 'fe-check-circle';
                                    break;
                                case NOTIFICACION_RECHAZO_PRESTAMO:
                                    $icon_class = 'fe-x-circle';
                                    break;
                                case NOTIFICACION_DEVOLUCION:
                                    $icon_class = 'fe-corner-up-left';
                                    break;
                                case NOTIFICACION_CANCELACION_SOLICITUD:
                                     $icon_class = 'fe-corner-up-left';
                                    break;
                                case NOTIFICACION_SOLICITUD_EXPIRADA:
                                    $icon_class = 'fe-corner-up-left';
                                    break;
                            }
                    ?>
                    <a href="<?php echo site_url('notificaciones/ver/' . $notificacion->idNotificacion); ?>" 
                       class="dropdown-item notify-item <?php echo $notificacion->leida ? '' : 'active'; ?>">
                        <div class="notify-icon bg-primary">
                            <i class="<?php echo $icon_class; ?>"></i>
                        </div>
                        <p class="notify-details">
                            <?php echo htmlspecialchars($notificacion->mensaje); ?>
                            <small class="text-muted">
                                <?php echo $this->notificacion_model->time_elapsed_string($notificacion->fechaEnvio); ?>
                            </small>
                        </p>
                    </a>
                    <?php 
                        endforeach;
                    else:
                    ?>
                    <div class="text-center p-3">
                        <i class="fe-bell font-24 text-muted"></i>
                        <p class="mt-2 text-muted">No hay notificaciones nuevas</p>
                    </div>
                    <?php endif; ?>
                </div>

                <a href="<?php echo site_url('notificaciones'); ?>" 
                   class="dropdown-item text-center text-primary notify-item notify-all">
                    Ver todas las notificaciones
                    <i class="fi-arrow-right"></i>
                </a>
            </div>
        </li>
        <!-- Código de depuración -->

        <!-- Nombre del usuario -->
        <li class="d-none d-sm-block" style="margin-right: 15px;">
            <span class="nav-link" style="color: #000000; font-weight: bold; background-color: rgba(255, 255, 255, 0.8); padding: 8px 15px; border-radius: 20px;">
                <?php
                if ($this->session->userdata('login')) {
                    $idUsuario = $this->session->userdata('idUsuario');
                    $username = $this->session->userdata('username');

                    // Cargar el modelo de usuario si aún no está cargado
                    if (!isset($this->usuario_model)) {
                        $this->load->model('usuario_model');
                    }

                    // Obtener el nombre completo del usuario de la base de datos
                    $usuario = $this->usuario_model->obtener_usuario($idUsuario);
                    
                    if ($usuario && isset($usuario->nombres) && isset($usuario->apellidoPaterno)) {
                        $nombreMostrar = $usuario->nombres . ' ' . $usuario->apellidoPaterno;
                    } else {
                        $nombreMostrar = ucfirst($username); // Usa el nombre de usuario si no se encuentra el nombre completo
                    }

                    echo htmlspecialchars($nombreMostrar);
                } else {
                    echo 'Invitado';
                }
                ?>
            </span>
        </li>
        
        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
            <img src="<?php echo base_url(); ?>img/user.jpg" alt="user-image" class="rounded-circle">
                <span class="pro-user-name ml-1">
                    <?php echo $this->session->userdata('nombres'); ?> <i class="mdi mdi-chevron-down"></i> 
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                <div class="dropdown-header noti-title">
                    <h6 class="text-overflow m-0 text-white">Bienvenido !</h6>
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

    <!-- LOGO -->
    <div class="logo-box">
        <a href="<?php echo base_url(); ?>" class="logo text-center">
            <span class="logo-lg">
            <img src="<?php echo base_url(); ?>uploads/logo1_umss.jpg" alt="logo" height="50">   
            </span>
            <span class="logo-sm">
            <img src="<?php echo base_url(); ?>uploads/logo_umss.jpg" alt="logo" height="20">   
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
<?php if ($this->session->flashdata('mensaje')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $this->session->flashdata('mensaje'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $this->session->flashdata('error'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
