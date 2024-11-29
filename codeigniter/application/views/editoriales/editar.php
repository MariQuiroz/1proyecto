<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="content-wrapper">
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Editar Editorial</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo base_url('editoriales'); ?>">Editoriales</a></li>
                                    <li class="breadcrumb-item active">Editar Editorial</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Formulario de Edición</h3>
                                    </div>
                                    <?php echo form_open('editoriales/editarbd'); ?>
                                        <div class="card-body">
                                            <?php if($this->session->flashdata('error')): ?>
                                                <div class="alert alert-danger alert-dismissible fade show">
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                                    <?php echo $this->session->flashdata('error'); ?>
                                                </div>
                                            <?php endif; ?>

                                            <input type="hidden" name="idEditorial" value="<?php echo $editorial->idEditorial; ?>">
                                            
                                            <div class="form-group">
                                                <label for="nombreEditorial">Nombre de la Editorial</label>
                                                <input type="text" 
                                                    class="form-control" 
                                                    id="nombreEditorial" 
                                                    name="nombreEditorial" 
                                                    value="<?php echo set_value('nombreEditorial', $editorial->nombreEditorial); ?>"
                                                    pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\-\.&,]+$"
                                                    title="Ingrese un nombre válido. Se permiten letras, números, espacios y caracteres especiales (- . & ,)"
                                                    required>
                                                <?php echo form_error('nombreEditorial', '<small class="text-danger">', '</small>'); ?>
                                            </div>
                                                                                    </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary">Actualizar</button>
                                            <button type="button" class="btn btn-secondary" id="btnCancelar" data-toggle="modal" data-target="#confirmCancelModal">Cancelar</button>
                                        </div>

                                    <?php echo form_close(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div> <!-- container -->
    </div> <!-- content -->
</div>

<!-- Modal de Confirmación de Cancelación -->
<div class="modal fade" id="confirmCancelModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmCancelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmCancelLabel">Confirmar Cancelación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas cancelar? Los cambios no guardados se perderán.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <a href="<?php echo site_url('editoriales'); ?>" class="btn btn-danger">Confirmar</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnCancelar = document.getElementById('btnCancelar');
    const modal = new bootstrap.Modal(document.getElementById('confirmCancelModal'), {
        backdrop: 'static',
        keyboard: false
    });

    btnCancelar.addEventListener('click', function(e) {
        e.preventDefault();
        modal.show();
    });
});
</script>