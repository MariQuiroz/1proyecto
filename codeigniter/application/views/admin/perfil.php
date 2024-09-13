<!-- application/views/usuario/perfil.php -->
<div class="container mt-4">
    <h2>Mi Perfil</h2>
    
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>

    <form action="<?= site_url('usuario/actualizar_perfil') ?>" method="post">
        <div class="form-group">
            <label for="nombres">Nombres</label>
            <input type="text" class="form-control" id="nombres" name="nombres" value="<?= $usuario->nombres ?>" required>
        </div>
        <div class="form-group">
            <label for="apellidoPaterno">Apellido Paterno</label>
            <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" value="<?= $usuario->apellidoPaterno ?>" required>
        </div>
        <div class="form-group">
            <label for="apellidoMaterno">Apellido Materno</label>
            <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno" value="<?= $usuario->apellidoMaterno ?>">
        </div>
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= $usuario->email ?>" required>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= $usuario->telefono ?>">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
    </form>
    <?php echo form_open_multipart('usuarios/cambiar_contrasena'); ?>
        <button type="submit" name="buton" class="btn btn-secondary mt-3">Cambiar Contraseña</button>
      <?php echo form_close(); ?>

    <a href="<?= base_url('usuario/cambiar_contrasena') ?>" class="btn btn-secondary mt-3">Cambiar Contraseña</a>
</div>

<br>
<button onclick="goBack()" class="btn btn-secondary mb-3">Volver</button>
<script>
function goBack() {
    window.history.back();
}
</script>
