<!-- application/views/usuario/historial.php -->
<div class="container mt-4">
    <h2>Mi Historial</h2>
    
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="prestamos-tab" data-toggle="tab" href="#prestamos" role="tab" aria-controls="prestamos" aria-selected="true">Préstamos</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="reservas-tab" data-toggle="tab" href="#reservas" role="tab" aria-controls="reservas" aria-selected="false">Reservas</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="prestamos" role="tabpanel" aria-labelledby="prestamos-tab">
            <?php if (empty($historial_prestamos)): ?>
                <p class="mt-3">No tienes historial de préstamos.</p>
            <?php else: ?>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Publicación</th>
                            <th>Fecha de Préstamo</th>
                            <th>Fecha de Devolución</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial_prestamos as $prestamo): ?>
                            <tr>
                                <td><?= $prestamo->titulo ?></td>
                                <td><?= date('d/m/Y', strtotime($prestamo->fechaPrestamo)) ?></td>
                                <td><?= $prestamo->fechaDevolucionReal ? date('d/m/Y', strtotime($prestamo->fechaDevolucionReal)) : 'Pendiente' ?></td>
                                <td><?= $prestamo->estado == 1 ? 'Activo' : 'Devuelto' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <div class="tab-pane fade" id="reservas" role="tabpanel" aria-labelledby="reservas-tab">
            <?php if (empty($historial_reservas)): ?>
                <p class="mt-3">No tienes historial de reservas.</p>
            <?php else: ?>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Publicación</th>
                            <th>Fecha de Reserva</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial_reservas as $reserva): ?>
                            <tr>
                                <td><?= $reserva->titulo ?></td>
                                <td><?= date('d/m/Y', strtotime($reserva->fechaReserva)) ?></td>
                                <td>
                                    <?php
                                    switch ($reserva->estado) {
                                        case 1:
                                            echo 'Activa';
                                            break;
                                        case 2:
                                            echo 'Finalizada';
                                            break;
                                        case 3:
                                            echo 'Cancelada';
                                            break;
                                        case 4:
                                            echo 'No efectivizada';
                                            break;
                                        default:
                                            echo 'Desconocido';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>