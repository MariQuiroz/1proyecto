<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Confirmación de Registro - Hemeroteca</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url('adminXeria/light/dist/assets/images/favicon.ico'); ?>"/>
    
    <!-- App css -->
    <link href="<?= base_url('adminXeria/light/dist/assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?= base_url('adminXeria/light/dist/assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?= base_url('adminXeria/light/dist/assets/css/app.min.css'); ?>" rel="stylesheet" type="text/css"/>
</head>

<body class="authentication-bg authentication-bg-pattern">
    <div class="account-pages mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="text-center w-75 m-auto">
                                <img src="<?= base_url('uploads/portadas/hemeroteca.jpg'); ?>" alt="logo" height="100">
                                <h3 class="text-dark-50 text-center mt-3 mb-4">¡Registro Exitoso!</h3>
                            </div>

                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="mdi mdi-email-outline text-success" style="font-size: 3em;"></i>
                                </div>
                                <h4>Verifica tu correo electrónico</h4>
                                <p class="text-muted mt-2">
                                    Hemos enviado un enlace de verificación a tu correo electrónico:<br/>
                                    <strong><?= $email ?></strong>
                                </p>
                                <p class="text-muted">
                                    Por favor, revisa tu bandeja de entrada y sigue las instrucciones para activar tu cuenta.
                                </p>
                                <p class="text-muted mt-3">
                                    ¿No recibiste el correo? 
                                    <a href="<?= site_url('usuarios/reenviar_verificacion'); ?>">Reenviar verificación</a>
                                </p>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <a href="<?= site_url('usuarios/index'); ?>" class="btn btn-primary btn-block">
                                        Volver al inicio
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer footer-alt">
        <?= date('Y') ?> &copy; Hemeroteca "José Antonio Arze"
    </footer>

    <!-- Vendor js -->
    <script src="<?= base_url('adminXeria/light/dist/assets/js/vendor.min.js'); ?>"></script>
    <!-- App js -->
    <script src="<?= base_url('adminXeria/light/dist/assets/js/app.min.js'); ?>"></script>
</body>
</html>