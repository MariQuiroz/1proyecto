<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificacion_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        
    }

    public function obtener_notificaciones_usuario($idUsuario) {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->order_by('fechaEnvio', 'DESC');
        return $this->db->get('NOTIFICACION')->result();
    }


    /*public function crear_notificacion($idUsuario, $idPublicacion, $tipo, $mensaje) {
        $data = array(
            'idUsuario' => $idUsuario,
            'idPublicacion' => $idPublicacion,
            'tipo' => $tipo,
            'mensaje' => $mensaje,
            'fechaEnvio' => date('Y-m-d H:i:s'),
            'leida' => FALSE
        );

        $this->db->trans_start();
        $this->db->insert('NOTIFICACION', $data);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Error al crear notificación: ' . $this->db->last_query());
            return false;
        } else {
            log_message('info', 'Notificación creada: ID=' . $insert_id);
            return true;
        }
    }
*/
/*public function crear_notificacion($idUsuario, $idPublicacion, $tipo, $mensaje) {
    log_message('debug', "\n==================================================");
    log_message('debug', 'INICIO crear_notificacion()');
    log_message('debug', '--------------------------------------------------');
    log_message('debug', 'Parámetros recibidos:');
    log_message('debug', 'idUsuario: ' . $idUsuario);
    log_message('debug', 'idPublicacion: ' . $idPublicacion);
    log_message('debug', 'tipo: ' . $tipo);
    log_message('debug', 'mensaje: ' . $mensaje);

    // Obtener stack trace para identificar origen de la llamada
    $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    log_message('debug', 'Llamada desde: ' . $stack[1]['class'] . '::' . $stack[1]['function'] . '()');
    
    // Verificar si ya existe una notificación similar
    $this->db->where('idUsuario', $idUsuario);
    $this->db->where('idPublicacion', $idPublicacion);
    $this->db->where('tipo', $tipo);
    $this->db->where('fechaEnvio >', date('Y-m-d H:i:s', strtotime('-1 minute')));
    $existe = $this->db->get('NOTIFICACION')->num_rows() > 0;
    
    log_message('debug', 'Verificación de duplicado: ' . ($existe ? 'Existe notificación similar' : 'No existe notificación similar'));
    
    if ($existe) {
        log_message('debug', 'Se evitó crear notificación duplicada');
        log_message('debug', "==================================================\n");
        return false;
    }

    $data = array(
        'idUsuario' => $idUsuario,
        'idPublicacion' => $idPublicacion,
        'tipo' => $tipo,
        'mensaje' => $mensaje,
        'fechaEnvio' => date('Y-m-d H:i:s'),
        'leida' => FALSE
    );

    $this->db->trans_start();
    $this->db->insert('NOTIFICACION', $data);
    $insert_id = $this->db->insert_id();
    
    log_message('debug', 'Query ejecutado: ' . $this->db->last_query());
    log_message('debug', 'ID de notificación generado: ' . $insert_id);
    
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        log_message('error', 'Error en transacción al crear notificación');
        log_message('error', $this->db->error());
        log_message('debug', "==================================================\n");
        return false;
    }

    log_message('info', 'Notificación creada exitosamente - ID: ' . $insert_id);
    log_message('debug', "==================================================\n");
    return true;
}*/
public function obtener_notificaciones($idUsuario, $rol) {
    log_message('debug', "\n==== INICIO obtener_notificaciones() ====");
    log_message('debug', 'Usuario ID: ' . $idUsuario . ', Rol: ' . $rol);
    
    $this->db->select('idNotificacion, idUsuario, mensaje, tipo, fechaEnvio, leida, idPublicacion');
    $this->db->from('NOTIFICACION');
    
    if ($rol == 'encargado') {
        $this->db->where('idUsuario', $idUsuario);
    } else {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->where('tipo !=', NOTIFICACION_NUEVA_SOLICITUD);
        log_message('debug', 'Usuario no encargado: excluyendo notificaciones de solicitudes');
    }
    
    $this->db->order_by('fechaEnvio', 'DESC');
    
    $notificaciones = $this->db->get()->result();
    log_message('debug', 'Query ejecutado: ' . $this->db->last_query());
    log_message('debug', 'Número de notificaciones encontradas: ' . count($notificaciones));
    
    log_message('debug', "==== FIN obtener_notificaciones() ====\n");
    return $notificaciones;
}

public function crear_notificacion($idUsuario, $idPublicacion, $tipo, $mensaje) {
    log_message('debug', "\n==== INICIO crear_notificacion() ====");
    log_message('debug', 'Parámetros:');
    log_message('debug', 'ID Usuario: ' . $idUsuario);
    log_message('debug', 'ID Publicación: ' . $idPublicacion);
    log_message('debug', 'Tipo: ' . $tipo);
    log_message('debug', 'Mensaje: ' . $mensaje);
    
    // Obtener información del usuario
    $usuario = $this->db->get_where('USUARIO', ['idUsuario' => $idUsuario])->row();
    if ($usuario) {
        log_message('debug', 'Rol del usuario destino: ' . $usuario->rol);
    }
    
    $data = array(
        'idUsuario' => $idUsuario,
        'idPublicacion' => $idPublicacion,
        'tipo' => $tipo,
        'mensaje' => $mensaje,
        'fechaEnvio' => date('Y-m-d H:i:s'),
        'leida' => FALSE
    );

    $this->db->trans_start();
    $this->db->insert('NOTIFICACION', $data);
    $insert_id = $this->db->insert_id();
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        log_message('error', 'Error al crear notificación');
        log_message('error', $this->db->error());
        return false;
    }

    log_message('info', 'Notificación creada exitosamente - ID: ' . $insert_id);
    log_message('debug', "==== FIN crear_notificacion() ====\n");
    return true;
}

public function contar_notificaciones_no_leidas($idUsuario, $rol) {
    $this->db->where('leida', 0);
    
    // Modificar para que solo los encargados reciban notificaciones de nuevas solicitudes
    if ($rol == 'encargado') {
        $this->db->where('idUsuario', $idUsuario)
                 ->where_in('tipo', [
                     NOTIFICACION_NUEVA_SOLICITUD,
                     // Otros tipos de notificaciones para encargados
                 ]);
    } else {
        $this->db->where('idUsuario', $idUsuario)
                 ->where_not_in('tipo', [NOTIFICACION_NUEVA_SOLICITUD]);
    }
    
    return $this->db->count_all_results('NOTIFICACION');
}

public function obtener_ultimas_notificaciones($idUsuario, $rol, $limite = 5) {
    $this->db->select('idNotificacion, idUsuario, mensaje, tipo, fechaEnvio, leida, idPublicacion');
    $this->db->from('NOTIFICACION');
    
    // Modificar para que solo los encargados reciban notificaciones de nuevas solicitudes
    if ($rol == 'encargado') {
        $this->db->where('idUsuario', $idUsuario)
                 ->where_in('tipo', [
                     NOTIFICACION_NUEVA_SOLICITUD,
                     // Otros tipos de notificaciones para encargados
                 ]);
    } else {
        $this->db->where('idUsuario', $idUsuario)
                 ->where_not_in('tipo', [NOTIFICACION_NUEVA_SOLICITUD]);
    }
    
    $this->db->order_by('fechaEnvio', 'DESC');
    $this->db->limit($limite);
    return $this->db->get()->result();
}


public function obtener_notificacion($idNotificacion) {
    return $this->db->get_where('NOTIFICACION', ['idNotificacion' => $idNotificacion])->row();
}

public function marcar_como_leida($idNotificacion) {
    $this->db->where('idNotificacion', $idNotificacion);
    return $this->db->update('NOTIFICACION', ['leida' => TRUE]);
}
public function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'año',
        'm' => 'mes',
        'w' => 'semana',
        'd' => 'día',
        'h' => 'hora',
        'i' => 'minuto',
        's' => 'segundo',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? 'hace ' . implode(', ', $string) : 'justo ahora';
}

/*public function obtener_ultimas_notificaciones($idUsuario, $rol, $limite = 5) {
    $this->db->select('idNotificacion, idUsuario, mensaje, tipo, fechaEnvio, leida, idPublicacion');
    $this->db->from('NOTIFICACION');
    
    if ($rol == 'administrador' || $rol == 'encargado') {
        $this->db->group_start()
            ->where('idUsuario', $idUsuario)
            ->or_where('tipo', NOTIFICACION_NUEVA_SOLICITUD)
        ->group_end();
    } else {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->where_not_in('tipo', [NOTIFICACION_NUEVA_SOLICITUD]);
    }
    
    $this->db->order_by('fechaEnvio', 'DESC');
    $this->db->limit($limite);
    return $this->db->get()->result();
}*/
public function guardar_preferencias($idUsuario, $preferencias) {
    $data = array(
        'idUsuario' => $idUsuario,
        'notificarDisponibilidad' => $preferencias['disponibilidad'],
        'notificarEmail' => $preferencias['email'],
        'notificarSistema' => $preferencias['sistema']
    );

    $this->db->replace('PREFERENCIAS_NOTIFICACION', $data);
}

public function obtener_preferencias($idUsuario) {
    return $this->db->get_where('PREFERENCIAS_NOTIFICACION', array('idUsuario' => $idUsuario))->row();
}



public function obtener_usuarios_interesados($idPublicacion) {
    $this->db->select('idUsuario');
    $this->db->where('idPublicacion', $idPublicacion);
    return $this->db->get('INTERES_PUBLICACION')->result();
}
public function agregar_interes_publicacion($idUsuario, $idPublicacion) {
    $this->db->where('idUsuario', $idUsuario);
    $this->db->where('idPublicacion', $idPublicacion);
    $query = $this->db->get('INTERES_PUBLICACION');

    if ($query->num_rows() > 0) {
        // Ya existe un interés, actualizar el estado
        $this->db->where('idUsuario', $idUsuario);
        $this->db->where('idPublicacion', $idPublicacion);
        return $this->db->update('INTERES_PUBLICACION', ['estado' => ESTADO_INTERES_SOLICITADO]);
    } else {
        // No existe, insertar nuevo interés
        $data = array(
            'idUsuario' => $idUsuario,
            'idPublicacion' => $idPublicacion,
            'fechaInteres' => date('Y-m-d H:i:s'),
            'estado' => ESTADO_INTERES_SOLICITADO
        );
        return $this->db->insert('INTERES_PUBLICACION', $data);
    }
}

public function obtener_estado_interes($idUsuario, $idPublicacion) {
    $this->db->where('idUsuario', $idUsuario);
    $this->db->where('idPublicacion', $idPublicacion);
    $query = $this->db->get('INTERES_PUBLICACION');

    if ($query->num_rows() > 0) {
        return $query->row()->estado;
    }
    return null;
}

public function marcar_todas_leidas($idUsuario, $rol) {
    $this->db->trans_start();

    if ($rol == 'administrador' || $rol == 'encargado') {
        $this->db->group_start()
            ->where('idUsuario', $idUsuario)
            ->or_where('tipo', NOTIFICACION_NUEVA_SOLICITUD)
        ->group_end();
    } else {
        $this->db->where('idUsuario', $idUsuario)
            ->where_not_in('tipo', [NOTIFICACION_NUEVA_SOLICITUD]);
    }

    $this->db->where('leida', FALSE);
    $this->db->update('NOTIFICACION', ['leida' => TRUE]);

    $this->db->trans_complete();
    
    return $this->db->trans_status();
}
public function eliminar_notificaciones_leidas($idUsuario, $rol) {
    $this->db->trans_start();

    // Construir condiciones según el rol
    if ($rol == 'administrador' || $rol == 'encargado') {
        $this->db->group_start()
            ->where('idUsuario', $idUsuario)
            ->or_where('tipo', NOTIFICACION_NUEVA_SOLICITUD)
        ->group_end();
    } else {
        $this->db->where('idUsuario', $idUsuario)
            ->where_not_in('tipo', [NOTIFICACION_NUEVA_SOLICITUD]);
    }

    // Solo eliminar notificaciones leídas
    $this->db->where('leida', TRUE);
    $this->db->delete('NOTIFICACION');

    $this->db->trans_complete();
    
    return $this->db->trans_status();
}

// Método para eliminar una notificación específica
public function eliminar_notificacion($idNotificacion, $idUsuario) {
    $this->db->trans_start();
    
    $this->db->where('idNotificacion', $idNotificacion);
    $this->db->where('idUsuario', $idUsuario);
    $this->db->delete('NOTIFICACION');
    
    $this->db->trans_complete();
    
    return $this->db->trans_status();
}

}