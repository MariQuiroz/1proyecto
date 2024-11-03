<!-- application/views/prestamos/devolucion_multiple.php -->
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Devolución de Publicaciones</h4>

                            <?php echo form_open('prestamos/procesar_devolucion_multiple'); ?>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Seleccionar</th>
                                            <th>Título</th>
                                            <th>Fecha Préstamo</th>
                                            <th>Estado de Devolución</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($prestamos as $prestamo): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="prestamos[]" 
                                                           value="<?php echo $prestamo->idPrestamo; ?>" 
                                                           class="form-check-input prestamo-checkbox">
                                                </td>
                                                <td><?php echo $prestamo->titulo; ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($prestamo->fechaPrestamo)); ?></td>
                                                <td>
                                                    <select name="estado_devolucion[<?php echo $prestamo->idPrestamo; ?>]" 
                                                            class="form-select estado-select" disabled>
                                                        <option value="">Seleccione estado</option>
                                                        <option value="bueno">Bueno</option>
                                                        <option value="dañado">Dañado</option>
                                                        <option value="perdido">Perdido</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea name="observaciones[<?php echo $prestamo->idPrestamo; ?>]" 
                                                              class="form-control observacion-textarea" 
                                                              rows="2" disabled></textarea>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" id="btn-procesar-devolucion" disabled>
                                    <i class="mdi mdi-check"></i> Procesar Devolución
                                </button>
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.prestamo-checkbox').on('change', function() {
        const $row = $(this).closest('tr');
        const isChecked = $(this).prop('checked');
        
        $row.find('.estado-select, .observacion-textarea').prop('disabled', !isChecked);
        if (!isChecked) {
            $row.find('.estado-select').val('');
            $row.find('.observacion-textarea').val('');
        }
        
        updateSubmitButton();
    });

    $('.estado-select').on('change', function() {
        updateSubmitButton();
    });

    function updateSubmitButton() {
        const $checkedBoxes = $('.prestamo-checkbox:checked');
        const $selectedStates = $('.estado-select').filter(function() {
            return $(this).closest('tr').find('.prestamo-checkbox').prop('checked');
        });
        
        const allStatesSelected = $selectedStates.toArray()
            .every(select => $(select).val() !== '');
        
        $('#btn-procesar-devolucion').prop('disabled', 
            $checkedBoxes.length === 0 || !allStatesSelected);
    }
});
</script>