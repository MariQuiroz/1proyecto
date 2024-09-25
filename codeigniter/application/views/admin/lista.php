<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Lista de Usuarios - Hemeroteca</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Lista de usuarios para administradores de la Hemeroteca" name="description" />
    <meta content="Hemeroteca" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo base_url('adminXeria/dist/assets/images/favicon.ico'); ?>">

    <!-- App css -->
    <link href="<?php echo base_url('adminXeria/dist/assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('adminXeria/dist/assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('adminXeria/dist/assets/css/app.min.css'); ?>" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Lista de Usuarios</h4>
                        
                        <?php if($this->session->flashdata('mensaje')): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $this->session->flashdata('mensaje'); ?>
                            </div>
                        <?php endif; ?>

                        <table id="basic-datatable" class="table dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo $usuario->idUsuario; ?></td>
                                    <td><?php echo $usuario->nombres . ' ' . $usuario->apellidoPaterno; ?></td>
                                    <td><?php echo $usuario->email; ?></td>
                                    <td><?php echo $usuario->rol; ?></td>
                                    <td><?php echo $usuario->estado == 1 ? 'Activo' : 'Inactivo'; ?></td>
                                    <td>
                                        <a href="<?php echo site_url('usuarios/modificar/'.$usuario->idUsuario); ?>" class="btn btn-primary btn-sm">Editar</a>
                                        <?php if($usuario->estado == 1): ?>
                                            <a href="<?php echo site_url('usuarios/desactivar/'.$usuario->idUsuario); ?>" class="btn btn-warning btn-sm">Desactivar</a>
                                        <?php else: ?>
                                            <a href="<?php echo site_url('usuarios/activar/'.$usuario->idUsuario); ?>" class="btn btn-success btn-sm">Activar</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div>
    </div>

    <!-- Vendor js -->
    <script src="<?php echo base_url('adminXeria/dist/assets/js/vendor.min.js'); ?>"></script>

    <!-- App js -->
    <script src="<?php echo base_url('adminXeria/dist/assets/js/app.min.js'); ?>"></script>
</body>
</html>