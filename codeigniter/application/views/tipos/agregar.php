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

                <!-- Formulario para agregar un nuevo tipo -->
                <?php echo form_open('tipos/agregar'); ?>
                
                    <div class="mb-3">
                        <label for="nombreTipo" class="form-label">Nombre del Tipo</label>
                        <input type="text" class="form-control" id="nombreTipo" name="nombreTipo" required>
                    </div>
                    
                    <!-- Botón para enviar el formulario -->
                    <button type="submit" class="btn btn-primary">Agregar Tipo</button>
                
                <!-- Cierre del formulario -->
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