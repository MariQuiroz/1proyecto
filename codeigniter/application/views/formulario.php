<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2>Agregar usuario</h2>

      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger">
          <?php echo $this->session->flashdata('error'); ?>
        </div>
      <?php endif; ?>

      <?php if ($this->session->flashdata('message')): ?>
        <div class="alert alert-success">
          <?php echo $this->session->flashdata('message'); ?>
        </div>
      <?php endif; ?>

      <?php echo form_open_multipart('usuario/agregarbd'); ?>

      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" placeholder="Ingrese su nombre">
      </div>
      <div class="mb-3">
        <label class="form-label">Primer Apellido</label>
        <input type="text" name="primerapellido" class="form-control" placeholder="Ingrese su primer apellido">
      </div>
      <div class="mb-3">
        <label class="form-label">Segundo Apellido</label>
        <input type="text" name="segundoapellido" class="form-control" placeholder="Ingrese su segundo apellido">
      </div>
      <div class="mb-3">
        <label class="form-label">CI</label>
        <input type="text" name="ci" class="form-control" placeholder="Ingrese su CI">
      </div>
      <div class="mb-3">
        <label class="form-label">Domicilio</label>
        <input type="text" name="domicilio" class="form-control" placeholder="Ingrese su domicilio">
      </div>
      <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input type="number" name="telefono" class="form-control" placeholder="Ingrese su teléfono">
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="Ingrese su email">
      </div>
      <div class="mb-3">
        <label class="form-label">Login</label>
        <input type="text" name="login" class="form-control" placeholder="Ingrese su login">
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Ingrese su password">
      </div>
      <div class="mb-3">
        <label class="form-label">Rol</label>
        <select name="rol" class="form-control">
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
