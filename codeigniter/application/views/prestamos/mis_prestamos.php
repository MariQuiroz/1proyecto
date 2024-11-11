<div class="content-page">
    <div class="content">
        <div class="container-fluid"> 
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Mis Préstamos</h4>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Listado de Mis Préstamos</h4>
                            <p class="text-muted font-13 mb-4">
                                Aquí se muestran todos tus préstamos, tanto activos como históricos.
                            </p>

                            <?php if (empty($prestamos)): ?>
                                <div class="alert alert-info">
                                    No tienes préstamos registrados.
                                </div>
                            <?php else: ?>
                                <table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th>Publicación</th>
                                            <th>Editorial</th>
                                            <th>Tipo</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Devolución</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($prestamos as $prestamo): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($prestamo->titulo); ?></td>
                                            <td><?= htmlspecialchars($prestamo->nombreEditorial); ?></td>
                                            <td><?= htmlspecialchars($prestamo->nombreTipo); ?></td>
                                            <td>
                                                <?= date('d/m/Y', strtotime($prestamo->fechaPrestamo)); ?>
                                                <br>
                                                <small class="text-muted">
                                                    <?= date('H:i', strtotime($prestamo->horaInicio)); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if ($prestamo->horaDevolucion): ?>
                                                    <?= date('d/m/Y H:i', strtotime($prestamo->horaDevolucion)); ?>
                                                <?php else: ?>
                                                    <span class="text-warning">En préstamo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $badge_class = 'badge-secondary';
                                                $estado_texto = 'Desconocido';
                                                
                                                switch($prestamo->estadoPrestamo) {
                                                    case ESTADO_PRESTAMO_ACTIVO:
                                                        $badge_class = 'badge-warning';
                                                        $estado_texto = 'En Préstamo';
                                                        break;
                                                    case ESTADO_PRESTAMO_FINALIZADO:
                                                        $badge_class = 'badge-success';
                                                        $estado_texto = 'Devuelto';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $badge_class ?>">
                                                    <?= $estado_texto ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= site_url('prestamos/detalle/' . $prestamo->idPrestamo); ?>" 
                                                   class="btn btn-info btn-sm" 
                                                   title="Ver detalles">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <?php if ($prestamo->estadoPrestamo == ESTADO_PRESTAMO_ACTIVO): ?>
                                                    <a href="<?= site_url('publicaciones/ver/' . $prestamo->idPublicacion); ?>" 
                                                       class="btn btn-primary btn-sm"
                                                       title="Ver publicación">
                                                        <i class="mdi mdi-book-open-variant"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>