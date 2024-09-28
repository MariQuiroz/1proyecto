<div class="container">
    <h2>Mis Préstamos</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Publicación</th>
                <th>Fecha de Préstamo</th>
                <th>Fecha de Devolución</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prestamos as $prestamo): ?>
                <tr>
                    <td><?php echo $prestamo->tituloPublicacion; ?></td>
                    <td><?php echo $prestamo->fechaPrestamo; ?></td>
                    <td><?php echo $prestamo->fechaDevolucionEstimada; ?></td>
                    <td><?php echo $prestamo->estado; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>