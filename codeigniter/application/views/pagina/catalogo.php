<style>
    .header-banner {
        background-color: #2c5282;
        color: white;
        padding: 20px;
        margin-bottom: 20px;
    }

    .header-banner h1 {
        font-size: 24px;
        margin: 0;
    }

    .publicacion-item {
        border: 1px solid #ddd;
        margin-bottom: 15px;
        padding: 15px;
        background: white;
    }

    .publicacion-header {
        background-color: #2c5282;
        color: white;
        padding: 5px 10px;
        margin: -15px -15px 15px -15px;
    }

    .publicacion-registro {
        font-size: 14px;
    }

    .publicacion-titulo {
        font-weight: bold;
        margin-bottom: 10px;
        color: #000;
    }

    .publicacion-autores {
        margin-bottom: 5px;
    }

    .publicacion-tipo {
        margin-bottom: 5px;
    }
    .publicacion-fecha {
        margin-bottom: 5px;
    }
    .publicacion-editorial {
        margin-bottom: 5px;
    }


    .publicacion-codigo {
        color: blue;
        margin-bottom: 5px;
    }

    .publicacion-info {
        color: #444;
    }

    .alert-login {
        background-color: #f8f9fa;
        border-left: 4px solid #2c5282;
        padding: 10px 15px;
        margin: 10px 0;
        color: #2c5282;
    }
 
  
    .header-umss {
        background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
        color: #ffffff; /* Aseguramos que el texto sea blanco */
        padding: 30px;
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .header-umss img {
        width: 120px;
        height: auto;
        margin-right: 30px;
        transition: transform 0.3s ease;
    }

    .header-umss img:hover {
        transform: scale(1.05);
    }
    
    .header-umss h1 {
        font-size: 24px;
        margin: 0;
        line-height: 1.4;
        font-weight: 600;
        font-family: Arial, sans-serif;
        color: #ffffff; /* Enfatizamos el color blanco específicamente para el h1 */
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); /* Sombra mejorada para legibilidad */
    }

    /* Estilos para la portada */
    .publicacion-portada {
        float: left;
        margin-right: 20px;
        margin-bottom: 15px;
        width: 120px;
    }

    .publicacion-portada img {
        width: 100%;
        height: auto;
        border: 1px solid #ddd;
    }

    .publicacion-contenido {
        overflow: hidden;

        /* Media query para dispositivos móviles */
        @media (max-width: 768px) {
        .header-umss {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .header-umss img {
            width: 100px;
            margin: 0 0 15px 0;
        }

        .header-umss h1 {
            font-size: 20px;
        }
    }
    .img-zoom {
    transition: transform 0.3s ease;
}

.img-zoom:hover {
    transform: scale(1.05);
}

.modal-body {
    padding: 20px;
    background-color: #f8f9fa;
}

#modalImage {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .modal-dialog {
        margin: 10px;
    }
}
}
</style>
<div class="container py-5">
<!-- Encabezado principal -->
<div class="header-umss">
    <img src="<?php echo base_url(); ?>img/libro.png" alt="logo" height="30"> 
    <h1>Sistema de automatización de Bibliotecas y Centro de<br>Documentación Universidad Mayor de San Simón</h1>
</div>

<!-- Alerta informativa -->
<div class="container mb-4">
    <div class="alert alert-info" role="alert">
        <i class="fe-info-circle mr-2"></i>
        Para solicitar préstamos de publicaciones, debe iniciar sesión primero. 
        <a href="<?php echo site_url('usuarios/index'); ?>" class="alert-link">Iniciar sesión aquí</a>
    </div>
</div>
</div>

<div class="container">
    <?php foreach ($publicaciones as $index => $publicacion): ?>
        <div class="publicacion-item">
            <div class="publicacion-header">
                <span class="publicacion-registro">
                    Registro: <?php echo str_pad($index + 1, 4, '0', STR_PAD_LEFT); ?> 
                    (<?php echo ($index + 1); ?> / <?php echo count($publicaciones); ?>)
                </span>
            </div>

            <div class="publicacion-portada">
                <?php if ($publicacion->portada): ?>
                    <img src="<?php echo base_url('uploads/portadas/'.$publicacion->portada); ?>" 
                        alt="Portada de <?php echo htmlspecialchars($publicacion->titulo); ?>"
                        class="img-zoom"
                        style="cursor: pointer;"
                        onclick="openImageModal(this.src, '<?php echo htmlspecialchars($publicacion->titulo); ?>')">
                <?php else: ?>
                    <img src="<?php echo base_url('assets/img/portada-default.jpg'); ?>" 
                        alt="Portada por defecto"
                        class="img-zoom"
                        style="cursor: pointer;"
                        onclick="openImageModal(this.src, 'Portada por defecto')">
                <?php endif; ?>
            </div>

            <div class="publicacion-contenido">
                <div class="publicacion-titulo">
                    <strong>Título:</strong> <?php echo htmlspecialchars($publicacion->titulo); ?>
                </div>
                
                <div class="publicacion-editorial">
                    <strong>Editorial:</strong> <?php echo htmlspecialchars($publicacion->nombreEditorial); ?>
                </div>

                <div class="publicacion-fecha">
                    <strong>Fecha de publicación:</strong> <?php echo date('d/m/Y', strtotime($publicacion->fechaPublicacion)); ?>
                </div>

                <div class="publicacion-tipo">
                    <strong>Tipo:</strong> <?php echo htmlspecialchars($publicacion->nombreTipo); ?>
                </div>

                <div class="publicacion-ubicacion">
                    <strong>Ubicación:</strong> Biblioteca José Antonio Arze (<?php echo htmlspecialchars($publicacion->nombreTipo); ?>)
                </div>

                <?php if (!$this->session->userdata('login')): ?>
                    <div class="alert-login">
                        <strong>Solicite la publicación iniciando sesión.</strong>
                    </div>
                <?php endif; ?>

                <div class="publicacion-info">
                    <strong>Para más información aproximarse por:</strong> 
                    Biblioteca Jose Antonio Arce de la Facultad de Ciencias Económicas (FCE)
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($publicaciones)): ?>
        <div class="alert alert-info text-center">
            No se encontraron publicaciones disponibles.
        </div>
    <?php endif; ?>
</div>
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Portada</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Portada ampliada" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</div>
<script>
function openImageModal(imageSrc, title) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModalLabel').textContent = 'Portada: ' + title;
    $('#imageModal').modal('show');
}
</script>




