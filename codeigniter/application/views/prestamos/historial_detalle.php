<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Detalle del Préstamo</h4>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h5>Publicación</h5>
                                    <p><?php echo htmlspecialchars($prestamo->titulo); ?></p>
                                </div>
                                
                                <div class="mb-3">
                                    <h5>Fecha de Préstamo</h5>
                                    <p><?php echo date('d/m/Y', strtotime($prestamo->fechaPrestamo)); ?></p>
                                </div>
                                
                                <div class="mb-3">
                                    <h5>Hora de Inicio</h5>
                                    <p><?php echo $prestamo->horaInicio; ?></p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h5>Estado del Préstamo</h5>
                                    <p>
                                        <?php if ($prestamo->estadoPrestamo == ESTADO_PRESTAMO_FINALIZADO): ?>
                                            <span class="badge badge-success">Finalizado</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Activo</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                
                                <?php if ($prestamo->horaDevolucion): ?>
                                <div class="mb-3">
                                    <h5>Hora de Devolución</h5>
                                    <p><?php echo $prestamo->horaDevolucion; ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>