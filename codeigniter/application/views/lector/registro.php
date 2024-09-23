<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Incluye los mismos estilos que en tu vista de login -->
</head>
<body class="authentication-bg authentication-bg-pattern">
    <div class="account-pages mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-body p-4">
                            <h5 class="auth-title">Registro de Usuario</h5>
                            <?php echo validation_errors(); ?>
                            <?php echo form_open('usuarios/registro'); ?>
                                <div class="form-group mb-3">
                                    <label for="nombres">Nombres</label>
                                    <?php echo form_input(['name' => 'nombres', 'class' => 'form-control', 'type' => 'text', 'id' => 'nombres', 'required' => '', 'placeholder' => 'Ingrese sus nombres']); ?>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="apellidoPaterno">Apellido Paterno</label>
                                    <?php echo form_input(['name' => 'apellidoPaterno', 'class' => 'form-control', 'type' => 'text', 'id' => 'apellidoPaterno', 'required' => '', 'placeholder' => 'Ingrese su apellido paterno']); ?>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="apellidoMaterno">Apellido Materno</label>
                                    <?php echo form_input(['name' => 'apellidoMaterno', 'class' => 'form-control', 'type' => 'text', 'id' => 'apellidoMaterno', 'placeholder' => 'Ingrese su apellido materno']); ?>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="carnet">Carnet</label>
                                    <?php echo form_input(['name' => 'carnet', 'class' => 'form-control', 'type' => 'text', 'id' => 'carnet', 'required' => '', 'placeholder' => 'Ingrese su carnet']); ?>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="profesion">Profesión</label>
                                    <?php echo form_dropdown('profesion', array('' => 'Seleccione su profesion', 'Estudiante Umss' => 'Estudiante Umss', 'Docente Umss' => 'Docente Umss', 'Investigador' => 'Investigador', 'Publico en General' => 'Publico en General'), '', 'class="form-control" required'); ?>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="fechaNacimiento">Fecha de Nacimiento</label>
                                    <?php echo form_input(['name' => 'fechaNacimiento', 'class' => 'form-control', 'type' => 'date', 'id' => 'fechaNacimiento']); ?>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="sexo">Sexo</label>
                                    <?php echo form_dropdown('sexo', array('' => 'Seleccione su sexo', 'M' => 'Masculino', 'F' => 'Femenino'), '', 'class="form-control" required'); ?>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="email">Correo Electrónico</label>
                                    <?php echo form_input(['name' => 'email', 'class' => 'form-control', 'type' => 'email', 'id' => 'email', 'required' => '', 'placeholder' => 'Ingrese su correo electrónico']); ?>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="username">Nombre de Usuario</label>
                                    <?php echo form_input(['name' => 'username', 'class' => 'form-control', 'type' => 'text', 'id' => 'username', 'required' => '', 'placeholder' => 'Ingrese un nombre de usuario']); ?>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password">Contraseña</label>
                                    <?php echo form_password(['name' => 'password', 'class' => 'form-control', 'id' => 'password', 'required' => '', 'placeholder' => 'Ingrese una contraseña']); ?>
                                </div>
                                <div class="form-group mb-0 text-center">
                                    <?php echo form_submit(['class' => 'btn btn-danger btn-block', 'value' => 'Registrarse']); ?>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">¿Ya tienes una cuenta? <a href="<?php echo site_url('usuarios/index'); ?>" class="text-muted ml-1"><b class="font-weight-semibold">Inicia sesión</b></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Incluye los mismos scripts que en tu vista de login -->
</body>
</html>