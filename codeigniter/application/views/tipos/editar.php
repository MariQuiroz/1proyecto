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

                <?php echo form_open('tipos/editar/'.$tipo->idTipo); ?>
                    <div class="mb-3">
                        <label for="nombreTipo" class="form-label">Nombre del Tipo</label>
                        <input type="text" class="form-control" id="nombreTipo" name="nombreTipo" value="<?php echo $tipo->nombreTipo; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar Tipo</button>
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