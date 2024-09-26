	    <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
              <div class="content">
                <!-- Start Content-->
                  <div class="container-fluid">

              
  
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">Total Publicaciones</h4>
                <div class="widget-chart-1">
                    <div class="widget-chart-box-1 float-left" dir="ltr">
                        <input data-plugin="knob" data-width="80" data-height="80" data-fgColor="#ffbd4a"
                               data-bgColor="#FFE6BA" value="<?php echo $total_publicaciones; ?>"
                               data-skin="tron" data-angleOffset="180" data-readOnly=true
                               data-thickness=".15"/>
                    </div>
                    <div class="widget-detail-1 text-right">
                        <h2 class="font-weight-normal pt-2 mb-1"> <?php echo $total_publicaciones; ?> </h2>
                        <p class="text-muted mb-1">Publicaciones</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">Préstamos Activos</h4>
                <div class="widget-chart-1">
                    <div class="widget-chart-box-1 float-left" dir="ltr">
                        <input data-plugin="knob" data-width="80" data-height="80" data-fgColor="#3db9dc"
                               data-bgColor="#C3E8F5" value="<?php echo $prestamos_activos; ?>"
                               data-skin="tron" data-angleOffset="180" data-readOnly=true
                               data-thickness=".15"/>
                    </div>
                    <div class="widget-detail-1 text-right">
                        <h2 class="font-weight-normal pt-2 mb-1"> <?php echo $prestamos_activos; ?> </h2>
                        <p class="text-muted mb-1">Préstamos Activos</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">Solicitudes Pendientes</h4>
                <div class="widget-chart-1">
                    <div class="widget-chart-box-1 float-left" dir="ltr">
                        <input data-plugin="knob" data-width="80" data-height="80" data-fgColor="#f05050"
                               data-bgColor="#F9B9B9" value="<?php echo $solicitudes_pendientes; ?>"
                               data-skin="tron" data-angleOffset="180" data-readOnly=true
                               data-thickness=".15"/>
                    </div>
                    <div class="widget-detail-1 text-right">
                        <h2 class="font-weight-normal pt-2 mb-1"> <?php echo $solicitudes_pendientes; ?> </h2>
                        <p class="text-muted mb-1">Solicitudes Pendientes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">Acciones Rápidas</h4>
                <div class="row mt-3">
                    <div class="col-6 mb-2">
                        <a href="<?php echo site_url('publicaciones/agregar'); ?>" class="btn btn-primary btn-block">Agregar Publicación</a>
                    </div>
                    <div class="col-6 mb-2">
                        <a href="<?php echo site_url('usuarios/agregar'); ?>" class="btn btn-success btn-block">Agregar Lector</a>
                    </div>
                    <div class="col-6">
                        <a href="<?php echo site_url('solicitudes/pendientes'); ?>" class="btn btn-warning btn-block">Ver Solicitudes</a>
                    </div>
                    <div class="col-6">
                        <a href="<?php echo site_url('prestamos/nuevo'); ?>" class="btn btn-info btn-block">Nuevo Préstamo</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->


            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->
                    </div>   
                </div> <!-- container -->
             </div> <!-- content -->
