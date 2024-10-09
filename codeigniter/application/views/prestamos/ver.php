<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Detalles del Préstamo
            <small>Información completa del préstamo</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Préstamo #<?php echo $prestamo->idPrestamo; ?></h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt>Usuario:</dt>
                            <dd><?php echo $prestamo->nombres . ' ' . $prestamo->apellidoPaterno; ?></dd>

                            <dt>Publicación:</dt>
                            <dd><?php echo $prestamo->titulo; ?></dd>

                            <dt>Fecha de Préstamo:</dt>
                            <dd><?php echo $prestamo->fechaPrestamo; ?></dd>

                            <dt>Hora de Inicio:</dt>
                            <dd><?php echo $prestamo->horaInicio; ?></dd>

                            <dt>Estado:</dt>
                            <dd><?php echo $prestamo->estadoPrestamo == ESTADO_PRESTAMO_ACTIVO ? 'Activo' : 'Finalizado'; ?></dd>

                            <?php if ($prestamo->estadoPrestamo == ESTADO_PRESTAMO_FINALIZADO): ?>
                            <dt>Fecha de Devolución:</dt>
                            <dd><?php echo $prestamo->horaDevolucion; ?></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                    <div class="box-footer">
                        <?php if ($prestamo->estadoPrestamo == ESTADO_PRESTAMO_ACTIVO): ?>
                        <a href="<?php echo site_url('prestamos/finalizar/'.$prestamo->idPrestamo); ?>" class="btn btn-warning" onclick="return confirm('¿Está seguro de que desea finalizar este préstamo?');">Finalizar Préstamo</a>
                        <?php endif; ?>
                        <a href="<?php echo site_url('prestamos/activos'); ?>" class="btn btn-default">Volver a la lista</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>