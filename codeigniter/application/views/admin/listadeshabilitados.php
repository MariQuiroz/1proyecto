<div class="container">
  <div class="row">
    <div class="col-md-12">

      <h1>Lista de usuarios deshabilitados</h1>

      <?php echo form_open_multipart('usuarios/mostrar'); ?>
        <button type="submit" name="buton2" class="btn btn-primary">VER USUARIOS HABILITADOS</button>
      <?php echo form_close(); ?>

      <br>

      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Nombres</th>
            <th scope="col">Apellido Paterno</th>
            <th scope="col">Apellido Materno</th>
            <th scope="col">Carnet</th>
            <th scope="col">Rol</th>
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
              <td><?php echo $row->nombres; ?></td>
              <td><?php echo $row->apellidoPaterno; ?></td>
              <td><?php echo $row->apellidoMaterno; ?></td>
              <td><?php echo $row->carnet; ?></td>
              <td><?php echo $row->rol; ?></td>

              <td>
                <?php echo form_open_multipart("usuarios/habilitarbd"); ?>
                <input type="hidden" name="idUsuario" value="<?php echo $row->idUsuario; ?>">
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