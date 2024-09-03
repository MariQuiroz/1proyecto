<div class="container">
  <main>
    <div class="py-5 text-center">
      <img class="d-block mx-auto mb-4" src="<?php echo base_url(); ?>img/bootstrap-logo.svg" alt="" width="72" height="57">
      <h2>Panel de lectores</h2>
    </div>

    <?php echo form_open_multipart('usuarios/logout'); ?>
      <button type="submit" name="buton2" class="btn btn-danger">CERRAR SESIÓN</button>
    <?php echo form_close(); ?>

    <div class="mt-4">
      <h3>Información del usuario:</h3>
      <p><strong>Nombre de usuario:</strong> <?php echo $this->session->userdata('username'); ?></p>
      <p><strong>Rol:</strong> <?php echo $this->session->userdata('rol'); ?></p>
      <p><strong>ID:</strong> <?php echo $this->session->userdata('idUsuario'); ?></p>
    </div>

    <!-- Aquí puedes agregar más contenido específico para los lectores -->

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