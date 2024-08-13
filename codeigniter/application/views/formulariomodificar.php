<div class="container">
  <div class="row">
    <div class="col-md-12">

      <h2>Modificar usuario</h2>

      <?php
      foreach ($infousuario->result() as $row)
      {
        echo form_open_multipart('usuario/modificarbd');
        ?>
        <input type="hidden" name="idusuario" value="<?php echo $row->idUsuario; ?>">

        <input type="text" name="nombre" placeholder="Ingrese su nombre" value="<?php echo $row->nombre; ?>">
        <input type="text" name="primerapellido" placeholder="Ingrese su primer apellido" value="<?php echo $row->primerApellido; ?>">
        <input type="text" name="segundoapellido" placeholder="Ingrese su segundo apellido" value="<?php echo $row->segundoApellido; ?>">
        <br>
        <input type="file" name="userfile">
        <br>
        <button type="submit" class="btn btn-success">MODIFICAR USUARIO</button>
        <?php
        form_close();
      }
      ?>
    </div>
  </div>
</div>





      
      
    </div>
  </div>
</div>