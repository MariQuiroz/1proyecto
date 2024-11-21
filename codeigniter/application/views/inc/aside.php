<div class="left-side-menu">
    <div class="slimscroll-menu">
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">
                <li class="menu-title">Navegación</li>

                <li>
                    <a href="<?php echo site_url('usuarios/panel'); ?>" id="inicio-menu">
                        <i class="la la-dashboard"></i>
                        <span> Inicio </span>
                    </a>
                </li>

                <?php if ($this->session->userdata('rol') == 'administrador'): ?>
                    <li>
                        <a href="javascript:void(0);" aria-haspopup="true" aria-expanded="false" id="usuarios-menu">
                            <i class="la la-users"></i>
                            <span> Usuarios </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?php echo site_url('usuarios/mostrar'); ?>" id="usuarios-habilitados">Usuarios Habilitados</a></li>
                            <li><a href="<?php echo site_url('usuarios/deshabilitados'); ?>" id="usuarios-deshabilitados">Usuarios Deshabilitados</a></li>
                            <li><a href="<?php echo site_url('usuarios/agregar'); ?>" id="agregar-usuario">Agregar Usuario</a></li>
                        </ul>
                    </li>
                <?php endif; ?>


                <li>
                    <a href="javascript:void(0);" aria-haspopup="true" aria-expanded="false" id="publicaciones-menu">
                        <i class="la la-book"></i>
                        <span> Publicaciones </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <li><a href="<?php echo site_url('publicaciones/index'); ?>" id="lista-publicaciones">Catálogo de Publicaciones</a></li>
                        <?php if ($this->session->userdata('rol') == 'administrador'): ?>
                            <li><a href="<?php echo site_url('publicaciones/agregar'); ?>" id="agregar-publicacion">Agregar Publicación</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <?php if ($this->session->userdata('rol') == 'administrador'): ?>
                    <li>
                        <a href="javascript:void(0);" aria-haspopup="true" aria-expanded="false" id="tipos-editoriales-menu">
                            <i class="la la-list"></i>
                            <span> Tipos y Editoriales </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?php echo site_url('tipos/index'); ?>" id="gestionar-tipos">Gestionar Tipos</a></li>
                            <li><a href="<?php echo site_url('editoriales/index'); ?>" id="gestionar-editoriales">Gestionar Editoriales</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <li>
                    <a href="javascript:void(0);" aria-haspopup="true" aria-expanded="false" id="solicitudes-menu">
                        <i class="la la-file-text"></i>
                        <span> Solicitudes de Préstamo </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <?php if ($this->session->userdata('rol') == 'lector'): ?>
                            <li><a href="<?php echo site_url('solicitudes/mis_solicitudes'); ?>" id="mis-solicitudes">Mis Solicitudes</a></li>
                        <?php endif; ?>
                        <?php if ($this->session->userdata('rol') == 'administrador' || $this->session->userdata('rol') == 'encargado'): ?>
                            <li><a href="<?php echo site_url('solicitudes/pendientes'); ?>" id="solicitudes-pendientes">Solicitudes Pendientes</a></li>
                            <li><a href="<?php echo site_url('solicitudes/aprobadas'); ?>" id="solicitudes-aprobadas">Solicitudes Aprobadas</a></li>
                            <li><a href="<?php echo site_url('solicitudes/rechazadas'); ?>" id="solicitudes-rechazadas">Solicitudes Rechazadas</a></li>
                            <li><a href="<?php echo site_url('solicitudes/historial'); ?>" id="historial-solicitudes">Historial de Solicitudes</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0);" aria-haspopup="true" aria-expanded="false" id="prestamos-menu">
                        <i class="la la-exchange"></i>
                        <span> Préstamos </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <?php if ($this->session->userdata('rol') == 'lector'): ?>
                            <li><a href="<?php echo site_url('prestamos/mis_prestamos'); ?>" id="mis-prestamos">Mis Préstamos</a></li>
                        <?php endif; ?>
                        <?php if ($this->session->userdata('rol') == 'administrador' || $this->session->userdata('rol') == 'encargado'): ?>
                            <li><a href="<?php echo site_url('prestamos/activos'); ?>" id="prestamos-activos">Préstamos Activos</a></li>
                            <li><a href="<?php echo site_url('prestamos/devueltos'); ?>"><i class="mdi mdi-book-check"></i><span>Préstamos Devueltos</span></a></li>
                            <li><a href="<?php echo site_url('prestamos/historial'); ?>" id="historial-prestamos">Historial de Préstamos</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <?php if ($this->session->userdata('rol') == 'administrador'): ?>

                    
                <li>
                    <a href="<?php echo site_url('reportes/index'); ?>" id="reportes">
                        <i class="fe-bar-chart-2"></i>
                        <span> Reportes </span>
                    </a>
                </li>
               
            
            <?php endif; ?>

                <li>
                    <a href="<?php echo site_url('notificaciones/index'); ?>" id="notificaciones-menu">
                        <i class="la la-bell"></i>
                        <span> Notificaciones </span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
