<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Login - Hemeroteca</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta content="Panel de administración de la Hemeroteca" name="description" />
        <meta content="Hemeroteca" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo base_url('adminXeria/dist/assets/images/favicon.ico'); ?>"/>

        <!-- App css -->
        <link href="<?php echo base_url('adminXeria/dist/assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url('adminXeria/dist/assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url('adminXeria/dist/assets/css/app.min.css'); ?>" rel="stylesheet" type="text/css" />

        <style>
            .error-message {
                color: #dc3545;
                font-size: 80%;
                margin-top: 0.25rem;
                display: none;
            }
            .is-invalid ~ .error-message {
                display: block;
            }
            .input-requirements {
                font-size: 12px;
                color: #666;
                margin-top: 5px;
            }
          /* Colores UMSS */
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

/* Checkbox personalizado */
.custom-control-input:checked ~ .custom-control-label::before {
    background-color: var(--umss-red) !important;
    border-color: var(--umss-red) !important;
}

/* Inputs focalizados */
.form-control:focus {
    border-color: var(--umss-blue:);
    box-shadow: 0 0 0 0.2rem rgba(179, 0, 27, 0.25);
}

/* Títulos */
.auth-title {
    color: var(--umss-blue);
    font-weight: bold;
}

/* Footer */
.footer-alt {
    color: var(--umss-light) !important;
}

/* Mensajes de alerta */
.alert-success {
    border-left: 4px solid #28a745;
}

.alert-danger {
    border-left: 4px solid var(--umss-red);
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
                                
                                <div class="text-center w-75 m-auto">
                                    <a href="<?php echo site_url('usuarios/index'); ?>">
                                        <img src="<?php echo base_url(); ?>uploads/portadas/hemeroteca.jpg" alt="100" height="100">
                                    </a>
                                    <p class="text-muted mb-4 mt-3">HEMEROTECA "JOSE ANTONIO ARZE"</p>
                                </div>

                                <p>¿No recibiste el correo de verificación? <a href="<?= site_url('usuarios/reenviar_verificacion') ?>">Solicitar reenvío</a></p>

                                <h5 class="auth-title">Iniciar Sesión</h5>

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

                                <?php echo form_open('usuarios/validar', ['id' => 'loginForm', 'novalidate' => '']); ?>

                                    <div class="form-group mb-3">
                                        <label for="username">Nombre de usuario</label>
                                        <?php echo form_input([
                                            'name' => 'username', 
                                            'class' => 'form-control', 
                                            'type' => 'text', 
                                            'id' => 'username', 
                                            'required' => '', 
                                            'minlength' => '4',
                                            'maxlength' => '20',
                                            'placeholder' => 'Ingrese su nombre de usuario'
                                        ]); ?>
                                        <div class="error-message" id="username-error"></div>
                                        
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="password">Contraseña</label>
                                        <?php echo form_password([
                                            'name' => 'password', 
                                            'class' => 'form-control', 
                                            'id' => 'password', 
                                            'required' => '', 
                                            'minlength' => '6',
                                            'maxlength' => '20',
                                            'placeholder' => 'Ingrese su contraseña'
                                        ]); ?>
                                        <div class="error-message" id="password-error"></div>
                                        
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

                            </div> <!-- end card-body -->
                        </div>
                        <!-- end card -->
                        
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <p><a href="<?php echo site_url('usuarios/recuperar_contrasena'); ?>" class="text-muted ml-1">¿Olvidó su contraseña?</a></p>
                                <p class="text-muted">¿No tiene una cuenta? <a href="<?php echo site_url('usuarios/auto_registro'); ?>" class="text-muted ml-1"><b class="font-weight-semibold">Regístrese</b></a></p>
                                <div class="col-12 text-center">
                                        <p class="text-muted">Volver a la <a href="<?php echo site_url('home/index'); ?>" class="text-muted ml-1"><b class="font-weight-semibold">página de inicio</b></a></p>
                                    </div>
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
            <?php echo date('Y'); ?> &copy; Hemeroteca
        </footer>

        <!-- Vendor js -->
        <script src="<?php echo base_url('adminXeria/dist/assets/js/vendor.min.js'); ?>"></script>

        <!-- App js -->
        <script src="<?php echo base_url('adminXeria/dist/assets/js/app.min.js'); ?>"></script>

        <!-- Validación del formulario -->
        <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            let isValid = true;
            const username = document.getElementById('username');
            const password = document.getElementById('password');
            const usernameError = document.getElementById('username-error');
            const passwordError = document.getElementById('password-error');

            // Validar username
            if (!username.value.trim()) {
                username.classList.add('is-invalid');
                usernameError.textContent = 'Por favor ingrese su nombre de usuario';
                isValid = false;
            } else if (username.value.length < 4) {
                username.classList.add('is-invalid');
                usernameError.textContent = 'El usuario debe tener al menos 4 caracteres';
                isValid = false;
            } else if (username.value.length > 20) {
                username.classList.add('is-invalid');
                usernameError.textContent = 'El usuario no puede tener más de 20 caracteres';
                isValid = false;
            } else {
                username.classList.remove('is-invalid');
                usernameError.textContent = '';
            }

            // Validar password
            if (!password.value.trim()) {
                password.classList.add('is-invalid');
                passwordError.textContent = 'Por favor ingrese su contraseña';
                isValid = false;
            } else if (password.value.length < 6) {
                password.classList.add('is-invalid');
                passwordError.textContent = 'La contraseña debe tener al menos 6 caracteres';
                isValid = false;
            } else if (password.value.length > 20) {
                password.classList.add('is-invalid');
                passwordError.textContent = 'La contraseña no puede tener más de 20 caracteres';
                isValid = false;
            } else {
                password.classList.remove('is-invalid');
                passwordError.textContent = '';
            }

            // Si hay errores, prevenir el envío del formulario
            if (!isValid) {
                event.preventDefault();
            }
        });

        // Validación en tiempo real
        const inputs = document.querySelectorAll('#loginForm input[required]');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                const errorElement = document.getElementById(`${this.id}-error`);
                
                if (!this.value.trim()) {
                    this.classList.add('is-invalid');
                    errorElement.textContent = `Por favor ingrese su ${this.id === 'username' ? 'nombre de usuario' : 'contraseña'}`;
                } else if (this.id === 'username' && (this.value.length < 4 || this.value.length > 20)) {
                    this.classList.add('is-invalid');
                    errorElement.textContent = `El usuario debe tener entre 4 y 20 caracteres`;
                } else if (this.id === 'password' && (this.value.length < 6 || this.value.length > 20)) {
                    this.classList.add('is-invalid');
                    errorElement.textContent = `La contraseña debe tener entre 6 y 20 caracteres`;
                } else {
                    this.classList.remove('is-invalid');
                    errorElement.textContent = '';
                }
            });
        });

        // Agregar mensaje de error específico si el servidor devuelve error de credenciales
        <?php if (isset($msg) && $msg == '2'): ?>
            document.addEventListener('DOMContentLoaded', function() {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger alert-dismissible fade show';
                errorDiv.innerHTML = 'Usuario o contraseña incorrectos';
                document.querySelector('.auth-title').insertAdjacentElement('afterend', errorDiv);
            });
        <?php endif; ?>
        </script>
        
    </body>
</html>