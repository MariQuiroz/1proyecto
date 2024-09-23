<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación de Cuenta</title>
</head>
<body>
    <h2>Verificación de Cuenta</h2>
    <?php if(isset($mensaje)): ?>
        <p><?php echo $mensaje; ?></p>
    <?php endif; ?>
    <p>Si no has recibido el correo de verificación, haz clic <a href="<?php echo site_url('usuarios/reenviar_verificacion'); ?>">aquí</a> para reenviar.</p>
</body>
</html>