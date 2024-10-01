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
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                        <?php echo $this->session->flashdata('error'); ?>
                                    </div>
                                <?php endif; ?>

                                <input type="hidden" name="idEditorial" value="<?php echo $editorial->idEditorial; ?>">
                                
                                <div class="form-group">
                                    <label for="nombreEditorial">Nombre de la Editorial</label>
                                    <input type="text" class="form-control" id="nombreEditorial" name="nombreEditorial" value="<?php echo set_value('nombreEditorial', $editorial->nombreEditorial); ?>" required>
                                    <?php echo form_error('nombreEditorial', '<small class="text-danger">', '</small>'); ?>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                                <a href="<?php echo site_url('editoriales'); ?>" class="btn btn-secondary" id="btnCancelar">Cancelar</a>
                            </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->
            </div>   
                </div> <!-- container -->
             </div> <!-- content -->
<script>
document.getElementById('btnCancelar').addEventListener('click', function(e) {
    var form = document.querySelector('form');
    var formChanged = false;

    // Verificar si algún campo del formulario ha sido modificado
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