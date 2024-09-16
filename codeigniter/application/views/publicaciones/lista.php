
<!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        


<!-- application/views/publicaciones/lista.php -->
<div class="container">
    <h2>Lista de Publicaciones</h2>
    <?php if ($this->session->flashdata('mensaje')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('mensaje') ?></div>
    <?php endif; ?>
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
            <?php foreach ($publicaciones as $publicacion): ?>
                <tr>
                    <td><?= $publicacion->titulo ?></td>
                    <td><?= $publicacion->editorial ?></td>
                    <td><?= ucfirst($publicacion->tipo) ?></td>
                    <td><?= $publicacion->añoPublicacion ?></td>
                    <td>
                        <a href="<?= site_url('publicaciones/editar/'.$publicacion->idPublicacion) ?>" class="btn btn-sm btn-primary">Editar</a>
                        <a href="<?= site_url('publicaciones/eliminar/'.$publicacion->idPublicacion) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar esta publicación?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="<?= site_url('publicaciones/agregar') ?>" class="btn btn-success">Agregar Nueva Publicación</a>
</div>

                                       

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->



                        </div>
                        
                    </div> <!-- container -->

                </div> <!-- content -->

               