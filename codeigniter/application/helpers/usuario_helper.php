<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('obtener_nombre_seguro')) {
    function obtener_nombre_seguro($nombres, $apellido) {
        if ($nombres && $apellido) {
            $inicial_nombre = mb_substr($nombres, 0, 1, 'UTF-8');
            return htmlspecialchars($inicial_nombre . '. ' . $apellido);
        }
        return 'Usuario';
    }
}