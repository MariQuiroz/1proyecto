
<div class="container">
  <div class="row">
    <div class="col-md-12">

      <h2>Agregar Usuario</h2>

      <?php echo form_open_multipart('usuarios/agregarbd'); ?>

      <div class="form-group">
        <input type="text" name="nombres" class="form-control" placeholder="Ingrese sus nombres" required>
      </div>
      <div class="form-group">
        <input type="text" name="apellidoPaterno" class="form-control" placeholder="Ingrese su apellido paterno" required>
      </div>
      <div class="form-group">
        <input type="text" name="apellidoMaterno" class="form-control" placeholder="Ingrese su apellido materno">
      </div>
      <div class="form-group">
        <input type="text" name="carnet" class="form-control" placeholder="Ingrese su carnet" required>
      </div>
      <div class="form-group">
        <input type="text" name="profesion" class="form-control" >
        <select name="profesion" class="form-control">
          <option value="">Seleccione su profesion</option>
          <option value="Estudiante Umss">Estudiante Umss</option>
          <option value="Docente Umss">Docente Umss</option>
          <option value="Investigador">Investigador</option>
          <option value="Publico en General">Publico en General</option>
        </select>
      </div>
      <div class="form-group">
        <input type="date" name="fechaNacimiento" class="form-control" placeholder="Fecha de nacimiento">
      </div>
      <div class="form-group">
        <select name="sexo" class="form-control">
          <option value="">Seleccione su sexo</option>
          <option value="M">Masculino</option>
          <option value="F">Femenino</option>
        </select>
      </div>
      <div class="form-group">
        <input type="email" name="email" class="form-control" placeholder="Ingrese su email" required>
      </div>
      <div class="form-group">
        <input type="text" name="username" class="form-control" placeholder="Ingrese su nombre de usuario" required>
      </div>
      <div class="form-group">
        <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required>
      </div>
      <div class="form-group">
        <select name="rol" class="form-control" required>
          <option value="">Seleccione el rol</option>
          <option value="administrador">Administrador</option>
          <option value="lector">Lector</option>
        </select>
      </div>
      <div class="form-group">
        <input type="file" name="foto" class="form-control-file">
      </div>
      
      <button type="submit" class="btn btn-primary">AGREGAR USUARIO</button>

      <?php echo form_close(); ?>
      
    </div>
  </div>
</div>
<br>

