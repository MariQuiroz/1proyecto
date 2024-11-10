<?php
error_reporting(E_ALL & ~E_WARNING); // Suprimir warnings
ini_set('display_errors', 0); // No mostrar errores

define('BASEPATH', TRUE);
require_once dirname(__FILE__) . '/../application/config/constants.php';

// Configurar zona horaria
date_default_timezone_set('America/La_Paz');

// Configurar el path del proyecto
$project_path = dirname(__FILE__) . '/..';
$log_path = $project_path . '/logs/cron';

// Función para hacer la petición HTTP
function ejecutar_tarea($url) {
    $token = 'tu_token_seguro'; // Deberías configurar esto en tus constants.php
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
$base_url = 'http://localhost/1proyecto/codeigniter';

// Ejecutar tareas
$tareas = [
    'verificar_reservas' => $base_url . '/cron/verificar_reservas',
    'ejecutar_tareas_programadas' => $base_url . '/cron/ejecutar_tareas_programadas'
];

foreach ($tareas as $nombre => $url) {
    $timestamp = date('Y-m-d H:i:s');
    echo "[$timestamp] Ejecutando $nombre\n";
    
    $resultado = ejecutar_tarea($url);
    
    echo "[$timestamp] Resultado: " . 
         ($resultado['codigo'] == 200 ? "OK" : "Error") . 
         " (HTTP " . $resultado['codigo'] . ")\n";
    echo "[$timestamp] Respuesta: " . $resultado['respuesta'] . "\n";
    echo "----------------------------------------\n";
}