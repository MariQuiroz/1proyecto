<!-- application/views/prestamos/plantilla_comprobante.php -->
<div style="text-align: center; margin-bottom: 20px;">
    <h1>U.M.S.S. BIBLIOTECAS - COMPROBANTE DE DEVOLUCIÓN</h1>
</div>

<table style="width: 100%; margin-bottom: 20px;">
    <tr>
        <td style="width: 30%; font-weight: bold;">Título:</td>
        <td><?php echo $titulo; ?></td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Editorial:</td>
        <td><?php echo $nombreEditorial; ?></td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Lector:</td>
        <td><?php echo $nombreLector . ' ' . $apellidoLector; ?></td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Fecha de préstamo:</td>
        <td><?php echo date('d/m/Y', strtotime($fechaPrestamo)); ?></td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Hora inicio:</td>
        <td><?php echo $horaInicio; ?></td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Hora devolución:</td>
        <td><?php echo $horaDevolucion; ?></td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Estado de devolución:</td>
        <td><?php echo ucfirst($estadoDevolucion); ?></td>
    </tr>
    <?php if(!empty($observaciones)): ?>
    <tr>
        <td style="font-weight: bold;">Observaciones:</td>
        <td><?php echo $observaciones; ?></td>
    </tr>
    <?php endif; ?>
    <tr>
        <td style="font-weight: bold;">Recibido por:</td>
        <td><?php echo $nombreEncargado . ' ' . $apellidoEncargado; ?></td>
    </tr>
</table>

<div style="margin-top: 50px; text-align: center;">
    <div style="border-top: 1px solid #000; width: 200px; margin: 0 auto; padding-top: 10px;">
        Firma del Lector
    </div>
</div>