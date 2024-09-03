<div class="container">
  <main>
    <div class="py-5 text-center">
      <img class="d-block mx-auto mb-4" src="<?php echo base_url(); ?>img/bootstrap-logo.svg" alt="" width="72" height="57">
      <h2>Login de usuarios</h2>
    </div>

    <?php
    switch ($msg) {
      case '1':
        $mensaje = "Gracias por usar el sistema";
        break;
      case '2':
        $mensaje = "Usuario no identificado";
        break;
      case '3':
        $mensaje = "Acceso no válido - Favor inicie sesión";
        break;
      default:
        $mensaje = "";
        break;
    }
    ?>

    <h1 class="text-danger"><?php echo $mensaje; ?></h1>

    <?php
    echo form_open_multipart('usuarios/validar', array('id' => 'form1', 'class' => 'form-control'))
    ?>
      <div class="mb-3">
        <label class="form-label">Nombre de usuario</label>
        <input type="text" name="username" class="form-control" placeholder="Ingrese su nombre de usuario">
      </div>
      <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña">
      </div>
      <button type="submit" class="btn btn-primary">Iniciar sesión</button>
    <?php
    echo form_close();
    ?>

  </main>

  <footer class="my-5 pt-5 text-muted text-center text-small">
    <p class="mb-1">&copy; 2017–<?php echo date('Y'); ?> Nombre de la Compañía</p>
    <ul class="list-inline">
      <li class="list-inline-item"><a href="#">Privacidad</a></li>
      <li class="list-inline-item"><a href="#">Términos</a></li>
      <li class="list-inline-item"><a href="#">Soporte</a></li>
    </ul>
  </footer>
</div>