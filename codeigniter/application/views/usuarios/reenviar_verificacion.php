<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Reenviar Correo de Verificación - Xeria</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo base_url(); ?>adminXeria/light/dist/assets/images/favicon.ico"/>

        <!-- App css -->
        <link href="<?php echo base_url(); ?>adminXeria/light/dist/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>adminXeria/light/dist/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>adminXeria/light/dist/assets/css/app.min.css" rel="stylesheet" type="text/css" />
        <style>
    :root {
    --umss-red: #B3001B;     /* Rojo UMSS */
    --umss-blue: #031B4E;    /* Azul oscuro UMSS */
    --umss-light: #f8f9fa;   /* Color claro para textos */
}

/* Fondo principal */
.authentication-bg {
    background-color: var(--umss-blue) !important;
    background-image: none !important;
}

/* Estilos para la tarjeta de login */
.card {
    border: none;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}

.card-body {
    border-top: 4px solid var(--umss-red);
}

/* Botón principal */
.btn-danger {
    background-color: var(--umss-red) !important;
    border-color: var(--umss-red) !important;
    transition: all 0.3s ease;
}

.btn-danger:hover {
    background-color: #8B0015 !important;
    border-color: #8B0015 !important;
}

/* Enlaces */
a {
    color: var(--umss-blue) !important;
}

a:hover {
    color: var(--umss-red) !important;
}
</style>
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
                                    <h4 class="text-dark-50 text-center mt-3">Reenviar Correo de Verificación</h4>
                                    <p class="text-muted mb-4">Ingresa tu correo electrónico para reenviar el correo de verificación.</p>
                                </div>

                                <!-- Mostrar mensajes de éxito o error -->
                                <?php if ($this->session->flashdata('mensaje')): ?>
                                    <div class="alert alert-success"><?= $this->session->flashdata('mensaje') ?></div>
                                <?php endif; ?>

                                <?php if ($this->session->flashdata('error')): ?>
                                    <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
                                <?php endif; ?>

                                <?php echo validation_errors(); ?>

                                <!-- Formulario de Reenvío de Verificación -->
                                <?php echo form_open('usuarios/reenviar_verificacion'); ?>
                                    <div class="form-group mb-3">
                                        <label for="email">Correo Electrónico</label>
                                        <input type="email" name="email" id="email" class="form-control" placeholder="Ingrese su correo electrónico" required>
                                    </div>

                                    <div class="form-group mb-0 text-center">
                                        <button class="btn btn-danger btn-block" type="submit">Reenviar Correo de Verificación</button>
                                    </div>
                                <?php echo form_close(); ?>

                                <div class="text-center mt-4">
                                    <p class="text-muted">¿Ya tienes una cuenta? <a href="<?= site_url('usuarios/index') ?>" class="text-muted ml-1"><b class="font-weight-semibold">Iniciar sesión</b></a></p>
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
        <script src="<?php echo base_url(); ?>adminXeria/light/dist/assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?php echo base_url(); ?>adminXeria/light/dist/assets/js/app.min.js"></script>
        
    </body>
</html>
