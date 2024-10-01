<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2>Modificar Usuario</h2>
      <?php
      if (isset($infoUsuario) && $infoUsuario) {
        echo form_open_multipart('usuarios/modificarbd');
      ?>
        <input type="hidden" name="idUsuario" value="<?php echo $infoUsuario->idUsuario; ?>">

        <div class="form-group">
          <input type="text" name="nombres" class="form-control" placeholder="Ingrese sus nombres" value="<?php echo $infoUsuario->nombres; ?>" required>
        </div>
        <div class="form-group">
          <input type="text" name="apellidoPaterno" class="form-control" placeholder="Ingrese su apellido paterno" value="<?php echo $infoUsuario->apellidoPaterno; ?>" required>
        </div>
        <div class="form-group">
          <input type="text" name="apellidoMaterno" class="form-control" placeholder="Ingrese su apellido materno" value="<?php echo $infoUsuario->apellidoMaterno; ?>">
        </div>
        <div class="form-group">
          <input type="text" name="carnet" class="form-control" placeholder="Ingrese su carnet" value="<?php echo $infoUsuario->carnet; ?>" required>
        </div>
        <?php if ($infoUsuario->rol == 'lector'): ?>
        <div class="form-group">
          <select name="profesion" class="form-control">
            <option value="">Seleccione su profesion</option>
            <option value="Estudiante" <?php echo ($infoUsuario->profesion == 'Estudiante') ? 'selected' : ''; ?>>Estudiante</option>
            <option value="Docente" <?php echo ($infoUsuario->profesion == 'Docente') ? 'selected' : ''; ?>>Docente</option>
          </select>
        </div>
        <?php endif; ?>
        <div class="form-group">
          <input type="date" name="fechaNacimiento" class="form-control" value="<?php echo $infoUsuario->fechaNacimiento; ?>">
        </div>
        <div class="form-group">
          <select name="sexo" class="form-control">
            <option value="">Seleccione su sexo</option>
            <option value="M" <?php echo ($infoUsuario->sexo == 'M') ? 'selected' : ''; ?>>Masculino</option>
            <option value="F" <?php echo ($infoUsuario->sexo == 'F') ? 'selected' : ''; ?>>Femenino</option>
          </select>
        </div>
        <div class="form-group">
          <input type="email" name="email" class="form-control" placeholder="Ingrese su email" value="<?php echo $infoUsuario->email; ?>" required>
        </div>
        <div class="form-group">
          <input type="text" name="username" class="form-control" placeholder="Ingrese su nombre de usuario" value="<?php echo $infoUsuario->username; ?>" required>
        </div>
        <div class="form-group">
          <select name="rol" class="form-control" required>
            <option value="">Seleccione el rol</option>
            <option value="administrador" <?php echo ($infoUsuario->rol == 'administrador') ? 'selected' : ''; ?>>Administrador</option>
            <option value="encargado" <?php echo ($infoUsuario->rol == 'encargado') ? 'selected' : ''; ?>>Encargado</option>
            <option value="lector" <?php echo ($infoUsuario->rol == 'lector') ? 'selected' : ''; ?>>Lector</option>
          </select>
        </div>
        
        <button type="submit" class="btn btn-success">MODIFICAR USUARIO</button>
      
        <button onclick="goBack()" class="btn btn-secondary ">Volver</button>
    
      <?php
        echo form_close();
      } else {
        echo "<p>No se encontró información del usuario.</p>";
      }
      ?>
    </div>
  </div>
</div>



<script>
function goBack() {
    window.history.back();
}
</script>