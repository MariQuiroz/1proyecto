<!-- application/views/solicitudes/confirmar_multiple.php -->
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Confirmar Solicitud de Préstamo Múltiple</h4>
                            
                            <?php if ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                            <?php endif; ?>

                            <div class="table-responsive mb-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th>Editorial</th>
                                            <th>Tipo</th>
                                            <th>Ubicación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($publicaciones_seleccionadas as $pub): ?>
                                            <tr>
                                                <td><?php echo $pub->titulo; ?></td>
                                                <td><?php echo $pub->nombreEditorial; ?></td>
                                                <td><?php echo $pub->nombreTipo; ?></td>
                                                <td><?php echo $pub->ubicacionFisica; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php echo form_open('solicitudes/procesar_solicitud_multiple'); ?>
                                <?php foreach ($publicaciones_seleccionadas as $pub): ?>
                                    <input type="hidden" name="publicaciones[]" value="<?php echo $pub->idPublicacion; ?>">
                                <?php endforeach; ?>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="observaciones" class="form-label">Observaciones</label>
                                        <textarea name="observaciones" id="observaciones" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <a href="<?php echo site_url('publicaciones'); ?>" class="btn btn-secondary me-2">
                                        <i class="mdi mdi-arrow-left"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-check"></i> Confirmar Solicitud
                                    </button>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>