<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Editar Tipo</h4>
                            <p class="text-muted font-13 mb-4">
                                Modifique el nombre del tipo de publicación.
                            </p>

                            <?php if($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('error'); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <?php echo form_open('tipos/editarbd'); ?>
                                <input type="hidden" name="idTipo" value="<?php echo $tipo->idTipo; ?>">
                                <div class="mb-3">
                                    <label for="nombreTipo" class="form-label">Nombre del Tipo</label>
                                    <input type="text" class="form-control" id="nombreTipo" name="nombreTipo" value="<?php echo set_value('nombreTipo', $tipo->nombreTipo); ?>" required>
                                    <?php echo form_error('nombreTipo', '<small class="text-danger">', '</small>'); ?>
                                </div>
                                <button type="submit" class="btn btn-primary">Actualizar Tipo</button>
                                <a href="<?php echo site_url('tipos'); ?>" class="btn btn-secondary" id="btnCancelar">Cancelar</a>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- container -->
    </div> <!-- content -->
</div>

<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->

<script>
document.getElementById('btnCancelar').addEventListener('click', function(e) {
    var form = document.querySelector('form');
    var formChanged = false;

    form.querySelectorAll('input, select, textarea').forEach(function(element) {
        if (element.type !== 'submit' && element.value !== element.defaultValue) {
            formChanged = true;
        }
    });

    if (formChanged) {
        if (!confirm('¿Estás seguro de que deseas cancelar? Los cambios no guardados se perderán.')) {
            e.preventDefault();
        }
    }
});
</script>