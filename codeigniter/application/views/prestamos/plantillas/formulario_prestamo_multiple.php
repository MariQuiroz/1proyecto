<!-- application/views/prestamos/plantillas/formulario_prestamo_multiple.php -->
<style>
    .tabla-prestamo {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .tabla-prestamo th, .tabla-prestamo td {
        border: 1px solid #000;
        padding: 5px;
    }
    .tabla-prestamo th {
        background-color: #f0f0f0;
    }
    .seccion {
        margin-bottom: 20px;
    }
    .firma {
        margin-top: 40px;
        border-top: 1px solid #000;
        width: 200px;
        text-align: center;
        float: left;
        margin-right: 50px;
    }
</style>

<div class="seccion">
    <h3 style="text-align: center;">FORMULARIO DE PRÉSTAMO EN SALA</h3>
    <p><strong>Fecha:</strong> <?php echo $fecha_actual; ?></p>
    <p><strong>Hora:</strong> <?php echo $hora_actual; ?></p>
</div>

<div class="seccion">
    <h4>DATOS DEL LECTOR</h4>
    <table style="width: 100%;">
        <tr>
            <td width="30%"><strong>Nombre completo:</strong></td>
            <td><?php echo $solicitud->nombres . ' ' . $solicitud->apellidoPaterno . ' ' . $solicitud->apellidoMaterno; ?></td>
        </tr>
        <tr>
            <td><strong>Carnet de Identidad:</strong></td>
            <td><?php echo $solicitud->carnet; ?></td>
        </tr>
        <tr>
            <td><strong>Profesión:</strong></td>
            <td><?php echo $solicitud->profesion; ?></td>
        </tr>
    </table>
</div>

<div class="seccion">
    <h4>PUBLICACIONES SOLICITADAS</h4>
    <table class="tabla-prestamo">
        <thead>
            <tr>
                <th>N°</th>
                <th>Título</th>
                <th>Editorial</th>
                <th>Ubicación</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($solicitud->publicaciones as $index => $pub): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo $pub->titulo; ?></td>
                <td><?php echo $pub->nombreEditorial; ?></td>
                <td><?php echo $pub->ubicacionFisica; ?></td>
                <td>EN SALA</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="seccion">
    <h4>CONDICIONES DE PRÉSTAMO</h4>
    <ol>
        <li>El préstamo es estrictamente para uso en sala de lectura.</li>
        <li>El material debe ser devuelto el mismo día.</li>
        <li>El lector es responsable del cuidado del material prestado.</li>
        <li>Cualquier daño será registrado y puede resultar en sanciones.</li>
    </ol>
</div>

<div style="clear: both; margin-top: 60px;">
    <div class="firma">
        <p>Firma del Lector</p>
        <p><?php echo $solicitud->nombres . ' ' . $solicitud->apellidoPaterno; ?></p>
        <p>C.I.: <?php echo $solicitud->carnet; ?></p>
    </div>
    
    <div class="firma">
        <p>Firma del Encargado</p>
        <p><?php echo $this->session->userdata('nombres') . ' ' . $this->session->userdata('apellidoPaterno'); ?></p>
        <p>Biblioteca Central UMSS</p>
    </div>
</div>