<?php
// En application/helpers/estados_helper.php

if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_badge_class')) {
    function get_badge_class($estado) {
        switch($estado) {
            case 'Disponible':
                return 'badge-success';
            case 'En consulta':
                return 'badge-warning';
            case 'En préstamo por ti':
                return 'badge-primary';
            case 'En Reserva':
                return 'badge-info';
            case 'En mantenimiento':
                return 'badge-danger';
            default:
                if (strpos($estado, 'Reservado por ti') !== false) {
                    return 'badge-info';
                }
                return 'badge-secondary';
        }
    }
}

if (!function_exists('get_estado_texto')) {
    function get_estado_texto($estado_codigo) {
        switch($estado_codigo) {
            case ESTADO_PUBLICACION_DISPONIBLE:
                return 'Disponible';
            case ESTADO_PUBLICACION_EN_CONSULTA:
                return 'En consulta';
            case ESTADO_PUBLICACION_EN_MANTENIMIENTO:
                return 'En mantenimiento';
            default:
                return 'Estado desconocido';
        }
    }
}

if (!function_exists('get_estado_icon')) {
    function get_estado_icon($estado) {
        switch($estado) {
            case 'Disponible':
                return 'mdi mdi-book-open-variant';
            case 'En consulta':
                return 'mdi mdi-book-clock';
            case 'En préstamo por ti':
                return 'mdi mdi-book-check';
            case 'En Reserva':
                return 'mdi mdi-book-lock';
            case 'En mantenimiento':
                return 'mdi mdi-book-cog';
            default:
                if (strpos($estado, 'Reservado por ti') !== false) {
                    return 'mdi mdi-book-clock';
                }
                return 'mdi mdi-book';
        }
    }
}