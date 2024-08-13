<div class="container">
  <div class="row">
    <div class="col-md-12">

      <h2>Agregar usuario</h2>

      <?php echo form_open_multipart('usuario/agregarbd'); ?>

      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" placeholder="Ingrese su nombre" required>
      </div>

      <div class="mb-3">
        <label for="primerapellido" class="form-label">Primer Apellido</label>
        <input type="text" name="primerapellido" class="form-control" placeholder="Ingrese su primer apellido" required>
      </div>

      <div class="mb-3">
        <label for="segundoapellido" class="form-label">Segundo Apellido</label>
        <input type="text" name="segundoapellido" class="form-control" placeholder="Ingrese su segundo apellido">
      </div>

      <div class="mb-3">
        <label for="ci" class="form-label">CI</label>
        <input type="text" name="ci" class="form-control" placeholder="Ingrese su CI" required>
      </div>

      <div class="mb-3">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-control" placeholder="Ingrese su teléfono">
      </div>

      <div class="mb-3">
        <label for="domicilio" class="form-label">Domicilio</label>
        <input type="text" name="domicilio" class="form-control" placeholder="Ingrese su domicilio">
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="Ingrese su email" required>
      </div>

      <div class="mb-3">
        <label for="login" class="form-label">Login</label>
        <input type="text" name="login" class="form-control" placeholder="Ingrese su login" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required>
      </div>

      <div class="mb-3">
        <label for="rol" class="form-label">Rol</label>
        <select name="rol" class="form-select" required>
          <option value="1">Administrador</option>
          <option value="2">Encargado</option>
          <option value="3">Lector</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary">AGREGAR USUARIO</button>

      <?php echo form_close(); ?>

    </div>
  </div>
</div>
