<!-- application/views/usuario/perfil.php -->
<div class="container mt-4">
    <h2>Mi Perfil</h2>
    
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>

    <form action="<?= site_url('usuario/actualizar_perfil') ?>" method="post">
        <div class="form-group">
            <label for="nombres">Nombres</label>
            <input type="text" class="form-control" id="nombres" name="nombres" value="<?= set_value('nombres', $usuario->nombres) ?>" required>
            <?php echo form_error('nombres', '<small class="text-danger">', '</small>'); ?>
        </div>
        <div class="form-group">
            <label for="apellidoPaterno">Apellido Paterno</label>
            <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" value="<?= set_value('apellidoPaterno', $usuario->apellidoPaterno) ?>" required>
            <?php echo form_error('apellidoPaterno', '<small class="text-danger">', '</small>'); ?>
        </div>
        <div class="form-group">
            <label for="apellidoMaterno">Apellido Materno</label>
            <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno" value="<?= set_value('apellidoMaterno', $usuario->apellidoMaterno) ?>">
        </div>
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= set_value('email', $usuario->email) ?>" required>
            <?php echo form_error('email', '<small class="text-danger">', '</small>'); ?>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= set_value('telefono', $usuario->telefono) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
    </form>

    <a href="<?= site_url('usuario/cambiar_contrasena') ?>" class="btn btn-secondary mt-3">Cambiar Contraseña</a>
</div>
