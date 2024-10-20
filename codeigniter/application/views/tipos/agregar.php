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
                            <h4 class="header-title">Agregar Nuevo Tipo</h4>
                            <p class="text-muted font-13 mb-4">
                                Complete el formulario para agregar un nuevo tipo de publicación.
                            </p>

                            <?php if($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('error'); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <?php echo form_open('tipos/agregarbd'); ?>
                                <div class="mb-3">
                                    <label for="nombreTipo" class="form-label">Nombre del Tipo</label>
                                    <input type="text" class="form-control" id="nombreTipo" name="nombreTipo" value="<?php echo set_value('nombreTipo'); ?>" required>
                                    <?php echo form_error('nombreTipo', '<small class="text-danger">', '</small>'); ?>
                                </div>
                                <button type="submit" class="btn btn-primary">Agregar Tipo</button>
                                <a href="<?php echo site_url('tipos'); ?>" class="btn btn-secondary">Cancelar</a>
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
