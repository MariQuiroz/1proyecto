<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


// Estados para la tabla USUARIO
defined('ESTADO_USUARIO_ACTIVO') OR define('ESTADO_USUARIO_ACTIVO', 1);
defined('ESTADO_USUARIO_INACTIVO') OR define('ESTADO_USUARIO_INACTIVO', 0);

// Estados para la tabla PUBLICACION
defined('ESTADO_PUBLICACION_DISPONIBLE') OR define('ESTADO_PUBLICACION_DISPONIBLE', 1);
defined('ESTADO_PUBLICACION_EN_CONSULTA') OR define('ESTADO_PUBLICACION_EN_CONSULTA', 2);
defined('ESTADO_PUBLICACION_EN_MANTENIMIENTO') OR define('ESTADO_PUBLICACION_EN_MANTENIMIENTO', 3);

// Estados para la tabla SOLICITUD_PRESTAMO
defined('ESTADO_SOLICITUD_PENDIENTE') OR define('ESTADO_SOLICITUD_PENDIENTE', 1);
defined('ESTADO_SOLICITUD_APROBADA') OR define('ESTADO_SOLICITUD_APROBADA', 2);
defined('ESTADO_SOLICITUD_RECHAZADA') OR define('ESTADO_SOLICITUD_RECHAZADA', 3);
defined('ESTADO_SOLICITUD_FINALIZADA') OR define('ESTADO_SOLICITUD_FINALIZADA', 4);
// Estados adicionales para la tabla SOLICITUD_PRESTAMO
defined('ESTADO_SOLICITUD_APROBADA_PARCIAL') OR define('ESTADO_SOLICITUD_APROBADA_PARCIAL', 5);

// Estados para la tabla PRESTAMO

defined('ESTADO_PRESTAMO_ACTIVO') OR define('ESTADO_PRESTAMO_ACTIVO', 1);
defined('ESTADO_PRESTAMO_FINALIZADO') OR define('ESTADO_PRESTAMO_FINALIZADO', 2);


// Estados para devolución de préstamos
defined('ESTADO_DEVOLUCION_BUENO') OR define('ESTADO_DEVOLUCION_BUENO', 'bueno');
defined('ESTADO_DEVOLUCION_DAÑADO') OR define('ESTADO_DEVOLUCION_DAÑADO', 'dañado');
defined('ESTADO_DEVOLUCION_PERDIDO') OR define('ESTADO_DEVOLUCION_PERDIDO', 'perdido');

// Estados para la tabla NOTIFICACION
/// Tipos de notificaciones
defined('NOTIFICACION_SOLICITUD_PRESTAMO') OR define('NOTIFICACION_SOLICITUD_PRESTAMO', 1);
defined('NOTIFICACION_APROBACION_PRESTAMO') OR define('NOTIFICACION_APROBACION_PRESTAMO', 2);
defined('NOTIFICACION_RECHAZO_PRESTAMO') OR define('NOTIFICACION_RECHAZO_PRESTAMO', 3);
defined('NOTIFICACION_DEVOLUCION') OR define('NOTIFICACION_DEVOLUCION', 4);
defined('NOTIFICACION_DISPONIBILIDAD') OR define('NOTIFICACION_DISPONIBILIDAD', 5);
defined('NOTIFICACION_NUEVA_SOLICITUD') OR define('NOTIFICACION_NUEVA_SOLICITUD', 6);
defined('NOTIFICACION_VENCIMIENTO') OR define('NOTIFICACION_VENCIMIENTO', 7);

// Estados para la tabla INTERES_PUBLICACION
defined('ESTADO_INTERES_PENDIENTE')   OR define('ESTADO_INTERES_PENDIENTE', 1);
defined('ESTADO_INTERES_SOLICITADO')  OR define('ESTADO_INTERES_SOLICITADO', 2);
defined('ESTADO_INTERES_NOTIFICADO')  OR define('ESTADO_INTERES_NOTIFICADO', 3);

// Estados para la devolución de publicaciones
defined('ESTADO_DEVOLUCION_BUENO') OR define('ESTADO_DEVOLUCION_BUENO', 1);
defined('ESTADO_DEVOLUCION_DAÑADO') OR define('ESTADO_DEVOLUCION_DAÑADO', 2);
defined('ESTADO_DEVOLUCION_PERDIDO') OR define('ESTADO_DEVOLUCION_PERDIDO', 3);