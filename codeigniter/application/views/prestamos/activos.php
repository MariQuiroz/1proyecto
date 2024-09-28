<!-- application/views/prestamos/activos.php -->
<div class="container">
    <h2>Préstamos Activos</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Publicación</th>
                <th>Fecha Préstamo</th>
                <th>Fecha Devolución Estimada</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prestamos as $prestamo): ?>
                <tr>
                    <td><?php echo $prestamo->nombres . ' ' . $prestamo->apellidos; ?></td>
                    <td><?php echo $prestamo->titulo; ?></td>
                    <td><?php echo $prestamo->fechaPrestamo; ?></td>
                    <td><?php echo $prestamo->fechaDevolucionEstimada; ?></td>
                    <td>
                        <a href="<?php echo site_url('prestamos/devolver/'.$prestamo->idPrestamo); ?>" class="btn btn-primary btn-sm">Devolver</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>