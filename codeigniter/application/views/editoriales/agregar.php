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
                            <h4 class="header-title">Agregar Nueva Editorial</h4>
                            <p class="text-muted font-13 mb-4">
                                Complete el formulario para agregar una nueva editorial.
                            </p>

                            <?php if($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('error'); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <?php if($this->session->flashdata('mensaje')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('mensaje'); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <?php echo form_open('editoriales/agregarbd'); ?>
                                <div class="mb-3">
                                    <label for="nombreEditorial" class="form-label">Nombre de la Editorial</label>
                                    <input type="text" class="form-control" id="nombreEditorial" name="nombreEditorial" value="<?php echo set_value('nombreEditorial'); ?>" required>
                                    <?php echo form_error('nombreEditorial', '<small class="text-danger">', '</small>'); ?>
                                </div>
                                <button type="submit" class="btn btn-primary">Agregar Editorial</button>
                                <button type="button" class="btn btn-secondary" id="btnCancelar" data-toggle="modal" data-target="#confirmCancelModal">Cancelar</button>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- container -->
    </div> <!-- content -->
</div>

<!-- Modal de Confirmación de Cancelación -->
<div class="modal fade" id="confirmCancelModal" tabindex="-1" aria-labelledby="confirmCancelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmCancelLabel">Confirmar Cancelación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas cancelar? Los cambios no guardados se perderán.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="<?php echo site_url('editoriales'); ?>" class="btn btn-danger">Cancelar</a>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->
