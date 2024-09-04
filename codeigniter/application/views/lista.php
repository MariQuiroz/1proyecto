
                        <h4 class="header-title">Lista de usuarios habilitados</h4>
                <p class="text-muted font-13 mb-4">
                    Username: <?php echo $this->session->userdata('username'); ?><br>
                    Rol: <?php echo $this->session->userdata('rol'); ?><br>
                    ID: <?php echo $this->session->userdata('idUsuario'); ?>
                </p>

                <div class="mb-2">
                    <?php echo form_open_multipart('usuarios/logout'); ?>
                        <button type="submit" name="buton2" class="btn btn-danger">CERRAR SESIÓN</button>
                    <?php echo form_close(); ?>
<br>
                    <?php echo form_open_multipart('usuarios/registrar'); ?>
                        <button type="submit" name="buton2" class="btn btn-success">REGISTRAR USUARIO</button>
                    <?php echo form_close(); ?>
<br>
                    <?php echo form_open_multipart('usuarios/listapdf'); ?>
                        <button type="submit" name="buton2" class="btn btn-success">Lista usuarios PDF</button>
                    <?php echo form_close(); ?>
<br>
                    <?php echo form_open_multipart('usuarios/deshabilitados'); ?>
                        <button type="submit" name="buton2" class="btn btn-warning">VER USUARIOS DESHABILITADOS</button>
                    <?php echo form_close(); ?>
<br>
                    <?php echo form_open_multipart('usuarios/agregar'); ?>
                        <button type="submit" name="buton1" class="btn btn-primary">AGREGAR USUARIO</button>
                    <?php echo form_close(); ?>       

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
               
                </div>

                <table id="alternative-page-datatable" class="table dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>Nombres</th>
                            <th>Apellido Paterno</th>
                            <th>Apellido Materno</th>
                            <th>Carnet</th>
                            <th>Rol</th>
                            <th>Modificar</th>
                            <th>Eliminar</th>
                            <th>Deshabilitar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $indice=1;
                        foreach ($usuarios->result() as $row)
                        {
                        ?>
                            <tr>
                                <td><?php echo $indice; ?></td>
                                <td>
                                    <?php
                                    $foto=$row->foto;
                                    if($foto=="")
                                    {
                                    ?>
                                        <img src="<?php echo base_url(); ?>/uploads/user.jpg" width="50px">
                                    <?php
                                    }
                                    else
                                    {
                                    ?>
                                        <img src="<?php echo base_url(); ?>/uploads/<?php echo $foto; ?>" width="50px">
                                    <?php
                                    }
                                    ?>
                                </td>
                                <td><?php echo $row->nombres; ?></td>
                                <td><?php echo $row->apellidoPaterno; ?></td>
                                <td><?php echo $row->apellidoMaterno; ?></td>
                                <td><?php echo $row->carnet; ?></td>
                                <td><?php echo $row->rol; ?></td>
                                <td>
                                    <?php echo form_open_multipart("usuarios/modificar"); ?>
                                    <input type="hidden" name="idUsuario" value="<?php echo $row->idUsuario; ?>">
                                    <input type="submit" name="buttony" value="Modificar" class="btn btn-success">
                                    <?php echo form_close(); ?>
                                </td>
                                <td>
                                    <?php echo form_open_multipart("usuarios/eliminarbd"); ?>
                                    <input type="hidden" name="idUsuario" value="<?php echo $row->idUsuario; ?>">
                                    <input type="submit" name="buttonx" value="Eliminar" class="btn btn-danger">
                                    <?php echo form_close(); ?>
                                </td>
                                <td>
                                    <?php echo form_open_multipart("usuarios/deshabilitarbd"); ?>
                                    <input type="hidden" name="idUsuario" value="<?php echo $row->idUsuario; ?>">
                                    <input type="submit" name="buttonz" value="Deshabilitar" class="btn btn-warning">
                                    <?php echo form_close(); ?>
                                </td>
                            </tr>
                        <?php
                        $indice++;
                        }
                        ?>
                    </tbody>
                </table>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->

</div> <!-- container -->

</div> <!-- content -->
