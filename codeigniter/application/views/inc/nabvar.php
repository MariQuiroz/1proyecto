<!-- Topbar Start -->
<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-right mb-0">
        <li class="dropdown notification-list d-inline-block">
            <a class="nav-link dropdown-toggle waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <i class="fe-bell noti-icon"></i>
                <?php 
                $idUsuario = $this->session->userdata('idUsuario');
                $rol = $this->session->userdata('rol');
                $notificaciones_no_leidas = $this->Notificacion_model->contar_notificaciones_no_leidas($idUsuario, $rol);
                if ($notificaciones_no_leidas > 0):
                ?>
                <span class="badge badge-danger rounded-circle noti-icon-badge"><?php echo $notificaciones_no_leidas; ?></span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-lg">
                <div class="dropdown-item noti-title">
                    <h5 class="m-0">
                        <span class="float-right">
                            <a href="<?php echo site_url('notificaciones/marcar_todas_leidas'); ?>" class="text-white">
                                <small>Marcar todas como leídas</small>
                            </a>
                        </span>Notificaciones
                    </h5>
                </div>

                <div class="slimscroll noti-scroll">
                    <?php 
                    $notificaciones = $this->Notificacion_model->obtener_ultimas_notificaciones($idUsuario, $rol, 5);
                    if (!empty($notificaciones)):
                        foreach ($notificaciones as $notificacion):
                            $icon_class = 'mdi mdi-comment-account-outline';
                            $mensaje_mostrar = $notificacion->mensaje;
                            
                            switch($notificacion->tipo) {
                                case NOTIFICACION_SOLICITUD_PRESTAMO:
                                    $icon_class = 'mdi mdi-book-open-page-variant';
                                    break;
                                case NOTIFICACION_APROBACION_PRESTAMO:
                                case NOTIFICACION_RECHAZO_PRESTAMO:
                                    $icon_class = 'mdi mdi-check-circle-outline';
                                    break;
                                case NOTIFICACION_NUEVA_SOLICITUD:
                                    $icon_class = 'mdi mdi-alert-circle-outline';
                                    if ($rol == 'administrador' || $rol == 'encargado') {
                                        $mensaje_mostrar = "Nueva solicitud de préstamo";
                                    }
                                    break;
                                case NOTIFICACION_DEVOLUCION:
                                    $icon_class = 'mdi mdi-undo-variant';
                                    break;
                                case NOTIFICACION_DISPONIBILIDAD:
                                    $icon_class = 'mdi mdi-bookmark-check';
                                    break;
                                case NOTIFICACION_VENCIMIENTO:
                                    $icon_class = 'mdi mdi-clock-alert';
                                    break;
                                default:
                                    $icon_class = 'mdi mdi-comment-account-outline';
                            }
                    ?>
                    <a href="<?php echo site_url('notificaciones/ver/' . $notificacion->idNotificacion); ?>" class="dropdown-item notify-item <?php echo $notificacion->leida ? '' : 'active'; ?>">
                        <div class="notify-icon bg-primary">
                            <i class="<?php echo $icon_class; ?>"></i>
                        </div>
                        <p class="notify-details"><?php echo (strlen($mensaje_mostrar) > 50) ? substr($mensaje_mostrar, 0, 47) . '...' : $mensaje_mostrar; ?>
                            <small class="text-muted"><?php echo $this->Notificacion_model->time_elapsed_string($notificacion->fechaEnvio); ?></small>
                        </p>
                    </a>
                    <?php 
                        endforeach;
                    else:
                    ?>
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
                    <h6 class="text-overflow m-0 text-white">Bienvenido!</h6>
                </div>

                <a href="<?php echo site_url('usuarios/perfil'); ?>" class="dropdown-item notify-item">
                    <i class="fe-user"></i>
                    <span>Mi Perfil</span>
                </a>

                <a href="<?php echo site_url('usuarios/configuracion'); ?>" class="dropdown-item notify-item">
                    <i class="fe-settings"></i>
                    <span>Configuración</span>
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
                <img src="<?php echo base_url(); ?>adminXeria/light/dist/assets/images/logo-light.png" alt="" height="16">
            </span>
            <span class="logo-sm">
                <img src="<?php echo base_url(); ?>adminXeria/light/dist/assets/images/logo-sm.png" alt="" height="18">
            </span>
        </a>
    </div>

    <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
            <button class="button-menu-mobile waves-effect">
                <span></span>
                <span></span>
                <span></span>
            </button>
    </ul>
</div>
<!-- end Topbar -->