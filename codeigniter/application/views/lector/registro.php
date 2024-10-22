<!DOCTYPE html> 
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Xeria - Responsive Admin Dashboard Template</title>
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
    </head>

    <body class="authentication-bg authentication-bg-pattern">
        <div class="account-pages mt-5 mb-5">
            <div class="container">
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
                                    <label for="nombres">Nombres</label>
                                    <input type="text" name="nombres" class="form-control" id="nombres" placeholder="Nombres" required>
                                </div>

                                <div class="form-group">
                                    <label for="apellidoPaterno">Apellido Paterno</label>
                                    <input type="text" name="apellidoPaterno" class="form-control" id="apellidoPaterno" placeholder="Apellido Paterno" required>
                                </div>

                                <div class="form-group">
                                    <label for="apellidoMaterno">Apellido Materno</label>
                                    <input type="text" name="apellidoMaterno" class="form-control" id="apellidoMaterno" placeholder="Apellido Materno">
                                </div>

                                <div class="form-group">
                                    <label for="carnet">Carnet</label>
                                    <input type="text" name="carnet" class="form-control" id="carnet" placeholder="Carnet" required>
                                </div>

                                <div class="form-group">
                                    <label for="profesion">Profesión</label>
                                    <select name="profesion" class="form-control" id="profesion" required>
                                        <option value="">Seleccione su profesión</option>
                                        <option value="Estudiante Umss">Estudiante UMSS</option>
                                        <option value="Docente Umss">Docente UMSS</option>
                                        <option value="Investigador">Investigador</option>
                                        <option value="Publico en General">Público en General</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="fechaNacimiento">Fecha de Nacimiento</label>
                                    <input type="date" name="fechaNacimiento" class="form-control" id="fechaNacimiento" required>
                                </div>

                                <div class="form-group">
                                    <label for="sexo">Sexo</label>
                                    <select name="sexo" class="form-control" id="sexo" required>
                                        <option value="">Seleccione su sexo</option>
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="email">Correo Electrónico</label>
                                    <input type="email" name="email" class="form-control" id="email" placeholder="Correo electrónico" required>
                                </div>

                                <div class="form-group">
                                    <label for="username">Nombre de Usuario</label>
                                    <input type="text" name="username" class="form-control" id="username" placeholder="Nombre de usuario" required>
                                </div>

                                <div class="form-group">
                                    <label for="password">Contraseña</label>
                                    <input type="password" name="password" class="form-control" id="password" placeholder="Contraseña" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_password">Repite tu Contraseña</label>
                                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Repite tu Contraseña" required>
                                </div>

                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
                                </div>

                                <?php echo form_close(); ?>

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
            2024 &copy; Hemeroteca theme by <a href="#" class="text-muted">YourName</a> 
        </footer>

        <!-- Vendor js -->
        <script src="<?php echo base_url(); ?>adminXeria/light/dist/assets/js/vendor.min.js"></script>

        <!-- App js -->
        <script src="<?php echo base_url(); ?>adminXeria/light/dist/assets/js/app.min.js"></script>

        <script>
        $(document).ready(function() {
            $('#form-registro-lector').submit(function(e) {
                var password = $('input[name="password"]').val();
                if (password.length < 6) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 6 caracteres.');
                }
            });
        });
        </script>
    </body>
</html>
