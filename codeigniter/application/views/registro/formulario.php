<!-- application/views/registro/formulario.php -->
<div class="container-fluid">
    <h2>Registro de Usuario</h2>
    <?php echo form_open('registro/procesar'); ?>
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrarse</button>
    <?php echo form_close(); ?>
</div>