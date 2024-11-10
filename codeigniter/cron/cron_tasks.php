
<?php
error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', 0);

// Configurar zona horaria
date_default_timezone_set('America/La_Paz');

// Configurar el path del proyecto
$project_path = dirname(__FILE__) . '/..';
$log_path = $project_path . '/logs/cron';

// Crear directorio de logs si no existe
if (!file_exists($log_path)) {
    mkdir($log_path, 0777, true);
}

// Función para hacer la petición HTTP
function ejecutar_tarea($url) {
    $token = 'tu_token_seguro';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '?token=' . $token);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resultado = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'codigo' => $http_code,
        'respuesta' => $resultado
    ];
}

// URL base de tu aplicación local
$base_url = 'http://localhost/1proyecto/codeigniter/index.php';

// Ejecutar tareas
$tareas = [
    'verificar_reservas' => $base_url . '/cron/verificar_reservas',
    'ejecutar_tareas_programadas' => $base_url . '/cron/ejecutar_tareas_programadas'
];

// Registrar inicio de ejecución
$log_file = $log_path . '/cron.log';
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Iniciando tareas programadas\n", FILE_APPEND);

foreach ($tareas as $nombre => $url) {
    $timestamp = date('Y-m-d H:i:s');
    echo "[$timestamp] Ejecutando $nombre\n";
    file_put_contents($log_file, "[$timestamp] Ejecutando $nombre\n", FILE_APPEND);
    
    $resultado = ejecutar_tarea($url);
    
    $mensaje = "[$timestamp] Resultado: " . 
               ($resultado['codigo'] == 200 ? "OK" : "Error") . 
               " (HTTP " . $resultado['codigo'] . ")\n";
    echo $mensaje;
    file_put_contents($log_file, $mensaje, FILE_APPEND);
    
    echo "[$timestamp] Respuesta: " . $resultado['respuesta'] . "\n";
    file_put_contents($log_file, "[$timestamp] Respuesta: " . $resultado['respuesta'] . "\n", FILE_APPEND);
    echo "----------------------------------------\n";
    file_put_contents($log_file, "----------------------------------------\n", FILE_APPEND);
}