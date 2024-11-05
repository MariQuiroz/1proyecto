<!-- application/views/admin/dashboard.php -->
<div class="container-fluid"> 
    <h1 class="mt-4">Dashboard de Administración</h1>
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-users"></i> Total Usuarios</h5>
                    <p class="card-text"><?php echo $total_usuarios; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-book"></i> Total Publicaciones</h5>
                    <p class="card-text"><?php echo $total_publicaciones; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-exchange-alt"></i> Préstamos Activos</h5>
                    <p class="card-text"><?php echo $prestamos_activos; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-clock"></i> Reservas Pendientes</h5>
                    <p class="card-text"><?php echo $reservas_pendientes; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de actividades recientes -->
    <div class="row">
        <div class="col-md-12">
            <h4 class="mt-4">Actividades Recientes</h4>
            <ul class="list-group">
                <!-- Aquí puedes agregar dinámicamente las actividades recientes -->
                <li class="list-group-item">Usuario Juan Pérez registrado.</li>
                <li class="list-group-item">Nueva publicación: "La Historia de la Biblioteca".</li>
                <li class="list-group-item">Préstamo de "El Gran Gatsby" realizado.</li>
                <li class="list-group-item">Reserva de "1984" pendiente.</li>
            </ul>
        </div>
    </div>
</div>
