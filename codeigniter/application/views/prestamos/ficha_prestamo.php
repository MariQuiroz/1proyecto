<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha de Préstamo</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .ficha { border: 1px solid #000; padding: 20px; max-width: 500px; margin: 0 auto; }
        .titulo { text-align: center; font-weight: bold; }
        .campo { margin-bottom: 10px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="ficha">
        <div class="titulo">U.M.S.S. BIBLIOTECAS EN SALA</div>
        <div class="campo">
            <span class="label">Signatura topográfica:</span> <?php echo $signatura_topografica; ?>
        </div>
        <div class="campo">
            <span class="label">AUTOR:</span> <!-- Agregar autor si está disponible -->
        </div>
        <div class="campo">
            <span class="label">TITULO:</span> <?php echo $titulo; ?>
        </div>
        <div class="campo">
            <span class="label">NOMBRE DEL LECTOR:</span> <!-- Agregar nombre del lector si está disponible -->
        </div>
        <div class="campo">
            <span class="label">C.I.:</span> <?php echo $carnet_lector; ?>
        </div>
        <div class="campo">
            <span class="label">Carrera:</span> <?php echo $profesion; ?>
        </div>
        <div class="campo">
            <span class="label">Domicilio:</span> <!-- Agregar domicilio si está disponible -->
        </div>
        <div class="campo">
            <span class="label">Lugar de Trabajo:</span> <!-- Agregar lugar de trabajo si está disponible -->
        </div>
        <div class="campo">
            <span class="label">Cochabamba,</span> <?php echo date('d \d\e F \d\e Y', strtotime($fecha_prestamo)); ?>
        </div>
        <div class="campo">
            <span class="label">Prestado por:</span> <?php echo $prestado_por; ?>
        </div>
        <div class="campo">
            <span class="label">Firma del lector:</span> _________________________
        </div>
        <div class="campo">
            <span class="label">Recibido por:</span> _________________________
        </div>
    </div>
    <script>
        window.print();
    </script>
</body>
</html>