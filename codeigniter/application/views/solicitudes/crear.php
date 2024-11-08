<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="header-title m-0">Solicitud de Préstamo</h4>
                                <a href="<?php echo site_url('publicaciones'); ?>" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left mr-1"></i>Volver al catálogo
                                </a>
                            </div>
                            
                            <?php if ($this->session->flashdata('mensaje')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('mensaje'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('error'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Mensaje informativo mejorado -->
                            <div class="alert alert-info">
                                <div class="d-flex align-items-start">
                                    <i class="mdi mdi-information-outline h3 mr-3 mb-0"></i>
                                    <div>
                                        <h5 class="mt-0">Información importante</h5>
                                        <ul class="mb-0 pl-3">
                                            <li>Puede seleccionar hasta 5 publicaciones para su solicitud.</li>
                                            <li>Tiene 2 horas para confirmar su solicitud antes de que se liberen las publicaciones.</li>
                                            <li>Las publicaciones seleccionadas quedarán reservadas exclusivamente para usted durante este tiempo.</li>
                                            <li>Puede cancelar la solicitud en cualquier momento antes de confirmarla.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (!empty($publicaciones)): ?>
                                <!-- Contador global para la reserva -->
                                <?php
                                $tiempo_menor = PHP_INT_MAX;
                                foreach ($publicaciones as $pub) {
                                    if (isset($pub->tiempo_restante_segundos) && $pub->tiempo_restante_segundos < $tiempo_menor) {
                                        $tiempo_menor = $pub->tiempo_restante_segundos;
                                    }
                                }
                                if ($tiempo_menor < PHP_INT_MAX):
                                ?>
                                    <div class="alert alert-warning">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-clock-alert h4 mb-0 mr-2"></i>
                                            <div>
                                                <strong>Tiempo restante para confirmar: </strong>
                                                <span id="countdown-timer" class="font-weight-bold">
                                                    <?php echo $this->publicacion_model->formatear_tiempo_restante($tiempo_menor); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="row mb-4">
                                    <?php foreach ($publicaciones as $publicacion): ?>
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100 shadow-sm">
                                                <!-- Imagen de portada con badge de tiempo -->
                                                <div class="position-relative">
                                                    <?php if ($publicacion->portada && file_exists(FCPATH . 'uploads/portadas/' . $publicacion->portada)): ?>
                                                        <img src="<?php echo base_url('uploads/portadas/' . $publicacion->portada); ?>" 
                                                             class="card-img-top" 
                                                             alt="Portada"
                                                             style="height: 250px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-light text-center py-5">
                                                            <i class="mdi mdi-book-variant h1 text-muted"></i>
                                                            <p class="text-muted mb-0">Sin portada</p>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (isset($publicacion->tiempo_restante)): ?>
                                                        <div class="position-absolute" style="top: 10px; right: 10px;">
                                                            <span class="badge badge-warning">
                                                                <i class="mdi mdi-clock mr-1"></i>
                                                                <?php echo $publicacion->tiempo_restante; ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="card-body">
                                                    <h5 class="card-title text-truncate" title="<?php echo htmlspecialchars($publicacion->titulo); ?>">
                                                        <?php echo htmlspecialchars($publicacion->titulo); ?>
                                                    </h5>
                                                    <div class="card-text">
                                                        <table class="table table-sm table-borderless mb-0">
                                                            <tr>
                                                                <td width="35%"><strong>Editorial:</strong></td>
                                                                <td><?php echo htmlspecialchars($publicacion->nombreEditorial); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Tipo:</strong></td>
                                                                <td><?php echo htmlspecialchars($publicacion->nombreTipo); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Ubicación:</strong></td>
                                                                <td><?php echo htmlspecialchars($publicacion->ubicacionFisica); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Publicación:</strong></td>
                                                                <td><?php echo date('d/m/Y', strtotime($publicacion->fechaPublicacion)); ?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="card-footer bg-transparent">
                                                    <a href="<?php echo site_url('solicitudes/remover/' . $publicacion->idPublicacion); ?>" 
                                                       class="btn btn-danger btn-block"
                                                       onclick="return confirm('¿Está seguro de remover esta publicación de la solicitud?');">
                                                        <i class="fe-trash-2 mr-1"></i> Remover de la solicitud
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Botones de acción -->
                                <div class="text-center border-top pt-4">
                                    <div class="btn-group btn-group-lg">
                                        <?php if (count($publicaciones) < 5): ?>
                                            <a href="<?php echo site_url('publicaciones'); ?>" class="btn btn-info">
                                                <i class="mdi mdi-plus mr-1"></i>Añadir Más Publicaciones
                                            </a>
                                        <?php endif; ?>

                                        <a href="<?php echo site_url('solicitudes/confirmar'); ?>" 
                                           class="btn btn-primary"
                                           onclick="return confirm('¿Está seguro de confirmar la solicitud? Una vez confirmada, no podrá modificarla.');">
                                            <i class="mdi mdi-check-circle mr-1"></i>Confirmar Solicitud
                                        </a>

                                        <a href="<?php echo site_url('solicitudes/cancelar'); ?>" 
                                           class="btn btn-secondary"
                                           onclick="return confirm('¿Está seguro de cancelar la solicitud? Se perderán todas las publicaciones seleccionadas.');">
                                            <i class="mdi mdi-close-circle mr-1"></i>Cancelar
                                        </a>
                                    </div>
                                </div>

                            <?php else: ?>
                                <!-- Mensaje cuando no hay publicaciones seleccionadas -->
                                <div class="alert alert-warning">
                                    <div class="d-flex align-items-center">
                                        <i class="mdi mdi-alert h2 m-0 mr-2"></i>
                                        <div>
                                            <h5 class="mt-0">No hay publicaciones seleccionadas</h5>
                                            <p class="mb-0">
                                                Por favor, seleccione algunas publicaciones del 
                                                <a href="<?php echo site_url('publicaciones'); ?>" class="alert-link">
                                                    catálogo
                                                </a>.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para el contador regresivo -->
<?php if (isset($tiempo_menor) && $tiempo_menor < PHP_INT_MAX): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tiempoRestante = <?php echo $tiempo_menor; ?>;
    var countdownElement = document.getElementById('countdown-timer');
    
    function actualizarContador() {
        var horas = Math.floor(tiempoRestante / 3600);
        var minutos = Math.floor((tiempoRestante % 3600) / 60);
        var segundos = tiempoRestante % 60;
        
        var textoTiempo = [];
        if (horas > 0) textoTiempo.push(horas + ' hora' + (horas > 1 ? 's' : ''));
        if (minutos > 0) textoTiempo.push(minutos + ' minuto' + (minutos > 1 ? 's' : ''));
        if (segundos > 0) textoTiempo.push(segundos + ' segundo' + (segundos > 1 ? 's' : ''));
        
        countdownElement.textContent = textoTiempo.join(', ');
        
        if (tiempoRestante <= 0) {
            clearInterval(intervalo);
            alert('El tiempo de reserva ha expirado. La página se recargará.');
            window.location.reload();
        }
        
        tiempoRestante--;
    }
    
    var intervalo = setInterval(actualizarContador, 1000);
    actualizarContador();
});
</script>
<?php endif; ?>