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

                <!-- Opciones para el Administrador -->
                <?php if ($this->session->userdata('rol') == 'administrador'): ?>
                    <li>
                        <a href="<?php echo site_url('usuarios/mostrar'); ?>">
                            <i class="la la-users"></i>
                            <span> Usuarios </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?php echo site_url('usuarios/mostrar'); ?>">Usuarios Habilitados</a></li>
                            <li><a href="<?php echo site_url('usuarios/deshabilitados'); ?>">Usuarios Deshabilitados</a></li>
                            <li><a href="<?php echo site_url('usuarios/agregar'); ?>">Agregar Usuario</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="la la-book"></i>
                            <span> Publicaciones </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?php echo site_url('publicaciones/index'); ?>">Lista de Publicaciones</a></li>
                            <li><a href="<?php echo site_url('publicaciones/agregar'); ?>">Agregar Publicación</a></li>
                            <li><a href="<?php echo site_url('editoriales/agregar'); ?>">Agregar Editorial</a></li>
                            <li><a href="<?php echo site_url('tipos/agregar'); ?>">Agregar Tipo</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="la la-calendar"></i>
                            <span> Reservas </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?php echo site_url('reservas/pendientes'); ?>">Reservas Pendientes</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="la la-exchange"></i>
                            <span> Préstamos </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?php echo site_url('prestamos/activos'); ?>">Préstamos Activos</a></li>
                            <li><a href="<?php echo site_url('prestamos/nuevo'); ?>">Nuevo Préstamo</a></li>
                            <li><a href="<?php echo site_url('prestamos/historial'); ?>">Historial de Préstamos</a></li>
                        </ul>
                    </li>

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

                <!-- Opciones para el Encargado -->
                <?php if ($this->session->userdata('rol') == 'encargado'): ?>
                    <li>
                        <a href="<?php echo site_url('usuarios/agregar'); ?>">
                            <i class="la la-user-plus"></i>
                            <span> Agregar Lector </span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="la la-book"></i>
                            <span> Publicaciones </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?php echo site_url('publicaciones/index'); ?>">Lista de Publicaciones</a></li>
                            <li><a href="<?php echo site_url('publicaciones/agregar'); ?>">Agregar Publicación</a></li>
                            <li><a href="<?php echo site_url('editoriales/agregar'); ?>">Agregar Editorial</a></li>
                            <li><a href="<?php echo site_url('tipos/agregar'); ?>">Agregar Tipo</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="la la-calendar"></i>
                            <span> Reservas </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?php echo site_url('reservas/pendientes'); ?>">Reservas Pendientes</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="la la-exchange"></i>
                            <span> Préstamos </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?php echo site_url('prestamos/activos'); ?>">Préstamos Activos</a></li>
                            <li><a href="<?php echo site_url('prestamos/nuevo'); ?>">Nuevo Préstamo</a></li>
                            <li><a href="<?php echo site_url('prestamos/historial'); ?>">Historial de Préstamos</a></li>
                        </ul>
                    </li>

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

                <!-- Opciones para el Lector -->
                <?php if ($this->session->userdata('rol') == 'lector'): ?>
                    <li>
                        <a href="<?php echo site_url('publicaciones/index'); ?>">
                            <i class="la la-book"></i>
                            <span> Publicaciones </span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo site_url('reservas/mis_reservas'); ?>">
                            <i class="la la-calendar"></i>
                            <span> Mis Reservas </span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo site_url('prestamos/mis_prestamos'); ?>">
                            <i class="la la-exchange"></i>
                            <span> Mis Préstamos </span>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo site_url('solicitudes/nueva'); ?>">
                            <i class="la la-plus"></i>
                            <span> Realizar Solicitud </span>
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</div>
