<body>
    <div id="container" class="cls-container">
        <!-- BACKGROUND IMAGE -->
        <div id="bg-overlay"></div>

        <!-- REGISTRATION FORM -->
        <div class="cls-content">
            <div class="cls-content-lg panel">
                <div class="panel-body">
                    <div class="mar-ver pad-btm">
                        <h1 class="h3">Crear una Nueva Cuenta</h1>
                        <p>Únete a la comunidad de Nifty! Vamos a configurar tu cuenta.</p>
                    </div>

                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= site_url('hemeroteca/create_usuario'); ?>">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Nombre" id="nombre" name="nombre" required>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Primer Apellido" id="primer_apellido" name="primer_apellido" required>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Segundo Apellido" id="segundo_apellido" name="segundo_apellido" required>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="CI" id="ci" name="ci" required>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Teléfono" id="telefono" name="telefono">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Dirección" id="direccion" name="direccion">
                                </div>
                                <div class="form-group">
                                    <select class="form-control" id="rol" name="rol" required>
                                        <option value="" disabled selected>Seleccionar Rol</option>
                                        <option value="1">Administrador</option>
                                        <option value="2">Encargado</option>
                                        <option value="3">Usuario Normal</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="email" class="form-control" placeholder="Correo Electrónico" id="correo" name="correo" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" placeholder="Contraseña" id="contraseña" name="contraseña" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" placeholder="Repetir Contraseña" id="repetir_contraseña" name="repetir_contraseña" required>
                                </div>
                            </div>
                        </div>

                        <div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY"></div>
                        <button class="btn btn-primary btn-lg btn-block" type="submit">Registrar</button>
                    </form>
                </div>

                <div class="pad-all">
                    ¿Ya tienes una cuenta? <a href="<?= site_url('hemeroteca/login'); ?>" class="btn-link mar-rgt text-bold">Iniciar Sesión</a>
                </div>
            </div>
        </div>
    </div>
