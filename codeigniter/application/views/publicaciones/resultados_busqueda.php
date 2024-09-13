<div class="container mt-4">
    <h2>Resultados de Búsqueda</h2>

    <?php if (empty($publicaciones)): ?>
        <p>No se encontraron resultados para su búsqueda.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Editorial</th>
                    <th>Año de Publicación</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($publicaciones as $publicacion): ?>
                    <tr>
                        <td><?= $publicacion->titulo ?></td>
                        <td><?= $publicacion->editorial ?></td>
                        <td><?= $publicacion->añoPublicacion ?></td>
                        <td><?= ucfirst($publicacion->tipo) ?></td>
                        <td>
                            <a href="<?= site_url('publicaciones/editar/'.$publicacion->idPublicacion) ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="<?= site_url('reservas/agregar/'.$publicacion->idPublicacion) ?>" class="btn btn-sm btn-primary">Reservar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="<?= site_url('publicaciones/index') ?>" class="btn btn-secondary mt-3">Volver a la lista de publicaciones</a>
</div>
<br>
<button onclick="goBack()" class="btn btn-secondary mb-3">Volver</button>
<script>
function goBack() {
    window.history.back();
}
</script>