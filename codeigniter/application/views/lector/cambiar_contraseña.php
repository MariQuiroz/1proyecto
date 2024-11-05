<!-- application/views/usuario/cambiar_contrasena.php -->
<div class="container-fluid"> 
    <h2>Cambiar Contraseña</h2>
    
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($this->session->flashdata('success')) ?></div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($this->session->flashdata('error')) ?></div>
    <?php endif; ?>

    <form action="<?= site_url('usuario/actualizar_contrasena') ?>" method="post">
        <div class="form-group">
            <label for="contrasena_actual">Contraseña Actual</label>
            <input type="password" class="form-control" id="contrasena_actual" name="contrasena_actual" required>
        </div>
        <div class="form-group">
            <label for="nueva_contrasena">Nueva Contraseña</label>
            <input type="password" class="form-control" id="nueva_contrasena" name="nueva_contrasena" required>
        </div>
        <div class="form-group">
            <label for="confirmar_contrasena">Confirmar Nueva Contraseña</label>
            <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" required>
        </div>
        <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
    </form>

    <a href="<?= site_url('usuario/perfil') ?>" class="btn btn-secondary mt-3">Volver al Perfil</a>
</div>
