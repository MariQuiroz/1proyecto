<!-- application/views/prestamos/plantillas/formulario_devolucion_multiple.php -->
<style>
    .tabla-devolucion {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .tabla-devolucion th, .tabla-devolucion td {
        border: 1px solid #000;
        padding: 5px;
    }
    .tabla-devolucion th {
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
    .estado-devolucion {
        margin: 10px 0;
    }
</style>

<div class="seccion">
    <h3 style="text-align: center;">FORMULARIO DE DEVOLUCIÓN</h3>
    <p><strong>Fecha de devolución:</strong> <?php echo $fecha_actual; ?></p>
    <p><strong>Hora de devolución:</strong> <?php echo $hora_actual; ?></p>
</div>

<div class="seccion">
    <h4>DATOS DEL LECTOR</h4>
    <table style="width: 100%;">
        <tr>
            <td width="30%"><strong>Nombre completo:</strong></td>
            <td><?php echo $prestamos[0]->nombres . ' ' . $prestamos[0]->apellidoPaterno; ?></td>
        </tr>
        <tr>
            <td><strong>Carnet de Identidad:</strong></td>
            <td><?php echo $prestamos[0]->carnet; ?></td>
        </tr>
    </table>
</div>

<div class="seccion">
    <h4>PUBLICACIONES DEVUELTAS</h4>
    <table class="tabla-devolucion">
        <thead>
            <tr>
                <th>N°</th>
                <th>Título</th>
                <th>Editorial</th>
                <th>Fecha/Hora Préstamo</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prestamos as $index => $prestamo): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo $prestamo->titulo; ?></td>
                <td><?php echo $prestamo->nombreEditorial; ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($prestamo->fechaPrestamo)); ?></td>
                <td>
                    □ Bueno
                    □ Dañado
                    □ Perdido
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="seccion">
    <h4>OBSERVACIONES</h4>
    <div style="border: 1px solid #000; padding: 10px; min-height: 80px;">
    </div>
</div>

<div style="clear: both; margin-top: 60px;">
    <div class="firma">
        <p>Firma del Lector</p>
        <p><?php echo $prestamos[0]->nombres . ' ' . $prestamos[0]->apellidoPaterno; ?></p>
        <p>C.I.: <?php echo $prestamos[0]->carnet; ?></p>
    </div>
    
    <div class="firma">
        <p>Firma del Encargado</p>
        <p><?php echo $this->session->userdata('nombres') . ' ' . $this->session->userdata('apellidoPaterno'); ?></p>
        <p>Biblioteca Central UMSS</p>
    </div>
</div>