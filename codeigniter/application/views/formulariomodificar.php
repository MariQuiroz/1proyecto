<div class="container">
  <div class="row">
    <div class="col-md-12">

      <h2>Modificar usuario</h2>

      <?php
      foreach ($infousuario->result() as $row)
      {
        echo form_open_multipart('usuario/modificarbd');
        ?>
        <input type="hidden" name="id" value="<?php echo $row->id; ?>">

        <input type="text" name="nombre" placeholder="Ingrese su nombre" value="<?php echo $row->nombre; ?>">
        <input type="text" name="primerapellido" placeholder="Ingrese su primer apellido" value="<?php echo $row->primerApellido; ?>">
        <input type="text" name="segundoapellido" placeholder="Ingrese su segundo apellido" value="<?php echo $row->segundoApellido; ?>">
        <input type="text" name="ci" placeholder="Ingrese su ci" value="<?php echo $row->ci; ?>">
        <input type="text" name="domicilio" placeholder="Ingrese su domicilio" value="<?php echo $row->domicilio; ?>">
        <input type="number" name="telefono" placeholder="Ingrese su telefono" value="<?php echo $row->telefono; ?>">
        <input type="email" name="email" placeholder="Ingrese su email" value="<?php echo $row->email; ?>">
        <input type="text" name="login" placeholder="Ingrese su login" value="<?php echo $row->login; ?>">
        <input type="password" name="password" placeholder="Ingrese su password" value="<?php echo $row->password; ?>">
        <input type="number" name="rol" placeholder="Ingrese su rol" value="<?php echo $row->rol; ?>">
        <input type="text" name="foto" placeholder="Ingrese su foto" value="<?php echo $row->foto; ?>">
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