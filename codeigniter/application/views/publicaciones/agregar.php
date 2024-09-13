<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Agregar Publicación</h2>
            <?php echo form_open_multipart('publicaciones/agregarbd'); ?>
                <div class="form-group">
                    <input type="text" name="titulo" class="form-control" placeholder="Título de la publicación" required>
                </div>
                <div class="form-group">
                    <input type="text" name="editorial" class="form-control" placeholder="Editorial" required>
                </div>
                <div class="form-group">
                    <input type="number" name="diaPublicacion" class="form-control" placeholder="Día de publicación" min="1" max="31">
                </div>
                <div class="form-group">
                    <input type="number" name="mesPublicacion" class="form-control" placeholder="Mes de publicación" min="1" max="12">
                </div>
                <div class="form-group">
                    <input type="number" name="añoPublicacion" class="form-control" placeholder="Año de publicación" required>
                </div>
                <div class="form-group">
                    <select name="tipo" class="form-control" required>
                        <option value="">Seleccione el tipo</option>
                        <option value="periodico">Periódico</option>
                        <option value="gaceta">Gaceta</option>
                    </select>
                </div>
                <div class="form-group">
                    <textarea name="descripcion" class="form-control" placeholder="Descripción" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <input type="file" name="portada" class="form-control-file">
                </div>
                <button type="submit" class="btn btn-primary">AGREGAR PUBLICACIÓN</button>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
