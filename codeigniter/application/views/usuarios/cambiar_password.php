
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Cambiar Contraseña</h4>

                        <?php if($this->session->flashdata('mensaje')): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $this->session->flashdata('mensaje'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $this->session->flashdata('error'); ?>
                            </div>
                        <?php endif; ?>

                        <?php echo form_open('usuarios/cambiar_password', ['id' => 'form-cambiar-password']); ?>

                        <div class="form-group">
                            <label for="password_actual">Contraseña Actual</label>
                            <input type="password" class="form-control" id="password_actual" name="password_actual" required>
                        </div>

                        <div class="form-group">
                            <label for="nueva_password">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="nueva_password" name="nueva_password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirmar_password">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor js -->
    <script src="<?php echo base_url('adminXeria/dist/assets/js/vendor.min.js'); ?>"></script>

    <!-- App js -->
    <script src="<?php echo base_url('adminXeria/dist/assets/js/app.min.js'); ?>"></script>

    <script>
    $(document).ready(function() {
        $('#form-cambiar-password').submit(function(e) {
            var nuevaPassword = $('#nueva_password').val();
            var confirmarPassword = $('#confirmar_password').val();
            if (nuevaPassword.length < 6) {
                e.preventDefault();
                alert('La nueva contraseña debe tener al menos 6 caracteres.');
            } else if (nuevaPassword !== confirmarPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden.');
            }
        });
    });
    </script>
