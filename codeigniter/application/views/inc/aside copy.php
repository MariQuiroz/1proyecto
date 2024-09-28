<div class="left-side-menu">
    <div class="slimscroll-menu">
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">
                <li class="menu-title">Navegación</li>

                <li>
                    <a href="<?php echo site_url('usuarios/panel'); ?>">
                        <i class="la la-dashboard"></i>
                        <span> Inicio </span>
                    </a>
                </li>

                <?php if ($this->session->userdata('rol') == 'administrador'): ?>
                    <li>
                        <a href="javascript: void(0);">
                            <i class="la la-users"></i>
                            <span> Usuarios </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?php echo site_url('usuarios/mostrar'); ?>">Usuarios Habilitados</a></li>
                            <li><a href="<?php echo site_url('usuarios/deshabilitados'); ?>">Usuarios Deshabilitados</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <li>
                    <a href="javascript: void(0);">
                        <i class="la la-book"></i>
                        <span> Publicaciones </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <li><a href="<?php echo site_url('publicaciones/index'); ?>">Lista de Publicaciones</a></li>
                        <?php if ($this->session->userdata('rol') == 'administrador' || $this->session->userdata('rol') == 'encargado'): ?>
                            <li><a href="<?php echo site_url('publicaciones/agregar'); ?>">Agregar Publicación</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);">
                        <i class="la la-file-text"></i>
                        <span> Solicitudes de Préstamo </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <?php if ($this->session->userdata('rol') == 'lector'): ?>
                            <li><a href="<?php echo site_url('solicitudes/crear'); ?>">Nueva Solicitud</a></li>
                            <li><a href="<?php echo site_url('solicitudes/mis_solicitudes'); ?>">Mis Solicitudes</a></li>
                        <?php endif; ?>
                        <?php if ($this->session->userdata('rol') == 'administrador' || $this->session->userdata('rol') == 'encargado'): ?>
                            <li><a href="<?php echo site_url('solicitudes/listar_pendientes'); ?>">Solicitudes Pendientes</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);">
                        <i class="la la-exchange"></i>
                        <span> Préstamos </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <?php if ($this->session->userdata('rol') == 'lector'): ?>
                            <li><a href="<?php echo site_url('prestamos/mis_prestamos'); ?>">Mis Préstamos</a></li>
                        <?php endif; ?>
                        <?php if ($this->session->userdata('rol') == 'administrador' || $this->session->userdata('rol') == 'encargado'): ?>
                            <li><a href="<?php echo site_url('prestamos/index'); ?>">Préstamos Activos</a></li>
                            <li><a href="<?php echo site_url('prestamos/historial'); ?>">Historial de Préstamos</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <?php if ($this->session->userdata('rol') == 'administrador'): ?>
                    <li>
                        <a href="javascript: void(0);">
                            <i class="la la-file-text-o"></i>
                            <span> Reportes </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?php echo site_url('reportes/prestamos'); ?>">Reporte de Préstamos</a></li>
                            <li><a href="<?php echo site_url('reportes/publicaciones'); ?>">Publicaciones más Solicitadas</a></li>
                            <li><a href="<?php echo site_url('reportes/usuarios'); ?>">Usuarios más Activos</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <li>
                    <a href="<?php echo site_url('notificaciones/index'); ?>">
                        <i class="la la-bell"></i>
                        <span> Notificaciones </span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>