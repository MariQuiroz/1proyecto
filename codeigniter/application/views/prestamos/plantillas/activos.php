<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Préstamos Activos</h4>
                    </div>
                </div>
            </div>
            
            <?php if ($this->session->flashdata('mensaje')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $this->session->flashdata('mensaje'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $this->session->flashdata('error'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Listado de Préstamos Activos</h4>
                            <p class="text-muted font-13 mb-4">
                                Aquí se muestran todos los préstamos actualmente en curso.
                            </p>
                            
                            <table id="prestamos-activos-table" class="table dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>ID Préstamo</th>
                                        <th>Usuario</th>
                                        <th>Publicación</th>
                                        <th>Fecha Inicio</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($prestamos as $prestamo): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($prestamo->idPrestamo); ?></td>
                                        <td><?php echo htmlspecialchars($prestamo->nombres . ' ' . $prestamo->apellidoPaterno); ?></td>
                                        <td><?php echo htmlspecialchars($prestamo->titulo); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($prestamo->fechaPrestamo)); ?></td>
                                        <td>
                                            <button onclick="abrirModalDevolucion(<?php echo $prestamo->idPrestamo; ?>)" 
                                                    class="btn btn-success btn-sm">
                                                Finalizar
                                            </button>
                                            <a href="<?php echo site_url('prestamos/detalle/' . $prestamo->idPrestamo); ?>" 
                                            class="btn btn-info btn-sm">
                                                Detalles
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-body">
    <div class="form-group">
        <label for="estadoDevolucion">Estado de la Devolución</label>
        <select class="form-control" name="estadoDevolucion" required>
            <option value="<?php echo ESTADO_DEVOLUCION_BUENO; ?>">Bueno</option>
            <option value="<?php echo ESTADO_DEVOLUCION_DAÑADO; ?>">Dañado</option>
            <option value="<?php echo ESTADO_DEVOLUCION_PERDIDO; ?>">Perdido</option>
        </select>
    </div>
</div>

<script>
function abrirModalDevolucion(idPrestamo) {
    // Actualizar la URL del formulario con el ID del préstamo
    var form = document.getElementById('formDevolucion');
    form.action = '<?php echo site_url("prestamos/finalizar/"); ?>' + idPrestamo;
    $('#modalDevolucion').modal('show');
}
</script>