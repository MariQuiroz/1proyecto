<!-- application/views/prestamos/agregar.php -->
<div class="container">
    <h2>Registrar Nuevo Préstamo</h2>
    <?php echo form_open_multipart('prestamos/agregarbd'); ?>
        <div class="form-group">
            <label for="idUsuario">Usuario</label>
            <select class="form-control" id="idUsuario" name="idUsuario" required>
                <option value="">Seleccione un usuario</option>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario->idUsuario ?>"><?= $usuario->nombres . ' ' . $usuario->apellidoPaterno ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="idPublicacion">Publicación</label>
            <select class="form-control" id="idPublicacion" name="idPublicacion" required>
                <option value="">Seleccione una publicación</option>
                <?php foreach ($publicaciones as $publicacion): ?>
                    <option value="<?= $publicacion->idPublicacion ?>"><?= $publicacion->titulo ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="fechaDevolucionEsperada">Fecha de Devolución Esperada</label>
            <input type="date" class="form-control" id="fechaDevolucionEsperada" name="fechaDevolucionEsperada" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Registrar Préstamo</button>
    <?php echo form_close(); ?>
</div>

<script>
function goBack() {
    window.history.back();
}
</script>