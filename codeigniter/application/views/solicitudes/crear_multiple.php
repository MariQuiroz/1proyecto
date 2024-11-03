<!-- application/views/solicitudes/crear_multiple.php -->
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Solicitud Múltiple de Publicaciones</h3>
                        </div>
                        <div class="card-body">
                            <?php if($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger">
                                    <?php echo $this->session->flashdata('error'); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php echo form_open('solicitudes/crear_solicitud_multiple'); ?>
                                <div class="form-group">
                                    <label>Seleccione las publicaciones que desea solicitar:</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Seleccionar</th>
                                                    <th>Título</th>
                                                    <th>Editorial</th>
                                                    <th>Tipo</th>
                                                    <th>Ubicación</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($publicaciones_disponibles as $publicacion): ?>
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" name="publicaciones[]" 
                                                                   value="<?php echo $publicacion->idPublicacion; ?>" 
                                                                   class="publicacion-checkbox">
                                                        </td>
                                                        <td><?php echo $publicacion->titulo; ?></td>
                                                        <td><?php echo $publicacion->nombreEditorial; ?></td>
                                                        <td><?php echo $publicacion->nombreTipo; ?></td>
                                                        <td><?php echo $publicacion->ubicacionFisica; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" id="btn-solicitar">
                                        Realizar Solicitud
                                    </button>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Limitar el número máximo de publicaciones que se pueden seleccionar
    const MAX_PUBLICACIONES = 3;
    
    $('.publicacion-checkbox').on('change', function() {
        let checkedBoxes = $('.publicacion-checkbox:checked').length;
        
        if(checkedBoxes > MAX_PUBLICACIONES) {
            $(this).prop('checked', false);
            alert('Solo puede solicitar hasta ' + MAX_PUBLICACIONES + ' publicaciones a la vez');
        }
        
        // Habilitar/deshabilitar el botón según si hay selecciones
        $('#btn-solicitar').prop('disabled', checkedBoxes === 0);
    });
    
    // Inicialmente deshabilitar el botón
    $('#btn-solicitar').prop('disabled', true);
});
</script>