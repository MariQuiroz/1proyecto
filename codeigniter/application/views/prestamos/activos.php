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

                        <!-- Al principio de la vista, justo después de mostrar los mensajes flash -->
            <?php if ($this->input->get('pdf')): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Abrir el PDF solo una vez cuando se carga la página
                        var pdfUrl = '<?php echo urldecode($this->input->get('pdf')); ?>';
                        window.open(pdfUrl, '_blank');
                    });
                </script>
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
                                            <a href="<?php echo site_url('solicitudes/generar_ficha/' . $prestamo->idSolicitud); ?>" 
                                            class="btn btn-primary btn-sm">
                                                Generar Ficha
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

<!-- Modal simplificado -->
<div class="modal fade" id="modalDevolucion" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Devolución</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php 
            $attributes = array('id' => 'formDevolucion');
            echo form_open('prestamos/finalizar', $attributes); 
            ?>
                <!-- Campo oculto para el ID del préstamo -->
                <input type="hidden" name="idPrestamo" id="idPrestamoInput">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label>Estado de la Devolución</label>
                        <select class="form-control" id="estadoDevolucion" name="estadoDevolucion" required>
                            <option value="">Seleccione el estado de devolución</option>
                            <option value="<?php echo ESTADO_DEVOLUCION_BUENO; ?>">En buen estado</option>
                            <option value="<?php echo ESTADO_DEVOLUCION_DAÑADO; ?>">Con daños</option>
                            <option value="<?php echo ESTADO_DEVOLUCION_PERDIDO; ?>">Perdido</option>
                        </select>
                    </div>
              
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Devolución</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
function abrirModalDevolucion(idPrestamo) {
    // Asignar el ID del préstamo al campo oculto
    document.getElementById('idPrestamoInput').value = idPrestamo;
    $('#modalDevolucion').modal('show');
}
</script>
