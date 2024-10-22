<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Preferencias de Notificación - Hemeroteca</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta content="Preferencias de notificación para usuarios de la Hemeroteca" name="description" />
    <meta content="Hemeroteca" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo base_url('adminXeria/dist/assets/images/favicon.ico'); ?>"/>

    <!-- App css -->
    <link href="<?php echo base_url('adminXeria/dist/assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('adminXeria/dist/assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('adminXeria/dist/assets/css/app.min.css'); ?>" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Preferencias de Notificación</h4>

                        <?php if($this->session->flashdata('mensaje')): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $this->session->flashdata('mensaje'); ?>
                            </div>
                        <?php endif; ?>

                        <?php echo form_open('usuarios/actualizar_preferencias_notificacion'); ?>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="notificar_email" name="notificar_email" <?php echo isset($preferencias['email']) && $preferencias['email'] ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="notificar_email">Recibir notificaciones por correo electrónico</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="notificar_sistema" name="notificar_sistema" <?php echo isset($preferencias['sistema']) && $preferencias['sistema'] ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="notificar_sistema">Recibir notificaciones en el sistema</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar Preferencias</button>

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
</body>
</html>