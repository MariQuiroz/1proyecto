<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <!-- Incluye aquí tus estilos CSS -->
</head>
<body>
    <div class="container">
        <h2>Recuperar Contraseña</h2>
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php endif; ?>
        <?php if($this->session->flashdata('mensaje')): ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('mensaje'); ?></div>
        <?php endif; ?>
        <?php echo form_open('usuarios/recuperar_contrasena'); ?>
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Correo de Recuperación</button>
        <?php echo form_close(); ?>
        <p><a href="<?php echo site_url('usuarios/login'); ?>">Volver al inicio de sesión</a></p>
    </div>
</body>
</html>