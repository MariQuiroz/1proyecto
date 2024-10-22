<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <title>Verificación de Cuenta</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Verificación de Cuenta</h2>
        <?php if(isset($mensaje)): ?>
            <div class="alert alert-info" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        <p class="text-center">Si no has recibido el correo de verificación, haz clic <a href="<?php echo site_url('usuarios/reenviar_verificacion'); ?>">aquí</a> para reenviar.</p>
    </div>
</body>
</html>
