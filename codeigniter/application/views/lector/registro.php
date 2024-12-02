<!DOCTYPE html> 
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Hemeroteca</title>
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
            <div class="container-fluid"> 
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card">
                            <div class="card-body p-4">
                                <div class="text-center w-75 m-auto">
                                    <a href="index.html">
                                        <img src="<?php echo base_url(); ?>uploads/portadas/hemeroteca.jpg" alt="100" height="100">
                                    </a>
                                    <p class="text-muted mb-4 mt-3">HEMEROTECA "JOSE ANTONIO ARZE"</p>
                                </div>

                                <h5 class="auth-title">Formulario de Registro</h5>

                                <!-- Aquí se coloca el formulario adaptado -->
                                <?php if($this->session->flashdata('mensaje')): ?>
                                    <div class="alert alert-success" role="alert">
                                        <?php echo $this->session->flashdata('mensaje'); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if($this->session->flashdata('error')): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $this->session->flashdata('error'); ?>
                                    </div>
                                <?php endif; ?>
                                <?php echo form_open('usuarios/auto_registro', ['class' => 'form-horizontal', 'id' => 'form-registro-lector']); ?>
            
            <div class="form-group">
                <label for="nombres">Nombres *</label>
                <input type="text" 
                       name="nombres" 
                       class="form-control <?php echo form_error('nombres') ? 'is-invalid' : ''; ?>" 
                       id="nombres" 
                       placeholder="Ingrese sus nombres"
                       minlength="2"
                       maxlength="20"
                       value="<?php echo set_value('nombres'); ?>"
                       required
                       autocomplete="off"
                       title="Solo se permiten letras, espacios y caracteres especiales permitidos">
                <?php echo form_error('nombres', '<div class="invalid-feedback">', '</div>'); ?>
            </div>

            <div class="form-group">
                <label for="apellidoPaterno">Apellido Paterno *</label>
                <input type="text" 
                       name="apellidoPaterno" 
                       class="form-control <?php echo form_error('apellidoPaterno') ? 'is-invalid' : ''; ?>"
                       id="apellidoPaterno" 
                       placeholder="Ingrese su apellido paterno"
                       minlength="2"
                       maxlength="25"
                       value="<?php echo set_value('apellidoPaterno'); ?>"
                       required
                       autocomplete="off"
                       title="Solo se permiten letras, espacios y caracteres especiales permitidos">
                <?php echo form_error('apellidoPaterno', '<div class="invalid-feedback">', '</div>'); ?>
            </div>

            <div class="form-group">
                <label for="apellidoMaterno">Apellido Materno</label>
                <input type="text" 
                       name="apellidoMaterno" 
                       class="form-control <?php echo form_error('apellidoMaterno') ? 'is-invalid' : ''; ?>"
                       id="apellidoMaterno" 
                       placeholder="Ingrese su apellido materno"
                       minlength="2"
                       maxlength="25"
                       value="<?php echo set_value('apellidoMaterno'); ?>"
                       autocomplete="off"
                       title="Solo se permiten letras, espacios y caracteres especiales permitidos">
                <?php echo form_error('apellidoMaterno', '<div class="invalid-feedback">', '</div>'); ?>
            </div>

            <div class="form-group">
                <label for="carnet">Carnet de Identidad *</label>
                <input type="text" 
                       name="carnet" 
                       class="form-control <?php echo form_error('carnet') ? 'is-invalid' : ''; ?>"
                       id="carnet" 
                       placeholder="Ej: 12345678 o EX-12345678"
                       value="<?php echo set_value('carnet'); ?>"
                       required
                       maxlength="15">
                <?php echo form_error('carnet', '<div class="invalid-feedback">', '</div>'); ?>
                <small class="form-text text-muted">
                    Formatos válidos:
                    <ul class="mb-0">
                        <li>Ciudadanos bolivianos: 4 a 9 dígitos (Ej: 12345678)</li>
                        <li>Extranjeros: 1-2 letras + guión + números (Ej: EX-12345678)</li>
                    </ul>
                </small>
            </div>

            <div class="form-group">
                <label for="fechaNacimiento">Fecha de Nacimiento *</label>
                <input type="date" 
                       name="fechaNacimiento" 
                       class="form-control <?php echo form_error('fechaNacimiento') ? 'is-invalid' : ''; ?>"
                       id="fechaNacimiento" 
                       value="<?php echo set_value('fechaNacimiento'); ?>"
                       required>
                <?php echo form_error('fechaNacimiento', '<div class="invalid-feedback">', '</div>'); ?>
                <small class="form-text text-muted">Debe ser mayor de 15 años.</small>
            </div>

            <div class="form-group">
                <label for="sexo">Sexo *</label>
                <select name="sexo" 
                        class="form-control <?php echo form_error('sexo') ? 'is-invalid' : ''; ?>"
                        id="sexo" 
                        required>
                    <option value="">Seleccione su sexo</option>
                    <option value="M" <?php echo set_value('sexo') == 'M' ? 'selected' : ''; ?>>Masculino</option>
                    <option value="F" <?php echo set_value('sexo') == 'F' ? 'selected' : ''; ?>>Femenino</option>
                </select>
                <?php echo form_error('sexo', '<div class="invalid-feedback">', '</div>'); ?>
            </div>
            <div class="form-group">
                <label for="profesion">Profesión *</label>
                <select name="profesion" 
                        class="form-control <?php echo form_error('profesion') ? 'is-invalid' : ''; ?>"
                        id="profesion" 
                        required>
                    <option value="">Seleccione su profesión</option>
                    <?php foreach($profesiones_lector as $valor => $etiqueta): ?>
                        <option value="<?php echo $valor; ?>" <?php echo set_value('profesion') == $valor ? 'selected' : ''; ?>>
                            <?php echo $etiqueta; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php echo form_error('profesion', '<div class="invalid-feedback">', '</div>'); ?>
            </div>
            <div class="form-group">
                <label for="email">Correo Electrónico *</label>
                <input type="email" 
                       name="email" 
                       class="form-control <?php echo form_error('email') ? 'is-invalid' : ''; ?>"
                       id="email" 
                       placeholder="Ingrese su correo electrónico"
                       maxlength="100"
                       value="<?php echo set_value('email'); ?>"
                       required
                       autocomplete="off">
                <?php echo form_error('email', '<div class="invalid-feedback">', '</div>'); ?>
            </div>

            <div class="form-group">
                <label for="username">Nombre de Usuario *</label>
                <input type="text" 
                       name="username" 
                       class="form-control <?php echo form_error('username') ? 'is-invalid' : ''; ?>"
                       id="username" 
                       placeholder="Ingrese su nombre de usuario"
                       minlength="4"
                       maxlength="20"
                       value="<?php echo set_value('username'); ?>"
                       required
                       autocomplete="off">
                <?php echo form_error('username', '<div class="invalid-feedback">', '</div>'); ?>
            </div>

            <div class="form-group">
                <label for="password">Contraseña *</label>
                <input type="password" 
                       name="password" 
                       class="form-control <?php echo form_error('password') ? 'is-invalid' : ''; ?>"
                       id="password" 
                       placeholder="Ingrese su contraseña"
                       minlength="6"
                       maxlength="20"
                       required>
                <?php echo form_error('password', '<div class="invalid-feedback">', '</div>'); ?>
                <small class="form-text text-muted">Mínimo 6 caracteres</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña *</label>
                <input type="password" 
                       name="confirm_password" 
                       class="form-control <?php echo form_error('confirm_password') ? 'is-invalid' : ''; ?>"
                       id="confirm_password" 
                       placeholder="Confirme su contraseña"
                       required>
                <?php echo form_error('confirm_password', '<div class="invalid-feedback">', '</div>'); ?>
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
            </div>

        <?php echo form_close(); ?>

                            </div> <!-- end card-body -->
                        </div>
                        <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">Volver al <a href="<?php echo site_url('usuarios/index'); ?>" class="text-muted ml-1"><b class="font-weight-semibold">Inicio de sesión</b></a></p>
                        </div>
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
            2024 &copy; Hemeroteca <a href="#" class="text-muted">"José Antonio Arze"</a> 
        </footer>

        <!-- Vendor js -->
        <script src="<?php echo base_url(); ?>adminXeria/light/dist/assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?php echo base_url(); ?>adminXeria/light/dist/assets/js/app.min.js"></script>

        <script>
function toggleProfesionField() {
    var rolSelect = document.getElementById('rol');
    var profesionContainer = document.getElementById('profesionContainer');
    var currentProfesion = document.getElementById('profesion');
    
    if (rolSelect.value === 'lector') {
        var profesionSelect = document.createElement('select');
        profesionSelect.className = 'form-control';
        profesionSelect.id = 'profesion';
        profesionSelect.name = 'profesion';
        profesionSelect.required = true;
        
        var opciones = <?php echo json_encode($profesiones_lector); ?>;
        var optionDefault = document.createElement('option');
        optionDefault.value = '';
        optionDefault.text = 'Seleccione una profesión';
        profesionSelect.appendChild(optionDefault);
        
        for (var valor in opciones) {
            var option = document.createElement('option');
            option.value = valor;
            option.text = opciones[valor];
            profesionSelect.appendChild(option);
        }
        
        currentProfesion.parentNode.replaceChild(profesionSelect, currentProfesion);
    } else {
        var inputText = document.createElement('input');
        inputText.type = 'text';
        inputText.className = 'form-control';
        inputText.id = 'profesion';
        inputText.name = 'profesion';
        inputText.required = true;
        inputText.maxLength = 100;
        inputText.placeholder = 'Ingrese la ocupación';
        
        if (document.getElementById('profesion').tagName === 'SELECT') {
            document.getElementById('profesion').parentNode.replaceChild(inputText, document.getElementById('profesion'));
        }
    }
}

// Función para validar nombres y apellidos
function validarNombresApellidos(event) {
    const patron = /^[a-zA-ZáéíóúüÁÉÍÓÚÜñÑ\s'\-]$/;
    if (!patron.test(event.key) && event.key !== 'Backspace' && event.key !== 'Tab') {
        event.preventDefault();
        return false;
    }
    return true;
}

// Función para convertir texto a mayúsculas manteniendo la posición del cursor
function convertirMayusculas(elemento) {
    const start = elemento.selectionStart;
    const end = elemento.selectionEnd;
    elemento.value = elemento.value.toUpperCase();
    elemento.setSelectionRange(start, end);
}

// Validación para el carnet
function validarInputCarnet(event) {
    const carnet = event.target.value;
    const key = event.key;

    // No permitir espacios o Enter
    if (key === ' ' || key === 'Enter') {
        event.preventDefault();
        return false;
    }

    // Si está vacío
    if (carnet.length === 0) {
        if (!/[\dA-Za-z]/.test(key)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    // FORMATO BOLIVIANO: Solo números (4-9 dígitos)
    if (/^\d+$/.test(carnet)) {
        if (carnet.length >= 9 || !/\d/.test(key)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    // FORMATO EXTRANJERO: Letras + guión + números
    if (/^[A-Za-z]/.test(carnet)) {
        // Si solo tiene letras (máximo 2)
        if (/^[A-Za-z]{1,2}$/.test(carnet)) {
            if (carnet.length === 2 && key !== '-') {
                event.preventDefault();
                return false;
            }
            if (!/[A-Za-z-]/.test(key)) {
                event.preventDefault();
                return false;
            }
        }
        // Si ya tiene el guión
        else if (carnet.includes('-')) {
            const [prefijo, numeros] = carnet.split('-');
            if (prefijo.length > 2 || (numeros && numeros.length >= 9) || !/\d/.test(key)) {
                event.preventDefault();
                return false;
            }
        }
        // Si tiene letras y no tiene guión, solo permitir guión
        else if (!/[-]/.test(key)) {
            event.preventDefault();
            return false;
        }
    }

    return true;
}

// Función para validar el pegado del carnet
function validarPegadoCarnet(event) {
    event.preventDefault();
    const texto = (event.clipboardData || window.clipboardData).getData('text').toUpperCase();
    const patronBoliviano = /^\d{4,9}$/;
    const patronExtranjero = /^[A-Z]{1,2}-\d{1,9}$/;
    if (patronBoliviano.test(texto) || patronExtranjero.test(texto)) {
        event.target.value = texto;
    }
}

// Validación para email
function validarEmail(event) {
    const email = event.target.value;
    const patron = /^[a-zA-Z0-9@._\-]$/;
    if (!patron.test(event.key) && event.key !== 'Backspace' && event.key !== 'Tab') {
        event.preventDefault();
        return false;
    }
    return true;
}

// Validación para username
function validarUsername(event) {
    const patron = /^[a-zA-Z0-9_]$/;
    if (!patron.test(event.key) && event.key !== 'Backspace' && event.key !== 'Tab') {
        event.preventDefault();
        return false;
    }
    return true;
}

// Validación de contraseña
function validarPassword() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    if (password.value.length < 6) {
        password.setCustomValidity('La contraseña debe tener al menos 6 caracteres');
        return false;
    } else {
        password.setCustomValidity('');
    }
    
    if (password.value !== confirmPassword.value) {
        confirmPassword.setCustomValidity('Las contraseñas no coinciden');
        return false;
    } else {
        confirmPassword.setCustomValidity('');
    }
    
    return true;
}

// Inicialización del formulario y validaciones
document.addEventListener('DOMContentLoaded', function() {
    // Configurar fecha máxima para fechaNacimiento
    const fechaNacimiento = document.getElementById('fechaNacimiento');
    if (fechaNacimiento) {
        const maxDate = new Date();
        maxDate.setFullYear(maxDate.getFullYear() - 15);
        fechaNacimiento.max = maxDate.toISOString().split('T')[0];
    }

    // Validaciones para nombres y apellidos
    const camposNombre = ['nombres', 'apellidoPaterno', 'apellidoMaterno'];
    camposNombre.forEach(campo => {
        const elemento = document.getElementById(campo);
        if (elemento) {
            elemento.addEventListener('keypress', validarNombresApellidos);
            elemento.addEventListener('input', function() {
                convertirMayusculas(this);
            });
            elemento.addEventListener('paste', function(e) {
                e.preventDefault();
                const texto = (e.clipboardData || window.clipboardData).getData('text');
                if (/^[a-zA-ZáéíóúüÁÉÍÓÚÜñÑ\s'\-]*$/.test(texto)) {
                    this.value = texto.toUpperCase();
                }
            });
        }
    });

    // Validaciones para carnet
    const carnetInput = document.getElementById('carnet');
    if (carnetInput) {
        carnetInput.addEventListener('keypress', validarInputCarnet);
        carnetInput.addEventListener('paste', validarPegadoCarnet);
        carnetInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }

    // Validaciones para email
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('keypress', validarEmail);
        emailInput.addEventListener('input', function() {
            this.value = this.value.toLowerCase();
        });
    }

    // Validaciones para username
    const usernameInput = document.getElementById('username');
    if (usernameInput) {
        usernameInput.addEventListener('keypress', validarUsername);
        usernameInput.addEventListener('input', function() {
            this.value = this.value.toLowerCase();
        });
    }

    // Validaciones para contraseña
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    if (passwordInput && confirmPasswordInput) {
        passwordInput.addEventListener('input', validarPassword);
        confirmPasswordInput.addEventListener('input', validarPassword);
    }

    // Validación del formulario antes de enviar
    const form = document.getElementById('form-registro-lector');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validarPassword()) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>
    </body>
</html>
