<div class="container">
    <h2>Solicitar Préstamo</h2>
    <?php echo validation_errors(); ?>
    <?php echo form_open('prestamos/solicitar'); ?>
        <div class="form-group">
            <label for="idPublicacion">Publicación:</label>
            <select name="idPublicacion" class="form-control" required>
                <?php foreach ($publicaciones as $publicacion): ?>
                    <option value="<?php echo $publicacion->idPublicacion; ?>"><?php echo $publicacion->titulo; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="fechaSolicitud">Fecha de Solicitud:</label>
            <input type="date" name="fechaSolicitud" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="motivoConsulta">Motivo de Consulta:</label>
            <textarea name="motivoConsulta" class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
    <?php echo form_close(); ?>
</div>