<!-- application/views/usuario/perfil.php -->
<div class="container-fluid"> 
    <h2>Mi Perfil</h2>
    
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>

    <?= form_open('usuario/actualizar_perfil'); ?>
        <div class="form-group">
            <label for="nombres">Nombres</label>
            <input type="text" class="form-control" id="nombres" name="nombres" value="<?= set_value('nombres', $usuario->nombres) ?>" required>
            <?= form_error('nombres', '<small class="text-danger">', '</small>') ?>
        </div>
        <div class="form-group">
            <label for="apellidoPaterno">Apellido Paterno</label>
            <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" value="<?= set_value('apellidoPaterno', $usuario->apellidoPaterno) ?>" required>
            <?= form_error('apellidoPaterno', '<small class="text-danger">', '</small>') ?>
        </div>
        <div class="form-group">
            <label for="apellidoMaterno">Apellido Materno</label>
            <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno" value="<?= set_value('apellidoMaterno', $usuario->apellidoMaterno) ?>">
            <?= form_error('apellidoMaterno', '<small class="text-danger">', '</small>') ?>
        </div>
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= set_value('email', $usuario->email) ?>" required>
            <?= form_error('email', '<small class="text-danger">', '</small>') ?>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= set_value('telefono', $usuario->telefono) ?>">
            <?= form_error('telefono', '<small class="text-danger">', '</small>') ?>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    <?= form_close(); ?>

    <a href="<?= base_url('usuario/cambiar_contrasena') ?>" class="btn btn-secondary mt-3">Cambiar Contraseña</a>

    <br>
    <button onclick="goBack()" class="btn btn-secondary mb-3">Volver</button>
    <script>
    function goBack() {
        window.history.back();
    }
    </script>
</div>
