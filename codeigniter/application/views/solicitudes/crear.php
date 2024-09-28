
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Crear Solicitud de Préstamo</h4>
                <p class="text-muted font-13 mb-4">
                    Complete el formulario para solicitar el préstamo de una publicación.
                </p>

                <?php echo form_open('solicitudes/crear'); ?>
                    <div class="mb-3">
                        <label for="idPublicacion" class="form-label">Seleccione la Publicación</label>
                        <select class="form-control" id="idPublicacion" name="idPublicacion" required>
                            <?php foreach ($publicaciones as $publicacion): ?>
                                <option value="<?php echo $publicacion->idPublicacion; ?>">
                                    <?php echo $publicacion->titulo; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>