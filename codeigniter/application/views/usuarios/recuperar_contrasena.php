<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Recuperar Contraseña - Hemeroteca</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta content="Recupera tu contraseña de la Hemeroteca" name="description" />
    <link rel="shortcut icon" href="<?php echo base_url(); ?>adminXeria/light/dist/assets/images/favicon.ico"/>
    
    <!-- App css -->
    <link href="<?php echo base_url(); ?>adminXeria/light/dist/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>adminXeria/light/dist/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>adminXeria/light/dist/assets/css/app.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="authentication-bg authentication-bg-pattern">
    <div class="account-pages mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-body p-4">
                            <h5 class="auth-title">Recuperar Contraseña</h5>

                            <?php if ($this->session->flashdata('mensaje')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('mensaje'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('error'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                            
                            <?php echo form_open('usuarios/recuperar_contrasena'); ?>
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

    <!-- Vendor js -->
    <script src="<?php echo base_url(); ?>adminXeria/light/dist/assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="<?php echo base_url(); ?>adminXeria/light/dist/assets/js/app.min.js"></script>
</body>
</html>
