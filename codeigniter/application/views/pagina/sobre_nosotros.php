<div class="about-section py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <h2 class="font-weight-bold mb-4">Sobre la Hemeroteca José Antonio Arze</h2>
                <p class="text-muted mb-4">La Hemeroteca José Antonio Arze de la Universidad Mayor de San Simón es un espacio dedicado a la preservación y difusión del conocimiento académico a través de publicaciones periódicas especializadas.</p>
                <p class="text-muted mb-4">Fundada en 1972, nuestra hemeroteca ha sido un pilar fundamental en el desarrollo académico de la Facultad de Ciencias Económicas, proporcionando acceso a recursos invaluables para estudiantes, docentes e investigadores.</p>
               
            </div>
            <div class="col-md-6">
            <div class="col-12">

        <div id="hemerotecaCarousel" class="carousel slide shadow-lg" data-ride="carousel">
            <!-- Indicadores -->
            <ol class="carousel-indicators">
                <li data-target="#hemerotecaCarousel" data-slide-to="0" class="active"></li>
                <li data-target="#hemerotecaCarousel" data-slide-to="1"></li>
                <li data-target="#hemerotecaCarousel" data-slide-to="2"></li>

            </ol>

            <!-- Slides -->
            <div class="carousel-inner rounded">
                <div class="carousel-item active">
                    <img src="<?php echo base_url(); ?>img/img1.jpg" class="d-block w-100" alt="Sala de Lectura">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Sala de Lectura Principal</h5>

                    </div>
                </div>
                <div class="carousel-item">
                    <img src="<?php echo base_url(); ?>img/img2.jpg" class="d-block w-100" alt="Estanterías">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Área de Colecciones</h5>

                    </div>
                </div>
                <div class="carousel-item">
                    <img src="<?php echo base_url(); ?>img/img3.jpg" class="d-block w-100" alt="Área de Estudio">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Espacios de Estudio</h5>
                    </div>
                </div>
                
                </div>
            </div>

            <!-- Controles -->
            <a class="carousel-control-prev" href="#hemerotecaCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Anterior</span>
            </a>
            <a class="carousel-control-next" href="#hemerotecaCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Siguiente</span>
            </a>
        </div>
    </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-4 mb-4">
                <div class="vision-card h-100 shadow-sm p-4">
                    <div class="text-center mb-3">
                        <i class="la la-eye la-3x text-primary"></i>
                    </div>
                    <h4 class="text-center font-weight-bold mb-3">Visión</h4>
                    <p class="text-center text-muted">Ser la principal fuente de recursos hemerográficos especializados en ciencias económicas de la región, facilitando el acceso al conocimiento para toda la comunidad académica.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="mission-card h-100 shadow-sm p-4">
                    <div class="text-center mb-3">
                        <i class="la la-bullseye la-3x text-primary"></i>
                    </div>
                    <h4 class="text-center font-weight-bold mb-3">Misión</h4>
                    <p class="text-center text-muted">Proporcionar acceso eficiente a recursos hemerográficos de calidad, apoyando la investigación y el desarrollo académico de nuestra comunidad universitaria.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="values-card h-100 shadow-sm p-4">
                    <div class="text-center mb-3">
                        <i class="la la-star la-3x text-primary"></i>
                    </div>
                    <h4 class="text-center font-weight-bold mb-3">Valores</h4>
                    <p class="text-center text-muted">Compromiso con la excelencia académica, responsabilidad en la preservación del conocimiento y servicio orientado a la comunidad universitaria.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.carousel {
    margin-bottom: 4rem;
}

.carousel-inner {
    max-height: 500px;
}

.carousel-item {
    height: 500px;
}

.carousel-item img {
    object-fit: cover;
    height: 100%;
}

.carousel-caption {
    background: rgba(0, 0, 0, 0.5);
    padding: 20px;
    border-radius: 10px;
}

.carousel-caption h5 {
    font-size: 1.5rem;
    font-weight: bold;
    color: #ffffff; 
}

.carousel-indicators {
    bottom: 20px;
}

.carousel-indicators li {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin: 0 5px;
}

/* Mejoras para dispositivos móviles */
@media (max-width: 768px) {
    .carousel-inner {
        max-height: 300px;
    }
    
    .carousel-item {
        height: 300px;
    }
    
    .carousel-caption {
        padding: 10px;
    }
    
    .carousel-caption h5 {
        font-size: 1.2rem;
    }
    
    .carousel-caption p {
        font-size: 0.9rem;
    }
}
</style>

<!-- Asegúrate de que este script esté después de cargar Bootstrap -->
<script>
$(document).ready(function(){
    // Inicializar el carrusel
    $('#hemerotecaCarousel').carousel({
        interval: 5000, // Cambia de imagen cada 5 segundos
        pause: "hover" // Pausa al pasar el mouse
    });
    
    // Habilitar gestos táctiles para dispositivos móviles
    $("#hemerotecaCarousel").on("touchstart", function(event){
        const xClick = event.originalEvent.touches[0].pageX;
        $(this).one("touchmove", function(event){
            const xMove = event.originalEvent.touches[0].pageX;
            const sensitivityInPx = 5;
            
            if(Math.floor(xClick - xMove) > sensitivityInPx){
                $(this).carousel('next');
            }
            else if(Math.floor(xClick - xMove) < -sensitivityInPx){
                $(this).carousel('prev');
            }
        });
        $(this).on("touchend", function(){
            $(this).off("touchmove");
        });
    });
});
</script>