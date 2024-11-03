<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Confirmar Solicitud de Préstamo</h4>
                            
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline mr-2"></i>
                                Verifique los detalles antes de confirmar la solicitud. Una vez confirmada, será revisada por un encargado.
                            </div>
                            
                            <!-- Lista de publicaciones seleccionadas -->
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Portada</th>
                                            <th>Título</th>
                                            <th>Editorial</th>
                                            <th>Tipo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="publicacionesSeleccionadas">
                                        <tr data-id="<?php echo $publicacion->idPublicacion; ?>">
                                            <td>
                                                <?php if (!empty($publicacion->portada)): ?>
                                                    <img src="<?php echo base_url('uploads/portadas/' . $publicacion->portada); ?>" 
                                                         alt="Portada" class="img-thumbnail" style="max-width: 50px;">
                                                <?php else: ?>
                                                    <span class="text-muted">Sin portada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $publicacion->titulo; ?></td>
                                            <td><?php echo $publicacion->nombreEditorial; ?></td>
                                            <td><?php echo $publicacion->nombreTipo; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-publicacion">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Botón para añadir más publicaciones -->
                            <button type="button" class="btn btn-info mb-3" data-toggle="modal" data-target="#modalPublicaciones">
                                <i class="mdi mdi-plus"></i> Añadir Más Publicaciones
                            </button>

                            <!-- Botones de acción -->
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" id="btnConfirmar">
                                    <i class="mdi mdi-check-circle mr-1"></i>Confirmar Solicitud
                                </button>
                                <a href="<?php echo site_url('publicaciones'); ?>" class="btn btn-secondary">
                                    <i class="mdi mdi-close-circle mr-1"></i>Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para seleccionar más publicaciones -->
<div class="modal fade" id="modalPublicaciones" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Publicaciones Adicionales</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table" id="tablaPublicaciones">
                        <thead>
                            <tr>
                                <th>Portada</th>
                                <th>Título</th>
                                <th>Editorial</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se llenará vía AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let publicacionesSeleccionadas = new Set([<?php echo $publicacion->idPublicacion; ?>]);
    const MAX_PUBLICACIONES = 5;

    // Cargar publicaciones disponibles en el modal
    $('#modalPublicaciones').on('show.bs.modal', function() {
        $.ajax({
            url: '<?php echo site_url("publicaciones/obtener_disponibles_ajax"); ?>',
            method: 'GET',
            success: function(response) {
                let html = '';
                response.forEach(function(pub) {
                    if (!publicacionesSeleccionadas.has(pub.idPublicacion)) {
                        html += `
                            <tr>
                                <td>
                                    ${pub.portada ? 
                                        `<img src="<?php echo base_url('uploads/portadas/'); ?>/${pub.portada}" 
                                             alt="Portada" class="img-thumbnail" style="max-width: 50px;">` : 
                                        '<span class="text-muted">Sin portada</span>'}
                                </td>
                                <td>${pub.titulo}</td>
                                <td>${pub.nombreEditorial}</td>
                                <td>${pub.nombreTipo}</td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm add-publicacion" 
                                            data-id="${pub.idPublicacion}">
                                        <i class="mdi mdi-plus"></i> Añadir
                                    </button>
                                </td>
                            </tr>`;
                    }
                });
                $('#tablaPublicaciones tbody').html(html);
            }
        });
    });

    // Añadir publicación desde el modal
    $(document).on('click', '.add-publicacion', function() {
        if (publicacionesSeleccionadas.size >= MAX_PUBLICACIONES) {
            Swal.fire({
                icon: 'warning',
                title: 'Límite alcanzado',
                text: 'Solo puede solicitar hasta 5 publicaciones a la vez.'
            });
            return;
        }

        const idPublicacion = $(this).data('id');
        // Obtener la información de la fila actual
        const $row = $(this).closest('tr');
        const nuevaFila = `
            <tr data-id="${idPublicacion}">
                <td>${$row.find('td:eq(0)').html()}</td>
                <td>${$row.find('td:eq(1)').html()}</td>
                <td>${$row.find('td:eq(2)').html()}</td>
                <td>${$row.find('td:eq(3)').html()}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-publicacion">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </td>
            </tr>`;

        $('#publicacionesSeleccionadas').append(nuevaFila);
        publicacionesSeleccionadas.add(idPublicacion);
        $(this).closest('tr').remove();
        $('#modalPublicaciones').modal('hide');
    });

    // Eliminar publicación de la selección
    $(document).on('click', '.remove-publicacion', function() {
        const id = $(this).closest('tr').data('id');
        if (publicacionesSeleccionadas.size <= 1) {
            Swal.fire({
                icon: 'warning',
                title: 'No permitido',
                text: 'Debe mantener al menos una publicación en la solicitud.'
            });
            return;
        }
        publicacionesSeleccionadas.delete(id);
        $(this).closest('tr').remove();
    });

    // Confirmar solicitud
    $('#btnConfirmar').click(function() {
        Swal.fire({
            title: '¿Confirmar solicitud?',
            text: "Esta acción enviará la solicitud al encargado para su aprobación",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const publicaciones = Array.from(publicacionesSeleccionadas);
                $.ajax({
                    url: '<?php echo site_url("solicitudes/confirmar/" . $publicacion->idPublicacion); ?>',
                    method: 'POST',
                    data: { 
                        publicaciones: publicaciones,
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Solicitud enviada',
                                text: 'Su solicitud ha sido enviada exitosamente'
                            }).then(() => {
                                window.location.href = '<?php echo site_url("solicitudes/mis_solicitudes"); ?>';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Hubo un error al procesar su solicitud'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema de comunicación con el servidor'
                        });
                    }
                });
            }
        });
    });
});
</script>