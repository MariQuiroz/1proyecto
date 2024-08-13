<div class="container">
  <div class="row">
    <div class="col-md-12">

      <h1>Lista de usuarios deshabilitados</h1>

      <?php echo form_open_multipart('usuario/index'); ?>
        <button type="submit" name="buton2" class="btn btn-primary">VER USUARIOS HABILITADOS</button>
      <?php echo form_close(); ?>


      <br>


      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Nombre</th>
            <th scope="col">Primer Apellido</th>
            <th scope="col">Segundo Apellido</th>
            <th scope="col">Habilitar</th>
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
            <td><?php echo $row->nombre; ?></td>
            <td><?php echo $row->primerApellido; ?></td>
            <td><?php echo $row->segundoApellido; ?></td>

            <td>

              
              <?php echo form_open_multipart("usuario/habilitarbd"); ?>
              <input type="hidden" name="id" value="<?php echo $row->id; ?>">
              <input type="submit" name="buttonz" value="Habilitar" class="btn btn-warning">
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
