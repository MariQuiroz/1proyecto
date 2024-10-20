<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid overflow-auto" style="max-height: 500px;">
                                   
<div class="content-wrapper">
    <section class="content-header">
        <h1>Configuración <small><?php echo ucfirst($this->session->userdata('rol')); ?></small></h1>
    </section>
    
    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cambiar Nombre de Usuario y Contraseña</h3>
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
                                <label for="nuevo_username">Nuevo Nombre de Usuario</label>
                                <input type="text" class="form-control" id="nuevo_username" name="nuevo_username" value="<?php echo set_value('nuevo_username', $usuario->username); ?>" required>
                            </div>
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
                            <button type="submit" class="btn btn-primary">Actualizar Configuración</button>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </section>
</div>
</div> <!-- container -->

</div> <!-- content -->

