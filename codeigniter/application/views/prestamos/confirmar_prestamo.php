<!-- application/views/prestamos/confirmar_prestamo.php -->
<div class="container-fluid"> 
    <h2>Confirmar Préstamo</h2>
    
    <?php echo form_open('prestamos/crear_desde_reserva/' . $reserva->idReserva); ?>
    
        <div class="form-group">
            <label>Usuario</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($reserva->nombre_usuario) ?>" readonly>
        </div>
        
        <div class="form-group">
            <label>Publicación</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($publicacion->titulo) ?>" readonly>
        </div>
        
        <div class="form-group">
            <label for="fechaDevolucionEsperada">Fecha de Devolución Esperada</label>
            <input type="date" class="form-control" id="fechaDevolucionEsperada" name="fechaDevolucionEsperada" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
        </div>
        
        <p class="text-muted">Por favor, confirme los detalles antes de enviar la solicitud de préstamo.</p>
        
        <button type="submit" class="btn btn-primary">Confirmar Préstamo</button>
    
    <?php echo form_close(); ?>
</div>
