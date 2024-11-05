<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Modificar Usuario - Hemeroteca</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta content="Modificación de usuario para la Hemeroteca" name="description" />
    <meta content="Hemeroteca" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo base_url('adminXeria/dist/assets/images/favicon.ico'); ?>"/>

    <!-- App css -->
    <link href="<?php echo base_url('adminXeria/dist/assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('adminXeria/dist/assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('adminXeria/dist/assets/css/app.min.css'); ?>" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="container-fluid"> 
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Modificar Usuario</h4>

                        <?php if ($this->session->flashdata('mensaje')): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $this->session->flashdata('mensaje'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $this->session->flashdata('error'); ?>
                            </div>
                        <?php endif; ?>

                        <?php echo form_open('usuarios/modificar/' . $usuario->idUsuario, ['class' => 'form-horizontal']); ?>

                        <div class="form-group row">
                            <label for="nombres" class="col-sm-3 col-form-label">Nombres</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="nombres" name="nombres" value="<?php echo htmlspecialchars($usuario->nombres); ?>" required placeholder="Ej: Juan Pérez">
                                <?php echo form_error('nombres', '<small class="text-danger">', '</small>'); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="apellidoPaterno" class="col-sm-3 col-form-label">Apellido Paterno</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" value="<?php echo htmlspecialchars($usuario->apellidoPaterno); ?>" required placeholder="Ej: Pérez">
                                <?php echo form_error('apellidoPaterno', '<small class="text-danger">', '</small>'); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="apellidoMaterno" class="col-sm-3 col-form-label">Apellido Materno</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno" value="<?php echo htmlspecialchars($usuario->apellidoMaterno); ?>" placeholder="Ej: Gómez">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="carnet" class="col-sm-3 col-form-label">Carnet</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="carnet" name="carnet" value="<?php echo htmlspecialchars($usuario->carnet); ?>" required placeholder="Ej: 123456">
                                <?php echo form_error('carnet', '<small class="text-danger">', '</small>'); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="profesion" class="col-sm-3 col-form-label">Profesión</label>
                            <div class="col-sm-9">
                                <select name="profesion" class="form-control" required>
                                    <option value="">Seleccione su profesión</option>
                                    <option value="Estudiante Umss" <?php echo ($usuario->profesion == 'Estudiante Umss') ? 'selected' : ''; ?>>Estudiante UMSS</option>
                                    <option value="Docente Umss" <?php echo ($usuario->profesion == 'Docente Umss') ? 'selected' : ''; ?>>Docente UMSS</option>
                                    <option value="Investigador" <?php echo ($usuario->profesion == 'Investigador') ? 'selected' : ''; ?>>Investigador</option>
                                    <option value="Publico en General" <?php echo ($usuario->profesion == 'Publico en General') ? 'selected' : ''; ?>>Público en General</option>
                                </select>
                                <?php echo form_error('profesion', '<small class="text-danger">', '</small>'); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="fechaNacimiento" class="col-sm-3 col-form-label">Fecha de Nacimiento</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" value="<?php echo htmlspecialchars($usuario->fechaNacimiento); ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sexo" class="col-sm-3 col-form-label">Sexo</label>
                            <div class="col-sm-9">
                                <select name="sexo" class="form-control" required>
                                    <option value="">Seleccione su sexo</option>
                                    <option value="M" <?php echo ($usuario->sexo == 'M') ? 'selected' : ''; ?>>Masculino</option>
                                    <option value="F" <?php echo ($usuario->sexo == 'F') ? 'selected' : ''; ?>>Femenino</option>
                                </select>
                                <?php echo form_error('sexo', '<small class="text-danger">', '</small>'); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario->email); ?>" required>
                                <?php echo form_error('email', '<small class="text-danger">', '</small>'); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="rol" class="col-sm-3 col-form-label">Rol</label>
                            <div class="col-sm-9">
                                <select name="rol" class="form-control" required>
                                    <option value="">Seleccione el rol</option>
                                    <option value="administrador" <?php echo ($usuario->rol == 'administrador') ? 'selected' : ''; ?>>Administrador</option>
                                    <option value="encargado" <?php echo ($usuario->rol == 'encargado') ? 'selected' : ''; ?>>Encargado</option>
                                    <option value="lector" <?php echo ($usuario->rol == 'lector') ? 'selected' : ''; ?>>Lector</option>
                                </select>
                                <?php echo form_error('rol', '<small class="text-danger">', '</small>'); ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                <a href="<?php echo site_url('usuarios/lista'); ?>" class="btn btn-secondary">Cancelar</a>
                            </div>
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
</body>
</html>
