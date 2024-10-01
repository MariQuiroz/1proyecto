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

                            <?php echo form_open('editoriales/agregarbd'); ?>
                                <div class="mb-3">
                                    <label for="nombreEditorial" class="form-label">Nombre de la Editorial</label>
                                    <input type="text" class="form-control" id="nombreEditorial" name="nombreEditorial" value="<?php echo set_value('nombreEditorial'); ?>" required>
                                    <?php echo form_error('nombreEditorial', '<small class="text-danger">', '</small>'); ?>
                                </div>
                                <button type="submit" class="btn btn-primary">Agregar Editorial</button>
                                <a href="<?php echo site_url('editoriales'); ?>" class="btn btn-secondary">Cancelar</a>
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