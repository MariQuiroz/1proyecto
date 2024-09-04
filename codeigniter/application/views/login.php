<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Login - Mi Aplicación</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Panel de administración" name="description" />
        <meta content="MiEmpresa" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo base_url('adminXeria/dist/assets/images/favicon.ico'); ?>">

        <!-- App css -->
        <link href="<?php echo base_url('adminXeria/dist/assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url('adminXeria/dist/assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url('adminXeria/dist/assets/css/app.min.css'); ?>" rel="stylesheet" type="text/css" />

    </head>

    <body class="authentication-bg authentication-bg-pattern">

        <div class="account-pages mt-5 mb-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card">

                            <div class="card-body p-4">
                                
                                <div class="text-center w-75 m-auto">
                                    <a href="<?php echo site_url('dashboard'); ?>">
                                        <span><img src="<?php echo base_url('assets/images/logo-light.png'); ?>" alt="" height="18"></span>
                                    </a>
                                    <p class="text-muted mb-4 mt-3">Ingrese su nombre de usuario y contraseña para acceder al panel de administración.</p>
                                </div>

                                <h5 class="auth-title">Iniciar Sesión</h5>

                                <?php echo form_open('usuarios/validar'); ?>

                                    <div class="form-group mb-3">
                                        <label for="username">Nombre de usuario</label>
                                        <?php echo form_input(['name' => 'username', 'class' => 'form-control', 'type' => 'text', 'id' => 'username', 'required' => '', 'placeholder' => 'Ingrese su nombre de usuario']); ?>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="password">Contraseña</label>
                                        <?php echo form_password(['name' => 'password', 'class' => 'form-control', 'id' => 'password', 'required' => '', 'placeholder' => 'Ingrese su contraseña']); ?>
                                    </div>

                                    <div class="form-group mb-3">
                                        <div class="custom-control custom-checkbox checkbox-info">
                                            <?php echo form_checkbox(['name' => 'remember', 'id' => 'checkbox-signin', 'class' => 'custom-control-input']); ?>
                                            <label class="custom-control-label" for="checkbox-signin">Recordarme</label>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0 text-center">
                                        <?php echo form_submit(['class' => 'btn btn-danger btn-block', 'value' => 'Iniciar Sesión']); ?>
                                    </div>

                                <?php echo form_close(); ?>

                                <div class="text-center">
                                    <h5 class="mt-3 text-muted">Iniciar sesión con</h5>
                                    <ul class="social-list list-inline mt-3 mb-0">
                                        <li class="list-inline-item">
                                            <a href="javascript: void(0);" class="social-list-item border-primary text-primary"><i class="mdi mdi-facebook"></i></a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="javascript: void(0);" class="social-list-item border-danger text-danger"><i class="mdi mdi-google"></i></a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="javascript: void(0);" class="social-list-item border-info text-info"><i class="mdi mdi-twitter"></i></a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="javascript: void(0);" class="social-list-item border-secondary text-secondary"><i class="mdi mdi-github-circle"></i></a>
                                        </li>
                                    </ul>
                                </div>

                            </div> <!-- end card-body -->
                        </div>
                        <!-- end card -->

                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <p> <a href="<?php echo base_url('lectores/recuperar_contrasena'); ?>" class="text-muted ml-1">¿Olvidó su contraseña?</a></p>
                                <p class="text-muted">¿No tiene una cuenta? <a href="<?php echo site_url('auth/registro'); ?>" class="text-muted ml-1"><b class="font-weight-semibold">Regístrese</b></a></p>
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->

                    </div> <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end page -->

        <footer class="footer footer-alt">
            <?php echo date('Y'); ?> &copy; Mi Aplicación por <a href="" class="text-muted">MiEmpresa</a> 
        </footer>

        <!-- Vendor js -->
        <script src="<?php echo base_url('adminXeria/dist/assets/js/vendor.min.js'); ?>"></script>

        <!-- App js -->
        <script src="<?php echo base_url('adminXeria/dist/assets/js/app.min.js'); ?>"></script>
        
    </body>
</html>