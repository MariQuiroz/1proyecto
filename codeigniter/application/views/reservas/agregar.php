<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Realizar Nueva Reserva</h2>
            <?php echo form_open_multipart('reservas/agregarbd'); ?>
                <input type="hidden" name="idPublicacion" value="<?= $publicacion->idPublicacion ?>">
                <div class="form-group">
                    <label>Publicación</label>
                    <input type="text" class="form-control" value="<?= $publicacion->titulo ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="fechaReserva">Fecha de Reserva</label>
                    <input type="date" class="form-control" id="fechaReserva" name="fechaReserva" required min="<?= date('Y-m-d') ?>">
                </div>
                <button type="submit" class="btn btn-primary">REALIZAR RESERVA</button>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<br>
<button onclick="goBack()" class="btn btn-secondary mb-3">Volver</button>
<script>
function goBack() {
    window.history.back();
}
</script>