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
                            <h4 class="header-title">Lista de Usuarios Deshabilitados</h4>
                            <p class="text-muted font-13 mb-4">
                                Aquí puedes ver la lista de usuarios que han sido deshabilitados en el sistema.
                            </p>

                            <?php if ($this->session->flashdata('mensaje')): ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo $this->session->flashdata('mensaje'); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $this->session->flashdata('error'); ?>
                                </div>
                            <?php endif; ?>

                            <?php echo form_open('usuarios/mostrar', ['class' => 'mb-3']); ?>
                                <button type="submit" name="buton2" class="btn btn-primary">Ver Usuarios Habilitados</button>
                            <?php echo form_close(); ?>

                            <div class="table-responsive">
                                <table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Nombres</th>
                                            <th scope="col">Apellido Paterno</th>
                                            <th scope="col">Apellido Materno</th>
                                            <th scope="col">Carnet</th>
                                            <th scope="col">Tipo Usuario</th>
                                            <th scope="col">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($usuarios) && is_array($usuarios) && count($usuarios) > 0): ?>
                                            <?php foreach ($usuarios as $indice => $row): ?>
                                                <tr>
                                                    <th scope="row"><?php echo $indice + 1; ?></th>
                                                    <td><?php echo htmlspecialchars($row->nombres); ?></td>
                                                    <td><?php echo htmlspecialchars($row->apellidoPaterno); ?></td>
                                                    <td><?php echo htmlspecialchars($row->apellidoMaterno); ?></td>
                                                    <td><?php echo htmlspecialchars($row->carnet); ?></td>
                                                    <td><?php echo htmlspecialchars($row->rol); ?></td>
                                                    <td>
                                                        <?php echo form_open('usuarios/habilitarbd', ['class' => 'd-inline']); ?>
                                                            <input type="hidden" name="idUsuario" value="<?php echo $row->idUsuario; ?>">
                                                            <button type="submit" name="buttonz" class="btn btn-success btn-sm" onclick="return confirm('¿Está seguro de querer habilitar este usuario?');">Habilitar</button>
                                                        <?php echo form_close(); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No hay usuarios deshabilitados</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div><!-- end col-->
            </div>
            <!-- end row-->
        </div> <!-- container -->
    </div> <!-- content -->
</div> <!-- content-page -->
<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->
