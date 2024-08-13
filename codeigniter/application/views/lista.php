
<div class="container">
  <div class="row">
    <div class="col-md-12">

      <?php echo form_open_multipart('usuario/logout'); ?>
        <button type="submit" name="buton2" class="btn btn-danger">CERRAR SESIÓN</button>
      <?php echo form_close(); ?>


      <?php
      //$atributos=array('class'=>'classemail','target'=>'_blank');
      //echo form_open_multipart('usuario/listapdf',$atributos);
      echo form_open_multipart('usuario/listapdf'); ?>
        <button type="submit" name="buton2" class="btn btn-success">Lista usuarios PDF</button>
      <?php echo form_close(); ?>

      <h1>Lista de usuarios habilitados</h1>
      <h1>Login: <?php echo $this->session->userdata('login'); ?></h1>
      <h1>Rol: <?php echo $this->session->userdata('rol'); ?></h1>
      <h1>ID: <?php echo $this->session->userdata('id'); ?></h1>


      <?php echo form_open_multipart('usuario/deshabilitados'); ?>
        <button type="submit" name="buton2" class="btn btn-warning">VER. USUARIOS DESHABILITADOS</button>
      <?php echo form_close(); ?>


      <br>


      <?php echo form_open_multipart('usuario/agregar'); ?>
        <button type="submit" name="buton1" class="btn btn-primary">AGREGAR USUARIO</button>
      <?php echo form_close(); ?>

      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Foto</th>
            <th scope="col">Nombre</th>
            <th scope="col">Primer Apellido</th>
            <th scope="col">Segundo Apellido</th>
            <th scope="col">Modificar</th>
            <th scope="col">Eliminar</th>
            <th scope="col">Deshabilitar</th>
          </tr>
        </thead>
        <tbody>

      <?php
      $indice=1;
      foreach ($usuarios->result() as $row)
      {
        ?>
          <tr>
            <th scope="row"><?php echo $indice; ?></th>
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
            <td><?php echo $row->nombre; ?></td>
            <td><?php echo $row->primerApellido; ?></td>
            <td><?php echo $row->segundoApellido; ?></td>
            

            <td>

              
              <?php echo form_open_multipart("usuario/modificar"); ?>
              <input type="hidden" name="id" value="<?php echo $row->id; ?>">
              <input type="submit" name="buttony" value="Modificar" class="btn btn-success">
              <?php echo form_close(); ?>
              
            </td>

            <td>

              
              <?php echo form_open_multipart("usuario/eliminarbd"); ?>
              <input type="hidden" name="id" value="<?php echo $row->id; ?>">
              <input type="submit" name="buttonx" value="Eliminar" class="btn btn-danger">
              <?php echo form_close(); ?>
              
            </td>

            <td>

              
              <?php echo form_open_multipart("usuario/deshabilitarbd"); ?>
              <input type="hidden" name="id" value="<?php echo $row->id; ?>">
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

    </div>
  </div>
</div>
