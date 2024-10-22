<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2>Modificar Usuario</h2>
      
      <?php if (isset($infoUsuario) && $infoUsuario): ?>
        <?= form_open_multipart('usuarios/modificarbd'); ?>
        <input type="hidden" name="idUsuario" value="<?= htmlspecialchars($infoUsuario->idUsuario); ?>">

        <div class="form-group">
          <label for="nombres">Nombres</label>
          <input type="text" id="nombres" name="nombres" class="form-control" placeholder="Ingrese sus nombres" value="<?= htmlspecialchars($infoUsuario->nombres); ?>" required>
        </div>
        
        <div class="form-group">
          <label for="apellidoPaterno">Apellido Paterno</label>
          <input type="text" id="apellidoPaterno" name="apellidoPaterno" class="form-control" placeholder="Ingrese su apellido paterno" value="<?= htmlspecialchars($infoUsuario->apellidoPaterno); ?>" required>
        </div>
        
        <div class="form-group">
          <label for="apellidoMaterno">Apellido Materno</label>
          <input type="text" id="apellidoMaterno" name="apellidoMaterno" class="form-control" placeholder="Ingrese su apellido materno" value="<?= htmlspecialchars($infoUsuario->apellidoMaterno); ?>">
        </div>
        
        <div class="form-group">
          <label for="carnet">Carnet</label>
          <input type="text" id="carnet" name="carnet" class="form-control" placeholder="Ingrese su carnet" value="<?= htmlspecialchars($infoUsuario->carnet); ?>" required>
        </div>
        
        <?php if ($infoUsuario->rol == 'lector'): ?>
          <div class="form-group">
            <label for="profesion">Profesión</label>
            <select id="profesion" name="profesion" class="form-control">
              <option value="">Seleccione su profesión</option>
              <option value="Estudiante" <?= ($infoUsuario->profesion == 'Estudiante') ? 'selected' : ''; ?>>Estudiante</option>
              <option value="Docente" <?= ($infoUsuario->profesion == 'Docente') ? 'selected' : ''; ?>>Docente</option>
            </select>
          </div>
        <?php endif; ?>

        <div class="form-group">
          <label for="fechaNacimiento">Fecha de Nacimiento</label>
          <input type="date" id="fechaNacimiento" name="fechaNacimiento" class="form-control" value="<?= htmlspecialchars($infoUsuario->fechaNacimiento); ?>">
        </div>
        
        <div class="form-group">
          <label for="sexo">Sexo</label>
          <select id="sexo" name="sexo" class="form-control">
            <option value="">Seleccione su sexo</option>
            <option value="M" <?= ($infoUsuario->sexo == 'M') ? 'selected' : ''; ?>>Masculino</option>
            <option value="F" <?= ($infoUsuario->sexo == 'F') ? 'selected' : ''; ?>>Femenino</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="Ingrese su email" value="<?= htmlspecialchars($infoUsuario->email); ?>" required>
        </div>
        
        <div class="form-group">
          <label for="username">Nombre de Usuario</label>
          <input type="text" id="username" name="username" class="form-control" placeholder="Ingrese su nombre de usuario" value="<?= htmlspecialchars($infoUsuario->username); ?>" required>
        </div>
        
        <div class="form-group">
          <label for="rol">Rol</label>
          <select id="rol" name="rol" class="form-control" required>
            <option value="">Seleccione el rol</option>
            <option value="administrador" <?= ($infoUsuario->rol == 'administrador') ? 'selected' : ''; ?>>Administrador</option>
            <option value="encargado" <?= ($infoUsuario->rol == 'encargado') ? 'selected' : ''; ?>>Encargado</option>
            <option value="lector" <?= ($infoUsuario->rol == 'lector') ? 'selected' : ''; ?>>Lector</option>
          </select>
        </div>

        <div class="btn-group" role="group">
          <button type="submit" class="btn btn-success">Modificar Usuario</button>
          <button type="button" class="btn btn-secondary" onclick="goBack()">Volver</button>
        </div>

        <?= form_close(); ?>
      <?php else: ?>
        <p>No se encontró información del usuario.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
function goBack() {
    window.history.back();
}
</script>
