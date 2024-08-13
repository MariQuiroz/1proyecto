<div class="container">
  <main>
    <div class="py-5 text-center">
      <!-- Asegúrate de que la imagen exista en la ruta especificada -->
      <img class="d-block mx-auto mb-4" src="<?php echo base_url('img/bootstrap-logo.svg'); ?>" alt="Bootstrap Logo" width="72" height="57">
      <h2>Login de usuarios</h2>
    </div>

    <?php
    // Definir el mensaje basado en el valor de $msg
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

    <!-- Mostrar el mensaje en rojo si existe -->
    <h1 class="text-danger"><?php echo $mensaje; ?></h1>

    <?php
    // Abrir el formulario usando la función form_open_multipart para subir archivos si es necesario
    echo form_open_multipart('usuario/validar', array('id' => 'form1', 'class' => 'form-horizontal'));
    ?>
      <div class="mb-3">
        <label class="form-label">Login</label>
        <input type="text" name="login" class="form-control" placeholder="Ingrese su login" required>
      </div>
      <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Ingrese su password" required>
      </div>
      <button type="submit" class="btn btn-primary">Ingresar</button>
    <?php
    echo form_close();
    ?>

    <footer class="my-5 pt-5 text-muted text-center text-small">
      <p class="mb-1">&copy; 2017–2021 Company Name</p>
      <ul class="list-inline">
        <li class="list-inline-item"><a href="#">Privacy</a></li>
        <li class="list-inline-item"><a href="#">Terms</a></li>
        <li class="list-inline-item"><a href="#">Support</a></li>
      </ul>
    </footer>
  </main>
</div>
