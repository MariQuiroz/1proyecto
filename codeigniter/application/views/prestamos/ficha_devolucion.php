<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Devolución</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .ficha { width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #000; }
        .titulo { text-align: center; font-size: 18px; font-weight: bold; }
        .datos { margin-top: 20px; }
        .datos p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="ficha">
        <div class="titulo">U.M.S.S. BIBLIOTECAS - COMPROBANTE DE DEVOLUCIÓN</div>
        <div class="datos">
            <p><strong>Título de la publicación:</strong> <?php echo $titulo; ?></p>
            <p><strong>Lector:</strong> <?php echo $nombreLector . ' ' . $apellidoLector; ?></p>
            <p><strong>Fecha de préstamo:</strong> <?php echo $fechaPrestamo; ?></p>
            <p><strong>Fecha de devolución:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>Encargado que recibió:</strong> <?php echo $nombreEncargado . ' ' . $apellidoEncargado; ?></p>
        </div>
    </div>
</body>
</html>