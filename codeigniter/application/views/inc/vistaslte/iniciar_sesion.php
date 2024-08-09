<body>
    <div id="container" class="cls-container">

        <!-- BACKGROUND IMAGE -->
        <!--===================================================-->
        <div id="bg-overlay"></div>

        <!-- LOGIN FORM -->
        <!--===================================================-->
        <div class="cls-content">
            <div class="cls-content-sm panel">
                <div class="panel-body">
                    <div class="mar-ver pad-btm">
                        <h1 class="h3">Inicio de Sesión</h1>
                        <p>Inicia sesión en tu cuenta</p>
                    </div>
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
                    <form method="post" action="<?= site_url('hemeroteca/login'); ?>">
                        <div class="form-group">
                            <input type="email" class="form-control" id="correo" name="correo" placeholder="Correo Electrónico" required autofocus="">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                        </div>
                        <div class="g-recaptcha" data-sitekey="6LcWryIqAAAAACi4lEvDwhs80IBNeQ5UrL2zNw7u"></div>
                        <button class="btn btn-primary btn-lg btn-block" type="submit">Iniciar Sesión</button>
                    </form>
                </div>
                <div class="pad-all">
                    <a href="<?= site_url('hemeroteca/forgot_password'); ?>" class="btn-link mar-rgt">¿Olvidaste tu contraseña?</a>
                    <a href="<?= site_url('hemeroteca/create_usuario'); ?>" class="btn-link mar-lft">Crear una nueva cuenta</a>

                    <div class="media pad-top bord-top">
                        <div class="pull-right">
                            <a href="#" class="pad-rgt"><i class="demo-psi-facebook icon-lg text-primary"></i></a>
                            <a href="#" class="pad-rgt"><i class="demo-psi-twitter icon-lg text-info"></i></a>
                            <a href="#" class="pad-rgt"><i class="demo-psi-google-plus icon-lg text-danger"></i></a>
                        </div>
                        <div class="media-body text-left text-bold text-main">
                            Inicia sesión con
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--===================================================-->

        <!-- DEMO PURPOSE ONLY -->
        <!--===================================================-->
        <div class="demo-bg">
            <div id="demo-bg-list">
                <div class="demo-loading"><i class="psi-repeat-2"></i></div>
                <img class="demo-chg-bg bg-trans active" src="img/bg-img/thumbs/bg-trns.jpeg" alt="Background Image">
                <img class="demo-chg-bg" src="<?php echo base_url(); ?>adminNifty/pages/assets/img/bg-img/thumbs/bg-img-1.jpeg" alt="Background Image">
                <img class="demo-chg-bg" src="<?php echo base_url(); ?>adminNifty/pages/assets/img/bg-img/thumbs/bg-img-2.jpeg" alt="Background Image">
                <img class="demo-chg-bg" src="<?php echo base_url(); ?>adminNifty/pages/assets/img/bg-img/thumbs/bg-img-3.jpeg" alt="Background Image">
                <img class="demo-chg-bg" src="<?php echo base_url(); ?>adminNifty/pages/assets/img/bg-img/thumbs/bg-img-4.jpeg" alt="Background Image">
                <img class="demo-chg-bg" src="<?php echo base_url(); ?>adminNifty/pages/assets/img/bg-img/thumbs/bg-img-5.jpeg" alt="Background Image">
                <img class="demo-chg-bg" src="<?php echo base_url(); ?>adminNifty/pages/assets/img/bg-img/thumbs/bg-img-6.jpeg" alt="Background Image">
                <img class="demo-chg-bg" src="<?php echo base_url(); ?>adminNifty/pages/assets/img/bg-img/thumbs/bg-img-7.jpeg" alt="Background Image">
            </div>
        </div>
        <!--===================================================-->
    </div>
    <!--===================================================-->
    <!-- END OF CONTAINER -->

</body>

