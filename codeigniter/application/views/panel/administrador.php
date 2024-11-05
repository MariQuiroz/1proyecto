 <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
              <div class="content">
                <!-- Start Content-->
                  <div class="container-fluid">
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card-box">
                                    <h4 class="header-title mt-0 mb-3">Total Usuarios</h4>
                                    <div class="widget-chart-1">
                                        <div class="widget-chart-box-1 float-left" dir="ltr">
                                            <input data-plugin="knob" data-width="80" data-height="80" data-fgColor="#f05050 "
                                                data-bgColor="#F9B9B9" value="<?php echo $total_usuarios; ?>"
                                                data-skin="tron" data-angleOffset="180" data-readOnly=true
                                                data-thickness=".15"/>
                                        </div>
                                        <div class="widget-detail-1 text-right">
                                            <h2 class="font-weight-normal pt-2 mb-1"> <?php echo $total_usuarios; ?> </h2>
                                            <p class="text-muted mb-1">Usuarios Registrados</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
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

                            <div class="col-xl-3 col-md-6">
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

                            <div class="col-xl-3 col-md-6">
                        <div class="card-box">
                            <h4 class="header-title mt-0 mb-3">Préstamos No Devueltos</h4>
                            <div class="widget-chart-1">
                                <div class="widget-chart-box-1 float-left" dir="ltr">
                                    <input data-plugin="knob" data-width="80" data-height="80" data-fgColor="#f05050 "
                                        data-bgColor="#F9B9B9" value="<?php echo $prestamos_no_devueltos; ?>"
                                        data-skin="tron" data-angleOffset="180" data-readOnly=true data-thickness=".15"/>
                                </div>
                                <div class="widget-detail-1 text-right">
                                    <h2 class="font-weight-normal pt-2 mb-1"> <?php echo $prestamos_no_devueltos; ?> </h2>
                                    <p class="text-muted mb-1">Préstamos No Devueltos Hoy</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                        </div>
                        <!-- end row -->

                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card-box">
                                    <h4 class="header-title mt-0 mb-3">Solicitudes Pendientes</h4>
                                    <div class="widget-chart text-center">
                                        <div dir="ltr">
                                            <input data-plugin="knob" data-width="120" data-height="120" data-linecap=round
                                                data-fgColor="#f05050" value="<?php echo $solicitudes_pendientes; ?>" data-skin="tron" data-angleOffset="180"
                                                data-readOnly=true data-thickness=".12"/>
                                        </div>

                                        <h5 class="text-muted mt-3">Total de solicitudes pendientes</h5>
                                        <h2><?php echo $solicitudes_pendientes; ?></h2>

                                        <p class="text-muted w-75 mx-auto sp-line-2">Solicitudes que requieren atención inmediata.</p>

                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <a href="<?php echo site_url('solicitudes/pendientes'); ?>" class="btn btn-danger btn-block">Ver Solicitudes Pendientes</a>
                                            </div>
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