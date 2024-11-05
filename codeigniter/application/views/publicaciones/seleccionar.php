<div class="container-fluid"> 
    <h2>Seleccionar Publicación para Solicitar</h2>
    <div class="table-responsive">
        <table class="table table-hover table-centered">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Editorial</th>
                    <th>Tipo</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($publicaciones as $publicacion): ?>
                <tr>
                    <td><?php echo $publicacion->titulo; ?></td>
                    <td><?php echo $publicacion->nombreEditorial; ?></td>
                    <td><?php echo $publicacion->nombreTipo; ?></td>
                    <td>
                        <!-- Enlace para solicitar la publicación -->
                        <a href="<?php echo site_url('solicitudes/crear/' . $publicacion->idPublicacion); ?>" class="btn btn-primary">Solicitar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
