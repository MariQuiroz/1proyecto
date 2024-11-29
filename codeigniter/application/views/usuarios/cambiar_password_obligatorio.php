<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Cambio Obligatorio de Contraseña - Hemeroteca</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta content="Cambio obligatorio de contraseña para la Hemeroteca" name="description" />
    <meta content="Hemeroteca" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo base_url('adminXeria/dist/assets/images/favicon.ico'); ?>"/>

    <!-- App css -->
    <link href="<?php echo base_url('adminXeria/dist/assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('adminXeria/dist/assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('adminXeria/dist/assets/css/app.min.css'); ?>" rel="stylesheet" type="text/css" />
</head>

<body class="authentication-bg">
    <div class="account-pages mt-5 mb-5">
        <div class="container-fluid"> 
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center mt-0 font-weight-bold">Cambio Obligatorio de Contraseña</h4>
                                <p class="text-muted mb-4">Por razones de seguridad, debe cambiar su contraseña temporal.</p>
                            </div>

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

                            <?php echo form_open('usuarios/cambiar_password_obligatorio', ['class' => 'form-horizontal', 'id' => 'formCambioPassword']); ?>

                            <div class="form-group">
                                <label for="nueva_password">Nueva Contraseña</label>
                                <input class="form-control" type="password" id="nueva_password" name="nueva_password" required 
                                       placeholder="Ingrese su nueva contraseña">
                                <small class="text-muted">La contraseña debe tener entre 6 y 20 caracteres</small>
                                <div id="password-error" class="invalid-feedback"></div>
                            </div>

                            <div class="form-group">
                                <label for="confirmar_password">Confirmar Nueva Contraseña</label>
                                <input class="form-control" type="password" id="confirmar_password" name="confirmar_password" required 
                                       placeholder="Confirme su nueva contraseña">
                                <div id="confirm-password-error" class="invalid-feedback"></div>
                            </div>

                            <div class="form-group mb-0 text-center">
                                <button class="btn btn-primary" type="submit">Cambiar Contraseña</button>
                            </div>

                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor js -->
    <script src="<?php echo base_url('adminXeria/dist/assets/js/vendor.min.js'); ?>"></script>
    <!-- App js -->
    <script src="<?php echo base_url('adminXeria/dist/assets/js/app.min.js'); ?>"></script>

    <script>
    $(document).ready(function() {
        const formCambioPassword = $('#formCambioPassword');
        const nuevaPassword = $('#nueva_password');
        const confirmarPassword = $('#confirmar_password');
        const passwordError = $('#password-error');
        const confirmPasswordError = $('#confirm-password-error');

        function validarPassword(password) {
            if (password.length < 6 || password.length > 20) {
                return 'La contraseña debe tener entre 6 y 20 caracteres.';
            }
            return '';
        }

        function validarConfirmacion(password, confirmacion) {
            if (password !== confirmacion) {
                return 'Las contraseñas no coinciden.';
            }
            return '';
        }

        nuevaPassword.on('input', function() {
            const error = validarPassword($(this).val());
            if (error) {
                $(this).addClass('is-invalid').removeClass('is-valid');
                passwordError.text(error).show();
            } else {
                $(this).addClass('is-valid').removeClass('is-invalid');
                passwordError.hide();
            }
        });

        confirmarPassword.on('input', function() {
            const error = validarConfirmacion(nuevaPassword.val(), $(this).val());
            if (error) {
                $(this).addClass('is-invalid').removeClass('is-valid');
                confirmPasswordError.text(error).show();
            } else {
                $(this).addClass('is-valid').removeClass('is-invalid');
                confirmPasswordError.hide();
            }
        });

        formCambioPassword.on('submit', function(e) {
            const passwordValue = nuevaPassword.val();
            const confirmValue = confirmarPassword.val();
            
            const passwordError = validarPassword(passwordValue);
            const confirmError = validarConfirmacion(passwordValue, confirmValue);

            if (passwordError || confirmError) {
                e.preventDefault();
                return false;
            }
        });
    });
    </script>
</body>
</html>