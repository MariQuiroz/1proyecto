<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Lista de usuarios deshabilitados</h4>
                    
                    <?php if($this->session->flashdata('mensaje')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $this->session->flashdata('mensaje'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                    <?php echo form_open('usuarios/mostrar', ['class' => 'mb-3']); ?>
                        <button type="submit" name="buton2" class="btn btn-primary">Ver usuarios habilitados</button>
                    <?php echo form_close(); ?>

                    <table class="table table-striped table-centered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombres</th>
                                <th>Apellido Paterno</th>
                                <th>Apellido Materno</th>
                                <th>Carnet</th>
                                <th>Tipo Usuario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($usuarios) && is_array($usuarios) && count($usuarios) > 0): ?>
                                <?php foreach ($usuarios as $indice => $row): ?>
                                    <tr>
                                        <th scope="row"><?php echo $indice + 1; ?></th>
                                        <td><?php echo $row->nombres; ?></td>
                                        <td><?php echo $row->apellidoPaterno; ?></td>
                                        <td><?php echo $row->apellidoMaterno; ?></td>
                                        <td><?php echo $row->carnet; ?></td>
                                        <td><?php echo $row->rol; ?></td>
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
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
</div>