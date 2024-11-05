<!-- application/views/publicaciones/busqueda_avanzada.php -->
<div class="container-fluid"> 
    <h2>Búsqueda Avanzada de Publicaciones</h2>
    
    <form action="<?= site_url('publicaciones/buscar_avanzado') ?>" method="get">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="titulo">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo" value="<?= set_value('titulo') ?>" placeholder="Ingrese el título">
            </div>
            <div class="form-group col-md-6">
                <label for="editorial">Editorial</label>
                <input type="text" class="form-control" id="editorial" name="editorial" value="<?= set_value('editorial') ?>" placeholder="Ingrese la editorial">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="tipo">Tipo</label>
                <select class="form-control" id="tipo" name="tipo">
                    <option value="">Todos</option>
                    <option value="periodico" <?= set_select('tipo', 'periodico') ?>>Periódico</option>
                    <option value="gaceta" <?= set_select('tipo', 'gaceta') ?>>Gaceta</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="año_desde">Año Desde</label>
                <input type="number" class="form-control" id="año_desde" name="año_desde" min="1800" max="<?= date('Y') ?>" value="<?= set_value('año_desde') ?>" placeholder="Año desde">
            </div>
            <div class="form-group col-md-4">
                <label for="año_hasta">Año Hasta</label>
                <input type="number" class="form-control" id="año_hasta" name="año_hasta" min="1800" max="<?= date('Y') ?>" value="<?= set_value('año_hasta') ?>" placeholder="Año hasta">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>

    <?php if (isset($resultados)): ?>
        <h3 class="mt-4">Resultados de la Búsqueda</h3>
        <?php if (empty($resultados)): ?>
            <p>No se encontraron resultados para tu búsqueda.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Editorial</th>
                        <th>Tipo</th>
                        <th>Año</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $publicacion): ?>
                        <tr>
                            <td><?= htmlspecialchars($publicacion->titulo) ?></td>
                            <td><?= htmlspecialchars($publicacion->editorial) ?></td>
                            <td><?= ucfirst($publicacion->tipo) ?></td>
                            <td><?= $publicacion->añoPublicacion ?></td>
                            <td>
                                <a href="<?= site_url('publicaciones/ver/'.$publicacion->idPublicacion) ?>" class="btn btn-sm btn-info">Ver</a>
                                <a href="<?= site_url('reservas/agregar/'.$publicacion->idPublicacion) ?>" class="btn btn-sm btn-primary">Reservar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>

<br>
<button onclick="goBack()" class="btn btn-secondary mb-3">Volver</button>
<script>
function goBack() {
    window.history.back();
}
</script>
