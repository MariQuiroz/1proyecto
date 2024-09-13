<div class="container">
  <div class="row">
    <div class="col-md-12">

      <h2>Modificar Usuario</h2>

      <?php
      foreach ($infoUsuario->result() as $row)
      {
        echo form_open_multipart('usuarios/modificarbd');
      ?>
        <input type="hidden" name="idUsuario" value="<?php echo $row->idUsuario; ?>">

        <div class="form-group">
          <input type="text" name="nombres" class="form-control" placeholder="Ingrese sus nombres" value="<?php echo $row->nombres; ?>" required>
        </div>
        <div class="form-group">
          <input type="text" name="apellidoPaterno" class="form-control" placeholder="Ingrese su apellido paterno" value="<?php echo $row->apellidoPaterno; ?>" required>
        </div>
        <div class="form-group">
          <input type="text" name="apellidoMaterno" class="form-control" placeholder="Ingrese su apellido materno" value="<?php echo $row->apellidoMaterno; ?>">
        </div>
        <div class="form-group">
          <input type="text" name="carnet" class="form-control" placeholder="Ingrese su carnet" value="<?php echo $row->carnet; ?>" required>
        </div>
        <div class="form-group">
        <select name="profesion" class="form-control">
            <option value="">Seleccione su profesion</option>
            <option value="M" <?php echo ($row->profesion == 'Estudiante') ? 'selected' : ''; ?>>Estudiante</option>
            <option value="F" <?php echo ($row->profesion == 'Docente') ? 'selected' : ''; ?>>Docente</option>
          </select>
        </div>
        <div class="form-group">
          <input type="date" name="fechaNacimiento" class="form-control" value="<?php echo $row->fechaNacimiento; ?>">
        </div>
        <div class="form-group">
          <select name="sexo" class="form-control">
            <option value="">Seleccione su sexo</option>
            <option value="M" <?php echo ($row->sexo == 'M') ? 'selected' : ''; ?>>Masculino</option>
            <option value="F" <?php echo ($row->sexo == 'F') ? 'selected' : ''; ?>>Femenino</option>
          </select>
        </div>
        <div class="form-group">
          <input type="email" name="email" class="form-control" placeholder="Ingrese su email" value="<?php echo $row->email; ?>" required>
        </div>
        <div class="form-group">
          <input type="text" name="username" class="form-control" placeholder="Ingrese su nombre de usuario" value="<?php echo $row->username; ?>" required>
        </div>
        <div class="form-group">
          <select name="rol" class="form-control" required>
            <option value="">Seleccione el rol</option>
            <option value="administrador" <?php echo ($row->rol == 'administrador') ? 'selected' : ''; ?>>Administrador</option>
            <option value="lector" <?php echo ($row->rol == 'lector') ? 'selected' : ''; ?>>Lector</option>
          </select>
        </div>
        
        
        <button type="submit" class="btn btn-success">MODIFICAR USUARIO</button>
      <?php
        echo form_close();
      }
      ?>

    </div>
  </div>
</div>

<br>
<button onclick="goBack()" class="btn btn-secondary mb-3">Volver</button>
<script>
function goBack() {
    window.history.back();
}
</script>

      
      
    </div>
  </div>
</div>