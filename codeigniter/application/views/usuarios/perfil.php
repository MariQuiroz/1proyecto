<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid"> 
   
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Detalles del Perfil</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Nombre Completo</th>
                                        <td><?php echo $usuario->nombres . ' ' . $usuario->apellidoPaterno . ' ' . $usuario->apellidoMaterno; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Correo Electrónico</th>
                                        <td><?php echo $usuario->email; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Carnet</th>
                                        <td><?php echo $usuario->carnet; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Profesión</th>
                                        <td><?php echo $usuario->profesion; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Fecha de Nacimiento</th>
                                        <td><?php echo $usuario->fechaNacimiento; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Sexo</th>
                                        <td><?php echo $usuario->sexo == 'M' ? 'Masculino' : 'Femenino'; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <a href="<?php echo site_url('usuarios/editar_perfil'); ?>" class="btn btn-primary mt-3">Editar Perfil</a>
                        <a href="<?php echo site_url('usuarios/cambiar_password'); ?>" class="btn btn-secondary mt-3">Cambiar Contraseña</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
