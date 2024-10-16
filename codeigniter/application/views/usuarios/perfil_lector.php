<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Mi Perfil
            <small>Lector</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <h3 class="profile-username text-center"><?php echo $usuario->nombres . ' ' . $usuario->apellidoPaterno; ?></h3>
                        <p class="text-muted text-center">Lector</p>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Email</b> <a class="pull-right"><?php echo $usuario->email; ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Carnet</b> <a class="pull-right"><?php echo $usuario->carnet; ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Profesión</b> <a class="pull-right"><?php echo $usuario->profesion; ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>