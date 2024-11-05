<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <title>Registro de Usuario</title>
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
            width: 400px; /* Ajusta el ancho según sea necesario */
        }
    </style>
</head>
<body>
    <div class="container-fluid"> 
        <h2 class="text-center">Registro de Nuevo Usuario</h2>
        
        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
        
        <?php echo form_open('usuarios/registrar'); ?>
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" class="form-control" name="email" placeholder="ejemplo@dominio.com" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" name="password" placeholder="Mínimo 6 caracteres" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Registrar</button>
        <?php echo form_close(); ?>

        <p class="text-center mt-3"><a href="<?php echo site_url('usuarios/login'); ?>">¿Ya tienes una cuenta? Inicia sesión aquí.</a></p>
    </div>
</body>
</html>
