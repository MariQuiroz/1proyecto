<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 500px;
            margin-top: 50px;
        }
    </style>
</head>
<body>
<div class="container-fluid"> 
        <h2>Cambiar Contraseña</h2>
        <?php if (validation_errors()): ?>
            <div class="alert alert-danger"><?php echo validation_errors(); ?></div>
        <?php endif; ?>
        <?php echo form_open('usuarios/cambiar_password'); ?>
            <div class="form-group">
                <label for="current_password">Contraseña Actual:</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="new_password">Nueva Contraseña:</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm_new_password">Confirmar Nueva Contraseña:</label>
                <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
            <a href="<?php echo site_url('usuarios/perfil'); ?>" class="btn btn-secondary">Cancelar</a>
        <?php echo form_close(); ?>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            const newPassword = document.querySelector('input[name="new_password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_new_password"]').value;

            if (newPassword !== confirmPassword) {
                alert('Las contraseñas nuevas no coinciden. Por favor, verifícalas.');
                event.preventDefault(); // Previene el envío del formulario
            }
        });
    </script>
</body>
</html>
