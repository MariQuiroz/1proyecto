<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Recuperar Contraseña - Xeria</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo base_url(); ?>adminXeria/light/dist/assets/images/favicon.ico">

        <!-- App css -->
        <link href="<?php echo base_url(); ?>adminXeria/light/dist/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>adminXeria/light/dist/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>adminXeria/light/dist/assets/css/app.min.css" rel="stylesheet" type="text/css" />

    </head>

    <body class="authentication-bg authentication-bg-pattern">

        <div class="account-pages mt-5 mb-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card">

                            <div class="card-body p-4">

                                <div class="text-center mb-4">
                                <a href="index.html">
                                        <img src="<?php echo base_url(); ?>uploads/portadas/hemeroteca.jpg" alt="100" height="100">
                                    </a>
                                    <p class="text-muted mb-4 mt-3">HEMEROTECA "JOSE ANTONIO ARZE"</p>
                                
                                </div>

                                <div class="text-center w-75 m-auto">
                                    <h4 class="text-dark-50 text-center mt-3">Recuperar Contraseña</h4>
                                    <p class="text-muted mb-4">Ingresa tu correo electrónico para recuperar tu cuenta.</p>
                                </div>

                                <!-- Formulario de Recuperación de Contraseña -->
                                <?php if(isset($error)): ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php endif; ?>
                                <?php if($this->session->flashdata('error')): ?>
                                    <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                                <?php endif; ?>
                                <?php if($this->session->flashdata('mensaje')): ?>
                                    <div class="alert alert-success"><?php echo $this->session->flashdata('mensaje'); ?></div>
                                <?php endif; ?>

                                <?php echo form_open('usuarios/recuperar_contrasena'); ?>
                                    <div class="form-group mb-3">
                                        <label for="email">Correo Electrónico</label>
                                        <input type="email" name="email" id="email" class="form-control" placeholder="Ingrese su correo electrónico" required>
                                    </div>

                                    <div class="form-group mb-0 text-center">
                                        <button class="btn btn-danger btn-block" type="submit">Enviar Correo de Recuperación</button>
                                    </div>
                                <?php echo form_close(); ?>

                                <div class="text-center mt-4">
                                    <p class="text-muted">¿Ya tienes una cuenta? <a href="<?php echo site_url('usuarios/index'); ?>" class="text-muted ml-1"><b class="font-weight-semibold">Iniciar sesión</b></a></p>
                                </div>

                            </div> <!-- end card-body -->
                        </div>
                        <!-- end card -->
                    </div> <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end page -->

        <footer class="footer footer-alt">
            2019 &copy; Xeria theme by <a href="" class="text-muted">Coderthemes</a> 
        </footer>

        <!-- Vendor js -->
        <script src="<?php echo base_url(); ?>adminXeria/light/assets/dist/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?php echo base_url(); ?>adminXeria/light/dist/assets/js/app.min.js"></script>
        
    </body>
</html>
