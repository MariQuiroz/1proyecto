<!DOCTYPE html>
<html>
<head>
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/adminlte.min.css'); ?>">
</head>
<body>
    <div class="container mt-5">
        <h1>Restablecer Contraseña</h1>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger">
                <?= $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>
        <form method="post" action="<?= site_url('hemeroteca/reset_password/'.$token); ?>">
            <div class="form-group">
                <label for="new_password">Nueva Contraseña</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
        </form>
    </div>
    <script src="<?= base_url('assets/js/bootstrap.min.js'); ?>"></script>
</body>
</html>
