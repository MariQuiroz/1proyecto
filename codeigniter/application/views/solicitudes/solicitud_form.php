<div class="container">
    <h2>Solicitud de Préstamo para "<?php echo $publicacion->titulo; ?>"</h2>
    <?php echo form_open('solicitudes/crear/' . $publicacion->idPublicacion); ?>
    
    <div class="form-group">
        <label for="motivo">Motivo de la Solicitud</label>
        <textarea name="motivo" class="form-control" required></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
    <?php echo form_close(); ?>
</div>