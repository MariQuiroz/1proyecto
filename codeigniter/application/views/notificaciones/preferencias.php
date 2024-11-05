<div class="container-fluid"> 
    <h2>Preferencias de Notificación</h2>
    
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

    <?php echo form_open('notificaciones/guardar_preferencias'); ?>
        <div class="form-group">
            <label>
                <input type="checkbox" name="notificar_disponibilidad" value="1" <?php echo $preferencias->notificarDisponibilidad ? 'checked' : ''; ?>>
                Notificarme cuando una publicación esté disponible
            </label>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="notificar_email" value="1" <?php echo $preferencias->notificarEmail ? 'checked' : ''; ?>>
                Recibir notificaciones por correo electrónico
            </label>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="notificar_sistema" value="1" <?php echo $preferencias->notificarSistema ? 'checked' : ''; ?>>
                Recibir notificaciones en el sistema
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Preferencias</button>
    <?php echo form_close(); ?>
</div>
