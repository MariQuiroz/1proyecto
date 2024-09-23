<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
</head>
<body>
    <h2>Cambiar Contraseña</h2>
    <?php echo validation_errors(); ?>
    <?php echo form_open('usuarios/cambiar_password'); ?>
        <label for="current_password">Contraseña Actual:</label>
        <input type="password" name="current_password" required>
        <br>
        <label for="new_password">Nueva Contraseña:</label>
        <input type="password" name="new_password" required>
        <br>
        <label for="confirm_new_password">Confirmar Nueva Contraseña:</label>
        <input type="password" name="confirm_new_password" required>
        <br>
        <input type="submit" value="Cambiar Contraseña">
    <?php echo form_close(); ?>
</body>
</html>