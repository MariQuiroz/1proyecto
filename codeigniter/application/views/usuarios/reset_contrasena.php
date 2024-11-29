<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Restablecer Contraseña</title>

    <link rel="shortcut icon" href="<?php echo base_url(); ?>adminXeria/light/dist/assets/images/favicon.ico"/>
    
    <!-- App css -->
    <link href="<?php echo base_url(); ?>adminXeria/light/dist/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>adminXeria/light/dist/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>adminXeria/light/dist/assets/css/app.min.css" rel="stylesheet" type="text/css" />
</head>
    <!-- Incluye aquí tus estilos CSS -->
</head>
<body class="authentication-bg authentication-bg-pattern">
    <div class="account-pages mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-body p-4">
        <h2>Restablecer Contraseña</h2>
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

        <?php echo form_open('usuarios/reset_contrasena/' . $token); ?>
            <div class="form-group">
                <label for="password">Nueva Contraseña:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
        <?php echo form_close(); ?>
        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer footer-alt">
            <?php echo date('Y'); ?> &copy; Hemeroteca
    </footer>

<!-- Vendor js -->
<script src="<?php echo base_url(); ?>adminXeria/light/dist/assets/js/vendor.min.js"></script>

<!-- App js -->
<script src="<?php echo base_url(); ?>adminXeria/light/dist/assets/js/app.min.js"></script>

<script>
    $(document).ready(function() {
        const formCambioPassword = $('#formCambioPassword');
        const nuevaPassword = $('#password');
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