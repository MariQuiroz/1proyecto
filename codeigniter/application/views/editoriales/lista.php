<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Lista de Editoriales</h4>
                <p class="text-muted font-13 mb-4">
                    Aquí se muestran todas las editoriales disponibles.
                </p>

                <a href="<?php echo site_url('editoriales/agregar'); ?>" class="btn btn-primary mb-3">Agregar Nueva Editorial</a>

                <table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>Nombre de la Editorial</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($editoriales as $editorial): ?>
                        <tr>
                            <td><?php echo $editorial->nombreEditorial; ?></td>
                            <td>
                                <a href="<?php echo site_url('editoriales/editar/'.$editorial->idEditorial); ?>" class="btn btn-primary btn-sm">Editar</a>
                                <a href="<?php echo site_url('editoriales/eliminar/'.$editorial->idEditorial); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta editorial?');">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>