<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Perfil de Usuario - Hemeroteca</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Perfil de usuario de la Hemeroteca" name="description" />
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
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Información de Perfil</h4>
                        <div class="text-center">
                            <img src="<?php echo base_url('assets/images/users/avatar-1.jpg'); ?>" alt="Foto de perfil" class="rounded-circle avatar-lg">
                            <h5 class="mt-3"><?php echo $usuario->nombres . ' ' . $usuario->apellidoPaterno; ?></h5>
                            <p class="text-muted"><?php echo $usuario->rol; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Detalles del Perfil</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Nombre Completo</th>
                                        <td><?php echo $usuario->nombres . ' ' . $usuario->apellidoPaterno . ' ' . $usuario->apellidoMaterno; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Correo Electrónico</th>
                                        <td><?php echo $usuario->email; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Carnet</th>
                                        <td><?php echo $usuario->carnet; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Profesión</th>
                                        <td><?php echo $usuario->profesion; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Fecha de Nacimiento</th>
                                        <td><?php echo $usuario->fechaNacimiento; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Sexo</th>
                                        <td><?php echo $usuario->sexo == 'M' ? 'Masculino' : 'Femenino'; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <a href="<?php echo site_url('usuarios/editar_perfil'); ?>" class="btn btn-primary mt-3">Editar Perfil</a>
                        <a href="<?php echo site_url('usuarios/cambiar_password'); ?>" class="btn btn-secondary mt-3">Cambiar Contraseña</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor js -->
    <script src="<?php echo base_url('adminXeria/dist/assets/js/vendor.min.js'); ?>"></script>

    <!-- App js -->
    <script src="<?php echo base_url('adminXeria/dist/assets/js/app.min.js'); ?>"></script>
</body>
</html>