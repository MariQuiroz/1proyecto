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
                <h4 class="header-title">Agregar Nueva Publicación</h4>
                <p class="text-muted font-13 mb-4">
                    Complete el formulario para agregar una nueva publicación a la hemeroteca.
                </p>

                <?php echo form_open('publicaciones/agregarbd'); ?>
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label for="idEditorial" class="form-label">Editorial</label>
                        <select class="form-control" id="idEditorial" name="idEditorial" required>
                            <option value="">Seleccione una editorial</option>
                            <?php foreach ($editoriales as $editorial): ?>
                                <option value="<?php echo $editorial->idEditorial; ?>"><?php echo $editorial->nombreEditorial; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="idTipo" class="form-label">Tipo</label>
                        <select class="form-control" id="idTipo" name="idTipo" required>
                            <option value="">Seleccione un tipo</option>
                            <?php foreach ($tipos as $tipo): ?>
                                <option value="<?php echo $tipo->idTipo; ?>"><?php echo $tipo->nombreTipo; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fechaPublicacion" class="form-label">Fecha de Publicación</label>
                        <input type="date" class="form-control" id="fechaPublicacion" name="fechaPublicacion" required>
                    </div>
                    <div class="mb-3">
                        <label for="numeroPaginas" class="form-label">Número de Páginas</label>
                        <input type="number" class="form-control" id="numeroPaginas" name="numeroPaginas">
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="ubicacionFisica" class="form-label">Ubicación Física</label>
                        <input type="text" class="form-control" id="ubicacionFisica" name="ubicacionFisica">
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar Publicación</button>
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
