<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Préstamo</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .ficha { width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #000; }
        .titulo { text-align: center; font-size: 18px; font-weight: bold; }
        .datos { margin-top: 20px; }
        .datos p { margin: 5px 0; }
        .firma { margin-top: 50px; text-align: center; }
        @media print {
            #botonImprimir, #botonVolver { display: none; }
        }
        </style>
</head>
<body>
    <div class="ficha">
        <div class="titulo">U.M.S.S. BIBLIOTECAS - EN SALA</div>
        <div class="datos">
            <p><strong>Título:</strong> <?php echo isset($titulo) ? $titulo : ''; ?></p>
            <p><strong>Fecha de Publicación:</strong> <?php echo isset($fechaPublicacion) ? $fechaPublicacion : ''; ?></p>
            <p><strong>Editorial:</strong> <?php echo isset($nombreEditorial) ? $nombreEditorial : ''; ?></p>
            <p><strong>Ubicación:</strong> <?php echo isset($ubicacionFisica) ? $ubicacionFisica : ''; ?></p>
            <p><strong>Carnet del Lector:</strong> <?php echo isset($carnet) ? $carnet : ''; ?></p>
            <p><strong>Profesión:</strong> <?php echo isset($profesion) ? $profesion : ''; ?></p>
            <p><strong>Fecha de Préstamo:</strong> <?php echo isset($fechaPrestamo) ? $fechaPrestamo : ''; ?></p>
            <p><strong>Prestado por:</strong> <?php echo (isset($nombreEncargado) && isset($apellidoEncargado)) ? $nombreEncargado . ' ' . $apellidoEncargado : ''; ?></p>
        </div>
        <div class="firma">
            <p>_________________________</p>
            <p>Firma del Lector</p>
        </div>
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <button id="botonImprimir" onclick="imprimirFicha()">Imprimir</button>
        <button id="botonVolver" onclick="volverPrestamosActivos()">Volver a Préstamos Activos</button>
    </div>
    <script>
        function imprimirFicha() {
            window.print();
        }

        function volverPrestamosActivos() {
            window.location.href = '<?php echo site_url('prestamos/activos'); ?>';
        }
    </script>
</body>
</html>