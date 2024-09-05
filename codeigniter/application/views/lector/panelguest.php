<div class="container">
  <main>
    <div class="py-5 text-center">
      <img class="d-block mx-auto mb-4" src="<?php echo base_url(); ?>img/bootstrap-logo.svg" alt="" width="72" height="57">
      <h2>Panel de lectores</h2>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3>Información del usuario</h3>
      <?php echo form_open('usuarios/logout'); ?>
        <button type="submit" class="btn btn-danger">CERRAR SESIÓN</button>
      <?php echo form_close(); ?>
    </div>

    <div class="card mb-4">
      <div class="card-body">
        <p><strong>Nombre de usuario:</strong> <?php echo $this->session->userdata('username'); ?></p>
        <p><strong>Rol:</strong> <?php echo $this->session->userdata('rol'); ?></p>
        <p class="mb-0"><strong>ID:</strong> <?php echo $this->session->userdata('idUsuario'); ?></p>
      </div>
    </div>

    <div class="mt-5">
      <h3 class="mb-4">Lista de Publicaciones</h3>
      <?php if(empty($publicaciones)): ?>
        <p class="alert alert-info">No hay publicaciones disponibles en este momento.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-light">
              <tr>
                <th>Título</th>
                <th>Editorial</th>
                <th>Fecha de Publicación</th>
                <th>Tipo</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($publicaciones as $publicacion): ?>
                <tr>
                  <td><?php echo htmlspecialchars($publicacion->titulo); ?></td>
                  <td><?php echo htmlspecialchars($publicacion->editorial); ?></td>
                  <td>
                    <?php 
                      $fecha = '';
                      if ($publicacion->diaPublicacion) {
                        $fecha .= str_pad($publicacion->diaPublicacion, 2, '0', STR_PAD_LEFT) . '/';
                      }
                      if ($publicacion->mesPublicacion) {
                        $fecha .= str_pad($publicacion->mesPublicacion, 2, '0', STR_PAD_LEFT) . '/';
                      }
                      $fecha .= $publicacion->añoPublicacion;
                      echo $fecha;
                    ?>
                  </td>
                  <td><?php echo ucfirst($publicacion->tipo); ?></td>
                  <td>
                    <a href="<?php echo site_url('publicaciones/ver/'.$publicacion->idPublicacion); ?>" class="btn btn-primary btn-sm">Ver detalles</a>
                    <?php echo form_open('reservas/crear', ['class' => 'd-inline']); ?>
                      <input type="hidden" name="idPublicacion" value="<?php echo $publicacion->idPublicacion; ?>">
                      <button type="submit" class="btn btn-success btn-sm">Reservar</button>
                    <?php echo form_close(); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
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