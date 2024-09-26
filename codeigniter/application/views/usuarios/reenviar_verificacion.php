<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reenviar Correo de Verificación</title>
    <!-- Aquí puedes incluir tus estilos CSS -->
</head>
<body>
    <h2>Reenviar Correo de Verificación</h2>

    <?php if ($this->session->flashdata('mensaje')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('mensaje') ?></div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
    <?php endif; ?>

    <?php echo validation_errors(); ?>

    <?php echo form_open('usuarios/reenviar_verificacion'); ?>
        <div>
            <label for="email">Correo electrónico:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <input type="submit" value="Reenviar Correo de Verificación">
        </div>
    <?php echo form_close(); ?>

    <p><a href="<?= site_url('usuarios/login') ?>">Volver al inicio de sesión</a></p>
</body>
</html>