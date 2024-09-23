<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
</head>
<body>
    <h2>Registro de Nuevo Usuario</h2>
    <?php echo validation_errors(); ?>
    <?php echo form_open('usuarios/registrar'); ?>
        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" required>
        <br>
        <label for="confirm_password">Confirmar Contraseña:</label>
        <input type="password" name="confirm_password" required>
        <br>
        <input type="submit" value="Registrar">
    <?php echo form_close(); ?>
</body>
</html>