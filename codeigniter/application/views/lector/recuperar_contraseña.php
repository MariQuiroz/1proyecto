<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Incluye los mismos estilos que en tu vista de login -->
</head>
<body class="authentication-bg authentication-bg-pattern">
    <div class="account-pages mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-body p-4">
                            <h5 class="auth-title">Recuperar Contraseña</h5>
                            <?php echo form_open('usuarios/enviar_recuperacion'); ?>
                                <div class="form-group mb-3">
                                    <label for="email">Correo Electrónico</label>
                                    <?php echo form_input(['name' => 'email', 'class' => 'form-control', 'type' => 'email', 'id' => 'email', 'required' => '', 'placeholder' => 'Ingrese su correo electrónico']); ?>
                                </div>
                                <div class="form-group mb-0 text-center">
                                    <?php echo form_submit(['class' => 'btn btn-danger btn-block', 'value' => 'Enviar instrucciones']); ?>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">Volver al <a href="<?php echo site_url('usuarios/index'); ?>" class="text-muted ml-1"><b class="font-weight-semibold">Inicio de sesión</b></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Incluye los mismos scripts que en tu vista de login -->
</body>
</html>