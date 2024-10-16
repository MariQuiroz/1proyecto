<div class="content-wrapper">
    <section class="content-header">
        <h1>Configuración <small>Lector</small></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cambiar Contraseña</h3>
                    </div>
                    <?php echo form_open('usuarios/configuracion'); ?>
                        <div class="box-body">
                            <?php if($this->session->flashdata('mensaje')): ?>
                                <div class="alert alert-success"><?php echo $this->session->flashdata('mensaje'); ?></div>
                            <?php endif; ?>
                            <?php if($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="nueva_password">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="nueva_password" name="nueva_password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirmar_password">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </section>
</div>