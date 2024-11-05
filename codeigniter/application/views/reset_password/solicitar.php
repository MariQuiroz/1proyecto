<!-- application/views/reset_password/solicitar.php -->
<div class="container-fluid"> 
    <h2>Restablecer Contraseña</h2>
    <?php echo form_open_multipart('reset_password/solicitar'); ?>
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Solicitar Restablecimiento</button>
    <?php echo form_close(); ?>
</div>