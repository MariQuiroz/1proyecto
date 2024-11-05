<div class="container-fluid"> 
    <h2>Solicitar Préstamo</h2>
    
    <!-- Mostrar errores de validación -->
    <?php if (validation_errors()): ?>
        <div class="alert alert-danger">
            <?= validation_errors(); ?>
        </div>
    <?php endif; ?>
    
    <?php echo form_open('prestamos/solicitar'); ?>
        <div class="form-group">
            <label for="idPublicacion">Publicación:</label>
            <select name="idPublicacion" class="form-control" required>
                <option value="">Seleccione una publicación</option>
                <?php foreach ($publicaciones as $publicacion): ?>
                    <option value="<?= htmlspecialchars($publicacion->idPublicacion); ?>"><?= htmlspecialchars($publicacion->titulo); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="fechaSolicitud">Fecha de Solicitud:</label>
            <input type="date" name="fechaSolicitud" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="motivoConsulta">Motivo de Consulta:</label>
            <textarea name="motivoConsulta" class="form-control" rows="3" placeholder="Escribe tu motivo de consulta aquí..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
        <a href="<?= site_url('prestamos'); ?>" class="btn btn-secondary">Cancelar</a>
    <?php echo form_close(); ?>
</div>
