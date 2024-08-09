
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Recuperar Contraseña | Nifty - Admin Template</title>

<body>
    <div id="container" class="cls-container">
        <div id="bg-overlay"></div>

        <div class="cls-content">
            <div class="cls-content-sm panel">
                <div class="panel-body">
                    <h1 class="h3">Recuperar Contraseña</h1>
                    <p>Ingresa tu correo electrónico para recibir un enlace de recuperación.</p>

                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($this->session->flashdata('message')): ?>
                        <div class="alert alert-success">
                            <?= $this->session->flashdata('message'); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= site_url('hemeroteca/forgot_password'); ?>">
                        <div class="form-group">
                            <input type="email" class="form-control" id="correo" name="correo" placeholder="Correo Electrónico" required>
                        </div>
                        <div class="form-group text-right">
                            <button class="btn btn-danger btn-lg btn-block" type="submit">Enviar Enlace de Recuperación</button>
                        </div>
                    </form>
                    <div class="pad-top">
                        <a href="<?= site_url('hemeroteca/login'); ?>" class="btn-link text-bold text-main">Volver al inicio de sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
</body>

</html>
