<!-- En application/views/prestamos/devueltos.php -->
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

                            <div class="table-responsive">
                                <table id="datatable-buttons" class="table table-striped dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
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
                                                <td><?php echo $prestamo->idPrestamo; ?></td>
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
                                                    $estado_class = '';
                                                    switch($prestamo->estadoDevolucion) {
                                                        case 'bueno':
                                                            $estado_class = 'badge-success';
                                                            break;
                                                        case 'dañado':
                                                            $estado_class = 'badge-warning';
                                                            break;
                                                        case 'perdido':
                                                            $estado_class = 'badge-danger';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $estado_class; ?>">
                                                        <?php echo strtoupper($prestamo->estadoDevolucion); ?>
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
                                                        
                                                        <a href="<?php echo site_url('prestamos/generar_ficha_manual/'.$prestamo->idPrestamo); ?>" 
                                                           class="btn btn-primary btn-sm" 
                                                           title="Generar ficha de devolución"
                                                           target="_blank">
                                                            <i class="mdi mdi-file-pdf"></i>
                                                        </a>
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

<!-- Agregar DataTables y otros scripts necesarios -->
<script>
$(document).ready(function() {
    // Inicializar DataTables
    var table = $('#datatable-buttons').DataTable({
        lengthChange: false,
        buttons: ['copy', 'excel', 'pdf', 'colvis'],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        },
        order: [[4, 'desc']], // Ordenar por fecha de préstamo descendente
        pageLength: 10
    });

    // Posicionar botones
    table.buttons().container()
        .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
});
</script>