<div class="container mt-4">
    <h2>Editar Publicación</h2>

    <form action="<?= site_url('publicaciones/editarbd') ?>" method="post">
        <input type="hidden" name="idPublicacion" value="<?= $publicacion->idPublicacion ?>">
        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" class="form-control" id="titulo" name="titulo" value="<?= $publicacion->titulo ?>" required>
        </div>
        <div class="form-group">
            <label for="editorial">Editorial</label>
            <input type="text" class="form-control" id="editorial" name="editorial" value="<?= $publicacion->editorial ?>" required>
        </div>
        <div class="form-group">
            <label for="diaPublicacion">Día de Publicación</label>
            <input type="number" class="form-control" id="diaPublicacion" name="diaPublicacion" min="1" max="31" value="<?= $publicacion->diaPublicacion ?>">
        </div>
        <div class="form-group">
            <label for="mesPublicacion">Mes de Publicación</label>
            <input type="number" class="form-control" id="mesPublicacion" name="mesPublicacion" min="1" max="12" value="<?= $publicacion->mesPublicacion ?>">
        </div>
        <div class="form-group">
            <label for="añoPublicacion">Año de Publicación</label>
            <input type="number" class="form-control" id="añoPublicacion" name="añoPublicacion" value="<?= $publicacion->añoPublicacion ?>" required>
        </div>
        <div class="form-group">
            <label for="tipo">Tipo</label>
            <select class="form-control" id="tipo" name="tipo" required>
                <option value="periodico" <?= $publicacion->tipo == 'periodico' ? 'selected' : '' ?>>Periódico</option>
                <option value="gaceta" <?= $publicacion->tipo == 'gaceta' ? 'selected' : '' ?>>Gaceta</option>
            </select>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= $publicacion->descripcion ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Publicación</button>
    </form>
</div>
