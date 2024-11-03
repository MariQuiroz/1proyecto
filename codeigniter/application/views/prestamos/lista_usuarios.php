<!-- application/views/prestamos/lista_usuario.php -->
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="header-title">Préstamos de <?php echo $usuario->nombres . ' ' . $usuario->apellidoPaterno; ?></h4>
                                <a href="<?php echo site_url('prestamos/devolver_multiple?idUsuario=' . $usuario->idUsuario); ?>" 
                                   class="btn btn-primary">
                                    <i class="mdi mdi-book-multiple"></i> Devolución Múltiple
                                </a>
                            </div>

                            <?php if($this->session->flashdata('mensaje')): ?>
                                <div class="alert alert-success">
                                    <?php echo $this->session->flashdata('mensaje'); ?>
                                </div>
                            <?php endif; ?>

                            <?php if($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger">
                                    <?php echo $this->session->flashdata('error'); ?>
                                </div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th>Editorial</th>
                                            <th>Fecha Préstamo</th>
                                            <th>Hora Inicio</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($prestamos_activos)): ?>
                                            <?php foreach($prestamos_activos as $prestamo): ?>
                                                <tr>
                                                    <td><?php echo $prestamo->titulo; ?></td>
                                                    <td><?php echo $prestamo->nombreEditorial; ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($prestamo->fechaPrestamo)); ?></td>
                                                    <td><?php echo $prestamo->horaInicio; ?></td>
                                                    <td>
                                                        <span class="badge badge-info">Activo</span>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo site_url('prestamos/finalizar/'.$prestamo->idPrestamo); ?>" 
                                                           class="btn btn-success btn-sm">
                                                            <i class="mdi mdi-check"></i> Devolver
                                                        </a>
                                                        <a href="<?php echo site_url('prestamos/ver/'.$prestamo->idPrestamo); ?>" 
                                                           class="btn btn-info btn-sm">
                                                            <i class="mdi mdi-eye"></i> Ver
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No hay préstamos activos para este usuario</td>
                                            </tr>
                                        <?php endif; ?>
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