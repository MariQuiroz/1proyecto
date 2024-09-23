<!-- application/views/reset_password/nueva_password.php -->
<div class="container">
    <h2>Ingresa tu Nueva Contraseña</h2>
    <?php echo form_open_multipart('reset_password/actualizar'); ?>
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <div class="form-group">
            <label for="nueva_password">Nueva Contraseña</label>
            <input type="password" class="form-control" id="nueva_password" name="nueva_password" required>
        </div>
        <div class="form-group">
            <label for="confirmar_password">Confirmar Contraseña</label>
            <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
    <?php echo form_close(); ?>
</div>