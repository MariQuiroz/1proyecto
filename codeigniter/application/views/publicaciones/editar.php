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
                            <h4 class="header-title">Editar Publicación</h4>
                            <p class="text-muted font-13 mb-4">
                                Modifique los campos necesarios para actualizar la información de la publicación.
                            </p>

                            <?php echo form_open_multipart('publicaciones/modificarbd'); ?>
                                <input type="hidden" name="idPublicacion" value="<?php echo $publicacion->idPublicacion; ?>">
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo $publicacion->titulo; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="idEditorial" class="form-label">Editorial</label>
                                    <select class="form-control" id="idEditorial" name="idEditorial" required>
                                        <option value="">Seleccione una editorial</option>
                                        <?php foreach ($editoriales as $editorial): ?>
                                            <option value="<?php echo $editorial->idEditorial; ?>" <?php echo ($publicacion->idEditorial == $editorial->idEditorial) ? 'selected' : ''; ?>><?php echo $editorial->nombreEditorial; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="idTipo" class="form-label">Tipo</label>
                                    <select class="form-control" id="idTipo" name="idTipo" required>
                                        <option value="">Seleccione un tipo</option>
                                        <?php foreach ($tipos as $tipo): ?>
                                            <option value="<?php echo $tipo->idTipo; ?>" <?php echo ($publicacion->idTipo == $tipo->idTipo) ? 'selected' : ''; ?>><?php echo $tipo->nombreTipo; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="fechaPublicacion" class="form-label">Fecha de Publicación</label>
                                    <input type="date" class="form-control" id="fechaPublicacion" name="fechaPublicacion" value="<?php echo $publicacion->fechaPublicacion; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="numeroPaginas" class="form-label">Número de Páginas</label>
                                    <input type="number" class="form-control" id="numeroPaginas" name="numeroPaginas" value="<?php echo $publicacion->numeroPaginas; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo $publicacion->descripcion; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="ubicacionFisica" class="form-label">Ubicación Física</label>
                                    <select class="form-control" id="ubicacionFisica" name="ubicacionFisica" required>
                                        <option value="">Seleccione una ubicación</option>
                                        <?php foreach ($ubicaciones as $key => $value): ?>
                                            <option value="<?php echo $key; ?>" <?php echo ($publicacion->ubicacionFisica == $key) ? 'selected' : ''; ?>><?php echo $value; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="portada" class="form-label">Portada</label>
                                    <?php if (!empty($publicacion->portada)): ?>
                                        <img src="<?php echo base_url('uploads/portadas/' . $publicacion->portada); ?>" alt="Portada actual" class="img-thumbnail mb-2" style="max-width: 200px;">
                                        <p>Portada actual: <?php echo $publicacion->portada; ?></p>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="portada" name="portada" accept="image/jpeg,image/png,image/gif">
                                    <small class="form-text text-muted">Formatos permitidos: JPG, JPEG, PNG, GIF. Tamaño máximo: 2MB. Deje este campo vacío si no desea cambiar la portada.</small>
                                </div>
                                <button type="submit" class="btn btn-primary">Actualizar Publicación</button>
                                <a href="<?php echo site_url('publicaciones'); ?>" class="btn btn-secondary">Cancelar</a>
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