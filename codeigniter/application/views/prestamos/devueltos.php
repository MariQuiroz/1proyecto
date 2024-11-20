<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Préstamos Devueltos</h4>
                            
                            <?php if($this->session->flashdata('mensaje')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('mensaje'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $this->session->flashdata('error'); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if($this->session->flashdata('pdf_path')): ?>
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <strong>¡Ficha generada!</strong> 
                                    <a href="<?php echo $this->session->flashdata('pdf_path'); ?>" 
                                       target="_blank" 
                                       class="btn btn-info btn-sm ml-2">
                                        <i class="mdi mdi-file-pdf"></i> Descargar Ficha
                                    </a>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                       
                                            <th>Lector</th>
                                            <th>Carnet</th>
                                            <th>Publicación</th>
                                            <th>Fecha Préstamo</th>
                                            <th>Fecha Devolución</th>
                                            <th>Estado</th>
                                            <th>Encargado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($prestamos as $prestamo): ?>
                                            <tr>
                                               
                                                <td>
                                                    <?php echo htmlspecialchars($prestamo->nombre_lector . ' ' . 
                                                        $prestamo->apellido_lector); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($prestamo->carnet); ?></td>
                                                <td><?php echo htmlspecialchars($prestamo->titulo); ?></td>
                                                <td>
                                                    <?php echo date('d/m/Y H:i', strtotime($prestamo->fechaPrestamo)); ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if ($prestamo->horaDevolucion) {
                                                        echo date('d/m/Y H:i', strtotime($prestamo->fechaPrestamo . ' ' . $prestamo->horaDevolucion));
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $estado_class = 'badge-secondary';
                                                    $nombre_estado = '';
                                                    
                                                    switch ($prestamo->estadoDevolucion) {
                                                        case ESTADO_DEVOLUCION_BUENO:
                                                            $estado_class = 'badge-success';
                                                            $nombre_estado = 'BUENO';
                                                            break;
                                                        case ESTADO_DEVOLUCION_DAÑADO:
                                                            $estado_class = 'badge-warning';
                                                            $nombre_estado = 'DAÑADO';
                                                            break;
                                                        case ESTADO_DEVOLUCION_PERDIDO:
                                                            $estado_class = 'badge-danger';
                                                            $nombre_estado = 'PERDIDO';
                                                            break;
                                                        default:
                                                            $nombre_estado = 'NO DEFINIDO';
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $estado_class; ?>">
                                                        <?php echo $nombre_estado; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($prestamo->nombre_encargado_devolucion . ' ' . 
                                                        $prestamo->apellido_encargado_devolucion); ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="<?php echo site_url('prestamos/ver_detalle/'.$prestamo->idPrestamo); ?>" 
                                                           class="btn btn-info btn-sm" 
                                                           title="Ver detalles">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                        
                                                        <button type="button" 
                                                                class="btn btn-primary btn-sm dropdown-toggle" 
                                                                data-toggle="dropdown" 
                                                                aria-haspopup="true" 
                                                                aria-expanded="false">
                                                            <i class="mdi mdi-file-document"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" 
                                                            href="<?php echo site_url('prestamos/generar_ficha_manual/'.$prestamo->idPrestamo); ?>" 
                                                            target="_blank">
                                                                <i class="mdi mdi-file-pdf text-danger"></i> Generar PDF
                                                            </a>
                                                            <a class="dropdown-item" 
                                                            href="<?php echo site_url('prestamos/reenviar_ficha_email/'.$prestamo->idPrestamo); ?>"
                                                            onclick="return confirm('¿Desea enviar la ficha por correo electrónico?');">
                                                                <i class="mdi mdi-email text-info"></i> Enviar por Email
                                                            </a>
                                                            <?php if(isset($prestamo->pdf_path)): ?>
                                                            <a class="dropdown-item" 
                                                            href="<?php echo $prestamo->pdf_path; ?>" 
                                                            download>
                                                                <i class="mdi mdi-download text-success"></i> Descargar Última Ficha
                                                            </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#datatable-buttons').DataTable({
        lengthChange: false,
        buttons: ['copy', 'excel', 'pdf', 'colvis'],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        },
        order: [[4, 'desc']],
        pageLength: 10
    });

    table.buttons().container()
        .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');

    // Verificar si hay una ficha PDF recién generada
    if ($('.alert-info').length) {
        setTimeout(function() {
            $('.alert-info').fadeOut('slow');
        }, 10000); // Ocultar después de 10 segundos
    }
});
</script>