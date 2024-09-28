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
                <h4 class="header-title">Editar Editorial</h4>
                <p class="text-muted font-13 mb-4">
                    Modifique el nombre de la editorial.
                </p>

                <?php echo form_open('editoriales/editar/'.$editorial->idEditorial); ?>
                    <div class="mb-3">
                        <label for="nombreEditorial" class="form-label">Nombre de la Editorial</label>
                        <input type="text" class="form-control" id="nombreEditorial" name="nombreEditorial" value="<?php echo $editorial->nombreEditorial; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar Editorial</button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->
            </div>   
                </div> <!-- container -->
             </div> <!-- content -->