 <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">
                        
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Xeria</a></li>
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboards</a></li>
                                            <li class="breadcrumb-item active">Dashboard</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Dashboard</h4>
                                </div>
                            </div>
                        </div>     
                        <!-- end page title --> 
                       <?php echo form_open_multipart('usuarios/logout'); ?>
        <button type="submit" name="buton2" class="btn btn-danger">CERRAR SESIÓN</button>
      <?php echo form_close(); ?>

      <?php echo form_open_multipart('usuarios/registrar'); ?>
        <button type="submit" name="buton2" class="btn btn-success">REGISTRAR USUARIO</button>
      <?php echo form_close(); ?>

      <?php echo form_open_multipart('usuarios/listapdf'); ?>
        <button type="submit" name="buton2" class="btn btn-success">Lista usuarios PDF</button>
      <?php echo form_close(); ?>

      <h1>Lista de usuarios habilitados</h1>
      <h1>Username: <?php echo $this->session->userdata('username'); ?></h1>
      <h1>Rol: <?php echo $this->session->userdata('rol'); ?></h1>
      <h1>ID: <?php echo $this->session->userdata('idUsuario'); ?></h1>

      <?php echo form_open_multipart('usuarios/deshabilitados'); ?>
        <button type="submit" name="buton2" class="btn btn-warning">VER USUARIOS DESHABILITADOS</button>
      <?php echo form_close(); ?>

      <br>

      <?php echo form_open_multipart('usuarios/agregar'); ?>
        <button type="submit" name="buton1" class="btn btn-primary">AGREGAR USUARIO</button>
      <?php echo form_close(); ?>



    

      <table id="scroll-horizontal-datatable" class="table w-100 nowrap">
        
  
        <thead>
          <tr>
            <th>#</th>
            <th>Foto</th>
            <th>Nombres</th>
            <th>Apellido Paterno</th>
            <th>Apellido Materno</th>
            <th>Carnet</th>
            <th>Rol</th>
            <th>Modificar</th>
            <th>Eliminar</th>
            <th>Deshabilitar</th>
          </tr>
        </thead>
        <tbody>
            <?php
            $indice=1;
            foreach ($usuarios->result() as $row)
            {
            ?>
                <tr>
                    <td><?php echo $indice; ?></td>
                    <td>
                        <?php
                        $foto=$row->foto;
                        if($foto=="") {
                            ?>
                            <img src="<?php echo base_url(); ?>/uploads/user.jpg" width="50px">
                            <?php
                        } else {
                            ?>
                            <img src="<?php echo base_url(); ?>/uploads/<?php echo $foto; ?>" width="50px">
                            <?php
                        }
                        ?>
                    </td>
                    <td><?php echo $row->nombres; ?></td>
                    <td><?php echo $row->apellidoPaterno; ?></td>
                    <td><?php echo $row->apellidoMaterno; ?></td>
                    <td><?php echo $row->carnet; ?></td>
                    <td><?php echo $row->rol; ?></td>
                    <td>
                        <?php echo form_open_multipart("usuarios/modificar"); ?>
                        <input type="hidden" name="idUsuario" value="<?php echo $row->idUsuario; ?>">
                        <input type="submit" name="buttony" value="Modificar" class="btn btn-success">
                        <?php echo form_close(); ?>
                    </td>
                    <td>
                        <?php echo form_open_multipart("usuarios/eliminarbd"); ?>
                        <input type="hidden" name="idUsuario" value="<?php echo $row->idUsuario; ?>">
                        <input type="submit" name="buttonx" value="Eliminar" class="btn btn-danger">
                        <?php echo form_close(); ?>
                    </td>
                    <td>
                        <?php echo form_open_multipart("usuarios/deshabilitarbd"); ?>
                        <input type="hidden" name="idUsuario" value="<?php echo $row->idUsuario; ?>">
                        <input type="submit" name="buttonz" value="Deshabilitar" class="btn btn-warning">
                        <?php echo form_close(); ?>
                    </td>
                </tr>
            <?php
            $indice++;
            }
            ?>
        </tbody>
    </table>

                                    <h4 class="header-title mb-3">Top 5 Users Balances</h4>

                                    <div class="table-responsive">
                                        <table class="table table-borderless table-hover table-centered m-0">

                                            <thead class="thead-light">
                                                <tr>
                                                    <th colspan="2">Profile</th>
                                                    <th>Currency</th>
                                                    <th>Balance</th>
                                                    <th>Reserved in orders</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="width: 36px;">
                                                        <img src="<?php echo base_url(); ?>adminXeria/light/dist/assets/images/users/user-2.jpg" alt="contact-img" title="contact-img" class="rounded-circle avatar-sm">
                                                    </td>
    
                                                    <td>
                                                        <h5 class="m-0 font-weight-normal">Tomaslau</h5>
                                                        <p class="mb-0 text-muted"><small>Member Since 2017</small></p>
                                                    </td>
    
                                                    <td>
                                                        <i class="mdi mdi-currency-btc text-primary"></i> BTC
                                                    </td>
    
                                                    <td>
                                                        0.00816117 BTC
                                                    </td>
    
                                                    <td>
                                                        0.00097036 BTC
                                                    </td>
    
                                                    <td>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i class="mdi mdi-plus"></i></a>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-danger"><i class="mdi mdi-minus"></i></a>
                                                    </td>
                                                </tr>
    
                                                <tr>
                                                    <td style="width: 36px;">
                                                        <img src="<?php echo base_url(); ?>adminXeria/light/dist/assets/images/users/user-3.jpg" alt="contact-img" title="contact-img" class="rounded-circle avatar-sm">
                                                    </td>
    
                                                    <td>
                                                        <h5 class="m-0 font-weight-normal">Erwin E. Brown</h5>
                                                        <p class="mb-0 text-muted"><small>Member Since 2017</small></p>
                                                    </td>
    
                                                    <td>
                                                        <i class="mdi mdi-currency-eth text-primary"></i> ETH
                                                    </td>
    
                                                    <td>
                                                        3.16117008 ETH
                                                    </td>
    
                                                    <td>
                                                        1.70360009 ETH
                                                    </td>
    
                                                    <td>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i class="mdi mdi-plus"></i></a>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-danger"><i class="mdi mdi-minus"></i></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 36px;">
                                                        <img src="<?php echo base_url(); ?>adminXeria/light/dist/assets/images/users/user-4.jpg" alt="contact-img" title="contact-img" class="rounded-circle avatar-sm">
                                                    </td>
    
                                                    <td>
                                                        <h5 class="m-0 font-weight-normal">Margeret V. Ligon</h5>
                                                        <p class="mb-0 text-muted"><small>Member Since 2017</small></p>
                                                    </td>
    
                                                    <td>
                                                        <i class="mdi mdi-currency-eur text-primary"></i> EUR
                                                    </td>
    
                                                    <td>
                                                        25.08 EUR
                                                    </td>
    
                                                    <td>
                                                        12.58 EUR
                                                    </td>
    
                                                    <td>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i class="mdi mdi-plus"></i></a>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-danger"><i class="mdi mdi-minus"></i></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 36px;">
                                                        <img src="<?php echo base_url(); ?>adminXeria/light/dist/assets/images/users/user-5.jpg" alt="contact-img" title="contact-img" class="rounded-circle avatar-sm">
                                                    </td>
    
                                                    <td>
                                                        <h5 class="m-0 font-weight-normal">Jose D. Delacruz</h5>
                                                        <p class="mb-0 text-muted"><small>Member Since 2017</small></p>
                                                    </td>
    
                                                    <td>
                                                        <i class="mdi mdi-currency-cny text-primary"></i> CNY
                                                    </td>
    
                                                    <td>
                                                        82.00 CNY
                                                    </td>
    
                                                    <td>
                                                        30.83 CNY
                                                    </td>
    
                                                    <td>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i class="mdi mdi-plus"></i></a>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-danger"><i class="mdi mdi-minus"></i></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 36px;">
                                                        <img src="<?php echo base_url(); ?>adminXeria/light/dist/assets/images/users/user-6.jpg" alt="contact-img" title="contact-img" class="rounded-circle avatar-sm">
                                                    </td>
    
                                                    <td>
                                                        <h5 class="m-0 font-weight-normal">Luke J. Sain</h5>
                                                        <p class="mb-0 text-muted"><small>Member Since 2017</small></p>
                                                    </td>
    
                                                    <td>
                                                        <i class="mdi mdi-currency-btc text-primary"></i> BTC
                                                    </td>
    
                                                    <td>
                                                        2.00816117 BTC
                                                    </td>
    
                                                    <td>
                                                        1.00097036 BTC
                                                    </td>
    
                                                    <td>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-light"><i class="mdi mdi-plus"></i></a>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-danger"><i class="mdi mdi-minus"></i></a>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div> <!-- end col -->

                            <div class="col-xl-6">
                                <div class="card-box">
                                    <h4 class="header-title mb-3">Revenue History</h4>

                                    <div class="table-responsive">
                                        <table class="table table-borderless table-hover table-centered m-0">

                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Marketplaces</th>
                                                    <th>Date</th>
                                                    <th>Payouts</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <h5 class="m-0 font-weight-normal">Themes Market</h5>
                                                    </td>
    
                                                    <td>
                                                        Oct 15, 2018
                                                    </td>
    
                                                    <td>
                                                        $5848.68
                                                    </td>
    
                                                    <td>
                                                        <span class="badge badge-light-warning">Upcoming</span>
                                                    </td>
    
                                                    <td>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-secondary"><i class="mdi mdi-pencil"></i></a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <h5 class="m-0 font-weight-normal">Freelance</h5>
                                                    </td>
    
                                                    <td>
                                                        Oct 12, 2018
                                                    </td>
    
                                                    <td>
                                                        $1247.25
                                                    </td>
    
                                                    <td>
                                                        <span class="badge badge-light-success">Paid</span>
                                                    </td>
    
                                                    <td>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-secondary"><i class="mdi mdi-pencil"></i></a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <h5 class="m-0 font-weight-normal">Share Holding</h5>
                                                    </td>
    
                                                    <td>
                                                        Oct 10, 2018
                                                    </td>
    
                                                    <td>
                                                        $815.89
                                                    </td>
    
                                                    <td>
                                                        <span class="badge badge-light-success">Paid</span>
                                                    </td>
    
                                                    <td>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-secondary"><i class="mdi mdi-pencil"></i></a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <h5 class="m-0 font-weight-normal">Envato's Affiliates</h5>
                                                    </td>
    
                                                    <td>
                                                        Oct 03, 2018
                                                    </td>
    
                                                    <td>
                                                        $248.75
                                                    </td>
    
                                                    <td>
                                                        <span class="badge badge-light-danger">Overdue</span>
                                                    </td>
    
                                                    <td>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-secondary"><i class="mdi mdi-pencil"></i></a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <h5 class="m-0 font-weight-normal">Marketing Revenue</h5>
                                                    </td>
    
                                                    <td>
                                                        Sep 21, 2018
                                                    </td>
    
                                                    <td>
                                                        $978.21
                                                    </td>
    
                                                    <td>
                                                        <span class="badge badge-light-warning">Upcoming</span>
                                                    </td>
    
                                                    <td>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-secondary"><i class="mdi mdi-pencil"></i></a>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <h5 class="m-0 font-weight-normal">Advertise Revenue</h5>
                                                    </td>
    
                                                    <td>
                                                        Sep 15, 2018
                                                    </td>
    
                                                    <td>
                                                        $358.10
                                                    </td>
    
                                                    <td>
                                                        <span class="badge badge-light-success">Paid</span>
                                                    </td>
    
                                                    <td>
                                                        <a href="javascript: void(0);" class="btn btn-xs btn-secondary"><i class="mdi mdi-pencil"></i></a>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>

                                        
                                    </div> <!-- end .table-responsive-->
                                </div> <!-- end card-box-->
                            </div> <!-- end col -->

                           

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->



                        </div>
                        
                    </div> <!-- container -->

                </div> <!-- content -->

               