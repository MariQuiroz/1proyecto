<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Lista de Publicaciones</h4>
                            <p class="text-muted font-13 mb-4">
                                Aquí se muestran todas las publicaciones de la hemeroteca, con su estado actual.
                            </p>

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

                            <!-- Leyenda de estados -->
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="float-right">
                                            <span class="mr-3"><i class="mdi mdi-circle text-success"></i> Disponible</span>
                                            <span class="mr-3"><i class="mdi mdi-circle text-warning"></i> En consulta</span>
                                            <span class="mr-3"><i class="mdi mdi-circle text-danger"></i> En mantenimiento</span>
                                            <span class="mr-3"><i class="mdi mdi-circle text-info"></i> Reservada</span>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if ($this->session->userdata('rol') == 'administrador'): ?>
                                <a href="<?php echo site_url('publicaciones/agregar'); ?>" class="btn btn-primary mb-3">
                                    <i class="mdi mdi-plus"></i> Agregar Nueva Publicación
                                </a>
                            <?php endif; ?>

                            <!-- Resumen de publicaciones seleccionadas para lectores -->
                            <?php 
                            $publicaciones_seleccionadas = $this->session->userdata('publicaciones_seleccionadas') ?: array();
                            if ($this->session->userdata('rol') == 'lector' && !empty($publicaciones_seleccionadas)): 
                            ?>
                                <div class="alert alert-info mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="m-0">
                                            <i class="mdi mdi-information-outline mr-2"></i>
                                            Publicaciones seleccionadas: <?php echo count($publicaciones_seleccionadas); ?>/5
                                        </h5>
                                        <div>
                                            <a href="<?php echo site_url('solicitudes/crear/0'); ?>" class="btn btn-info btn-sm">
                                                <i class="mdi mdi-eye"></i> Ver seleccionadas
                                            </a>
                                            <?php if (count($publicaciones_seleccionadas) > 0): ?>
                                                <a href="<?php echo site_url('solicitudes/confirmar'); ?>" class="btn btn-success btn-sm ml-2">
                                                    <i class="mdi mdi-check"></i> Confirmar solicitud
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>Portada</th>
                                        <th>Título</th>
                                        <th>Editorial</th>
                                        <th>Tipo</th>
                                        <th>Fecha de Publicación</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($publicaciones as $publicacion): 
                                        $ya_seleccionada = in_array($publicacion->idPublicacion, $publicaciones_seleccionadas);
                                        
                                        if (!($this->session->userdata('rol') == 'lector' && $ya_seleccionada)):
                                    ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($publicacion->portada) && file_exists(FCPATH . 'uploads/portadas/' . $publicacion->portada)): ?>
                                                    <img src="<?php echo base_url('uploads/portadas/' . $publicacion->portada); ?>" 
                                                         alt="Portada" 
                                                         class="img-thumbnail" 
                                                         style="max-width: 50px;">
                                                <?php else: ?>
                                                    <span class="text-muted">Sin portada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($publicacion->titulo); ?></td>
                                            <td><?php echo htmlspecialchars($publicacion->nombreEditorial); ?></td>
                                            <td><?php echo htmlspecialchars($publicacion->nombreTipo); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($publicacion->fechaPublicacion)); ?></td>
                                            <td>
                                                <?php 
                                                $badge_class = '';
                                                $estado_texto = '';
                                                $icon_class = '';

                                                if ($publicacion->es_mi_reserva == 1) {
                                                    $badge_class = 'badge-primary';
                                                    $estado_texto = 'Reservada por ti';
                                                    if ($publicacion->fechaExpiracionReserva) {
                                                        $tiempo_restante = strtotime($publicacion->fechaExpiracionReserva) - time();
                                                        if ($tiempo_restante > 0) {
                                                            $horas = floor($tiempo_restante / 3600);
                                                            $minutos = floor(($tiempo_restante % 3600) / 60);
                                                            
                                                            if ($horas > 0) {
                                                                $estado_texto .= ' (' . $horas . 'h ' . $minutos . 'm)';
                                                            } else {
                                                                $estado_texto .= ' (' . $minutos . 'm)';
                                                            }
                                                        }
                                                    }
                                                    $icon_class = 'mdi mdi-account-clock';
                                                } else{
                                                    switch (intval($publicacion->estado)) {
                                                        case ESTADO_PUBLICACION_DISPONIBLE:
                                                            $badge_class = 'badge-success';
                                                            $estado_texto = 'Disponible';
                                                            $icon_class = 'mdi mdi-book-open-variant';
                                                            break;
                                                        case ESTADO_PUBLICACION_EN_CONSULTA:
                                                            $badge_class = 'badge-warning';
                                                            $estado_texto = 'En Consulta';
                                                            $icon_class = 'mdi mdi-book-account';
                                                            break;
                                                        case ESTADO_PUBLICACION_EN_MANTENIMIENTO:
                                                            $badge_class = 'badge-danger';
                                                            $estado_texto = 'En Mantenimiento';
                                                            $icon_class = 'mdi mdi-wrench';
                                                            break;
                                                        case ESTADO_PUBLICACION_RESERVADA:
                                                            $badge_class = 'badge-info';
                                                            $estado_texto = 'Reservada';
                                                            $icon_class = 'mdi mdi-calendar-clock';
                                                            break;
                                                        case ESTADO_PUBLICACION_ELIMINADO:
                                                            $badge_class = 'badge-secondary';
                                                            $estado_texto = 'Eliminada';
                                                            $icon_class = 'mdi mdi-delete';
                                                            break;
                                                        default:
                                                            $badge_class = 'badge-secondary';
                                                            $estado_texto = 'Estado Desconocido';
                                                            $icon_class = 'mdi mdi-help-circle';
                                                    }
                                                }
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <i class="<?php echo $icon_class; ?> mr-1"></i>
                                                    <?php echo $estado_texto; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo site_url('publicaciones/ver/'.$publicacion->idPublicacion); ?>" 
                                                   class="btn btn-info btn-sm" 
                                                   title="Ver detalles">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>

                                                <?php if ($this->session->userdata('rol') == 'administrador'): ?>
                                                    
                                                    <a href="<?php echo site_url('publicaciones/modificar/'.$publicacion->idPublicacion); ?>" 
                                                       class="btn btn-primary btn-sm" 
                                                       title="Editar">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>

                                                    <?php if (intval($publicacion->estado) === ESTADO_PUBLICACION_DISPONIBLE): ?>
                                                        <div class="btn-group">
                                                                <a href="<?php echo site_url('publicaciones/eliminar/'.$publicacion->idPublicacion); ?>"
                                                                class="btn btn-danger btn-sm" 
                                                                onclick="return confirm('¿Está seguro de eliminar esta publicación?');">
                                                                    <i class="fe-trash-2"></i>
                                                                </a>
                                                        </div>
                                                    <?php endif; ?>

                                                <?php endif; ?>
                                                <?php if ($this->session->userdata('rol') == 'lector'): ?>
    <?php if (intval($publicacion->estado) === ESTADO_PUBLICACION_DISPONIBLE): ?>
        <!-- Solicitar préstamo -->
        <a href="<?php echo site_url('solicitudes/crear/'.$publicacion->idPublicacion); ?>"
            class="btn btn-success btn-sm"
            title="Solicitar préstamo">
            <i class="mdi mdi-book-open-page-variant"></i> Solicitar
        </a>
    <?php elseif (intval($publicacion->estado) === ESTADO_PUBLICACION_EN_CONSULTA || 
                 intval($publicacion->estado) === ESTADO_PUBLICACION_RESERVADA): ?>
        <?php if ($publicacion->es_mi_consulta == 1): ?>
            <!-- Publicación en consulta por el usuario actual -->
            <button class="btn btn-info btn-sm" disabled>
                <i class="mdi mdi-book-account"></i> En tu poder (Consulta)
            </button>
        <?php elseif ($publicacion->es_mi_reserva == 1): ?>
            <!-- Publicación reservada por el usuario actual -->
            <button class="btn btn-primary btn-sm" disabled>
                <i class="mdi mdi-bookmark"></i> Reservada por ti
            </button>
        <?php else: ?>
            <?php 
            $interes_existente = $this->Notificacion_model->obtener_estado_interes(
                $this->session->userdata('idUsuario'),
                $publicacion->idPublicacion
            );
            ?>
            
            <?php if (!$interes_existente || $interes_existente->estado == ESTADO_INTERES_NOTIFICADO): ?>
                <!-- Notificar interés -->
                <a href="<?php echo site_url('notificaciones/agregar_interes_simple/'.$publicacion->idPublicacion); ?>"
                    class="btn btn-warning btn-sm">
                    <i class="mdi mdi-bell"></i> Notificarme cuando esté disponible
                </a>
            <?php elseif ($interes_existente->estado == ESTADO_INTERES_SOLICITADO): ?>
                <!-- Cancelar interés -->
                <div class="btn-group">
                    <button class="btn btn-secondary btn-sm" disabled>
                        <i class="mdi mdi-bell-check"></i> Notificación Activa
                    </button>
                    <a href="<?php echo site_url('notificaciones/cancelar_interes/'.$publicacion->idPublicacion); ?>"
                        class="btn btn-secondary btn-sm"
                        title="Cancelar notificación">
                        <i class="mdi mdi-bell-off"></i>
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>     </td>
                                        </tr>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function modificarPreferencias(idPublicacion) {
    // Abrir modal de preferencias existente
    $(`#modalNotificacion${idPublicacion}`).modal('show');
}

function cancelarNotificacion(idPublicacion) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Ya no recibirás notificaciones sobre esta publicación",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No, mantener'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `<?php echo site_url('notificaciones/cancelar_interes/'); ?>${idPublicacion}`;
        }
    });
}

// Validación del formulario
$(document).ready(function() {
    $('form[id^="formNotificacion"]').on('submit', function(e) {
        const sistemaChecked = $(this).find('input[name="notificar_sistema"]').is(':checked');
        const emailChecked = $(this).find('input[name="notificar_email"]').is(':checked');
        
        if (!sistemaChecked && !emailChecked) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debes seleccionar al menos un método de notificación'
            });
        }
    });
});
</script>