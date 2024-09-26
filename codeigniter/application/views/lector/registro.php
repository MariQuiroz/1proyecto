

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Registro de Lector</h2>

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

                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="text" name="nombres" class="form-control" placeholder="Nombres" required>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" name="apellidoPaterno" class="form-control" placeholder="Apellido Paterno" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="apellidoMaterno" class="form-control" placeholder="Apellido Materno">
                        </div>

                        <div class="form-group">
                            <input type="text" name="carnet" class="form-control" placeholder="Carnet" required>
                        </div>

                        <div class="form-group">
                            <select name="profesion" class="form-control" required>
                                <option value="">Seleccione su profesión</option>
                                <option value="Estudiante Umss">Estudiante UMSS</option>
                                <option value="Docente Umss">Docente UMSS</option>
                                <option value="Investigador">Investigador</option>
                                <option value="Publico en General">Público en General</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="date" name="fechaNacimiento" class="form-control" placeholder="Fecha de nacimiento">
                        </div>

                        <div class="form-group">
                            <select name="sexo" class="form-control" required>
                                <option value="">Seleccione su sexo</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
                        </div>

                        <div class="form-group">
                            <input type="text" name="username" class="form-control" placeholder="Nombre de usuario" required>
                        </div>

                        <div class="form-group">
                            <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                        </div>
                        
                            <input type="password" name="confirm_password"sword" class="form-control" placeholder="Repite tu Contraseña"  required>
                        </div>
                        
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
                        </div>

                        <?php echo form_close(); ?>
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