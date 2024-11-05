<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid"> 
            <!-- Start Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Usuarios</a></li>
                                <li class="breadcrumb-item active">Lista de Usuarios Activos</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Lista de Usuarios Activos</h4>
                    </div>
                </div>
            </div>
            <!-- End Page Title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
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

                            <div class="mb-3">
                                <?php if ($this->session->userdata('rol') == 'administrador' || $this->session->userdata('rol') == 'encargado'): ?>
                                    <a href="<?php echo site_url('usuarios/agregar'); ?>" class="btn btn-success mr-2">
                                        <i class="mdi mdi-plus-circle mr-1"></i>Agregar Nuevo Usuario
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo site_url('usuarios/deshabilitados'); ?>" class="btn btn-info">
                                    <i class="mdi mdi-account-off mr-1"></i>Ver Usuarios Deshabilitados
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th scope="col">Nombre</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Rol</th>
                                            <th scope="col">Profesión</th>
                                            <th scope="col">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($usuario->nombres . ' ' . $usuario->apellidoPaterno); ?></td>
                                                <td><?php echo htmlspecialchars($usuario->email); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo $usuario->rol == 'administrador' ? 'danger' : ($usuario->rol == 'encargado' ? 'warning' : 'info'); ?>">
                                                        <?php echo ucfirst($usuario->rol); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo !empty($usuario->profesion) ? htmlspecialchars($usuario->profesion) : '<i>No especificada</i>'; ?></td>
                                                <td>
                                                    <a href="<?php echo site_url('usuarios/modificar/' . $usuario->idUsuario); ?>" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Editar">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    <?php echo form_open('usuarios/deshabilitarbd', ['style' => 'display:inline;']); ?>
                                                        <input type="hidden" name="idUsuario" value="<?php echo $usuario->idUsuario; ?>">
                                                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('¿Está seguro de querer deshabilitar este usuario?');" data-toggle="tooltip" title="Deshabilitar">
                                                            <i class="mdi mdi-account-off"></i>
                                                        </button>
                                                    <?php echo form_close(); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div><!-- end col-->
            </div>
            <!-- end row-->
        </div> <!-- container -->
    </div>

