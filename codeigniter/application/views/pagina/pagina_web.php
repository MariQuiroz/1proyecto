
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container hero-content text-center">
            <h1 class="display-4 mb-4 font-weight-bold">Bienvenido a la Hemeroteca UMSS</h1>
            <p class="lead mb-5">Tu fuente de conocimiento académico en la Facultad de Ciencias Económicas</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="<?php echo site_url('usuarios/registro'); ?>" class="btn btn-lg btn-hemeroteca mr-3">
                    <i class="la la-user-plus mr-2"></i>Registrarse
                </a>
                <a href="<?php echo site_url('catalogo'); ?>" class="btn btn-lg btn-umss">
                    <i class="la la-book mr-2"></i>Ver Catálogo
                </a>
            </div>
            <div class="scroll-down text-white">
                <i class="la la-arrow-down la-2x"></i>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="font-weight-bold">Servicios Principales</h2>
            <p class="text-muted">Descubre todo lo que nuestra hemeroteca tiene para ofrecerte</p>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="feature-card h-100 shadow-sm p-4">
                    <div class="feature-icon">
                        <i class="la la-book la-2x text-primary"></i>
                    </div>
                    <h5 class="text-center font-weight-bold">Amplio Catálogo</h5>
                    <p class="text-center text-muted">Accede a nuestra extensa colección de publicaciones académicas y científicas.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card h-100 shadow-sm p-4">
                    <div class="feature-icon">
                        <i class="la la-clock la-2x text-primary"></i>
                    </div>
                    <h5 class="text-center font-weight-bold">Préstamos Eficientes</h5>
                    <p class="text-center text-muted">Proceso simple con confirmación en 2 horas presentando tu carnet de identidad.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card h-100 shadow-sm p-4">
                    <div class="feature-icon">
                        <i class="la la-bell la-2x text-primary"></i>
                    </div>
                    <h5 class="text-center font-weight-bold">Notificaciones</h5>
                    <p class="text-center text-muted">Recibe alertas sobre el estado de tus solicitudes y disponibilidad.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Proceso Preview Section -->
    <div class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="font-weight-bold mb-4">¿Cómo solicitar un préstamo?</h2>
                    <p class="text-muted mb-4">El proceso es simple y seguro:</p>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="la la-check-circle text-success mr-2"></i>
                            Regístrate con tus datos y correo institucional
                        </li>
                        <li class="mb-3">
                            <i class="la la-check-circle text-success mr-2"></i>
                            Solicita la publicación que necesites
                        </li>
                        <li class="mb-3">
                            <i class="la la-check-circle text-success mr-2"></i>
                            Acércate con tu carnet de identidad dentro de las 2 horas siguientes
                        </li>
                    </ul>
                    <a href="<?php echo site_url('home/proceso'); ?>" class="btn btn-lg btn-umss mt-3">
                        Ver proceso completo
                    </a>
                </div>
                <div class="col-md-6">
                <img src="<?php echo base_url(); ?>img/prestamo.jpg" height="50" alt="Proceso de préstamo" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>

  

    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-custom');
            if (window.scrollY > 50) {
                navbar.style.padding = '0.5rem 0';
                navbar.style.backgroundColor = 'rgba(0, 51, 102, 0.95) !important';
            } else {
                navbar.style.padding = '1rem 0';
                navbar.style.backgroundColor = 'var(--umss-blue) !important';
            }
        });
    </script>