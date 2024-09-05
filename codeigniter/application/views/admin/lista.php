<?php echo form_open_multipart('usuarios/registrar'); ?>
        <button type="submit" name="buton2" class="btn btn-success">REGISTRAR USUARIO</button>
      <?php echo form_close(); ?>

      <?php echo form_open_multipart('usuarios/listapdf'); ?>
        <button type="submit" name="buton2" class="btn btn-success">Lista usuarios PDF</button>
      <?php echo form_close(); ?>

      <h1>Lista de usuarios habilitados</h1>
      <h1>Username: <?php echo $this->session->userdata('username'); ?></h1>
      <h1>Rol: <?php echo $this->session->userdata('rol'); ?></h1>
      <h1>ID: <?php echo $this->session->userdata('idUsuario'); ?></h1>

      <?php echo form_open_multipart('usuarios/deshabilitados'); ?>
        <button type="submit" name="buton2" class="btn btn-warning">VER USUARIOS DESHABILITADOS</button>
      <?php echo form_close(); ?>

      <br>

      <?php echo form_open_multipart('usuarios/agregar'); ?>
        <button type="submit" name="buton1" class="btn btn-primary">AGREGAR USUARIO</button>
      <?php echo form_close(); ?>



    

      <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
        
  
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
                        if($foto=="") {
                            ?>
                            <img src="<?php echo base_url(); ?>/uploads/user.jpg" width="50px">
                            <?php
                        } else {
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
