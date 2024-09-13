<!-- application/views/errors/html/error_403.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Acceso Denegado</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3 text-center">
                <h1 class="display-1">403</h1>
                <h2>Acceso Denegado</h2>
                <p>Lo sentimos, no tienes permiso para acceder a esta página.</p>
                <a href="<?= base_url() ?>" class="btn btn-primary">Volver a la página principal</a>
            </div>
        </div>
    </div>
</body>
</html>