<!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
              <div class="content">
                <!-- Start Content-->
                  <div class="container-fluid">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Detalles de la Publicación</h4>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Título:</strong> <?php echo $publicacion->titulo; ?></p>
                        <p><strong>Editorial:</strong> <?php echo $publicacion->nombreEditorial; ?></p>
                        <p><strong>Tipo:</strong> <?php echo $publicacion->nombreTipo; ?></p>
                        <p><strong>Fecha de Publicación:</strong> <?php echo $publicacion->fechaPublicacion; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Número de Páginas:</strong> <?php echo $publicacion->numeroPaginas; ?></p>
                        <p><strong>Ubicación Física:</strong> <?php echo $publicacion->ubicacionFisica; ?></p>
                        <p><strong>Estado:</strong> <?php echo $this->publicacion_model->obtener_nombre_estado($publicacion->estado); ?></p>
                        <p><strong>Creado por:</strong> <?php echo $publicacion->nombres . ' ' . $publicacion->apellidoPaterno; ?></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Descripción:</h5>
                        <p><?php echo $publicacion->descripcion; ?></p>
                    </div>
                </div>
                <?php if (!$es_lector): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <a href="<?php echo site_url('publicaciones/editar/'.$publicacion->idPublicacion); ?>" class="btn btn-primary">Editar</a>
                        <a href="<?php echo site_url('publicaciones/index'); ?>" class="btn btn-secondary">Volver a la lista</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
  <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->
            </div>   
                </div> <!-- container -->
             </div> <!-- content -->
