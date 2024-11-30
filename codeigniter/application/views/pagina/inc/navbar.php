<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="<?php echo base_url(); ?>img/umss2.png" alt="logo" height="50"> 
                <span class="text-white font-weight-bold">Hemeroteca José Antonio Arze</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo site_url('home/index'); ?>">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo site_url('home/sobre_nosotros'); ?>">Sobre Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#catalogo">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo site_url('home/proceso'); ?>">Proceso de Préstamo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?php echo site_url('home/contacto'); ?>">Nuestra Ubicación</a>
                    </li>
                    <li class="nav-item ml-3">
                        <a class="btn btn-hemeroteca" href="<?php echo site_url('usuarios/index'); ?>">
                            Iniciar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>