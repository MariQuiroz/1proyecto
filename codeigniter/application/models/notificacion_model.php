<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificacion_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        
    }

    public function obtener_notificaciones_usuario($idUsuario) {
        $this->db->select('
            idNotificacion,
            idUsuario,
            mensaje,
            tipo,
            fechaEnvio,
            leida
        ');
        $this->db->from('NOTIFICACION');
        $this->db->where('idUsuario', $idUsuario);
        $this->db->order_by('fechaEnvio', 'DESC');
        return $this->db->get()->result();
    }


    public function obtener_notificaciones($idUsuario, $rol) {
        $this->db->select('
            idNotificacion,
            idUsuario,
            tipo,
            mensaje,
            leida,
            fechaEnvio
        ');
        $this->db->from('NOTIFICACION');
        
        if ($rol == 'encargado') {
            $this->db->where('idUsuario', $idUsuario);
        } else {
            $this->db->where([
                'idUsuario' => $idUsuario,
                'tipo !=' => NOTIFICACION_NUEVA_SOLICITUD
            ]);
        }
        
        $this->db->order_by('fechaEnvio', 'DESC');
        return $this->db->get()->result();
    }
    

    public function crear_notificacion($idUsuario, $idPublicacion, $tipo, $mensaje) {
        $this->db->trans_start();
        
        try {
            $data = [
                'idUsuario' => $idUsuario,
                'tipo' => $tipo,
                'mensaje' => $mensaje,
                'fechaEnvio' => date('Y-m-d H:i:s'),
                'leida' => FALSE
            ];

            $this->db->insert('NOTIFICACION', $data);
            $insert_id = $this->db->insert_id();

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            log_message('error', 'Error al crear notificación: ' . $e->getMessage());
            $this->db->trans_rollback();
            return false;
        }
    }


    public function contar_notificaciones_no_leidas($idUsuario, $rol) {
        $this->db->where([
            'idUsuario' => $idUsuario,
            'leida' => 0
        ]);
        
        if ($rol == 'encargado') {
            $this->db->where_in('tipo', [
                NOTIFICACION_NUEVA_SOLICITUD,
                NOTIFICACION_APROBACION_PRESTAMO,
                NOTIFICACION_DEVOLUCION,
                NOTIFICACION_CANCELACION_SOLICITUD,
                NOTIFICACION_SOLICITUD_EXPIRADA
            ]);
        } else {
            $this->db->where('tipo !=', NOTIFICACION_NUEVA_SOLICITUD);
        }
        
        return $this->db->count_all_results('NOTIFICACION');
    }


    public function obtener_ultimas_notificaciones($idUsuario, $rol, $limite = 5) {
        $this->db->select('
            idNotificacion,
            idUsuario,
            tipo,
            mensaje,
            leida,
            fechaEnvio
        ');
        $this->db->from('NOTIFICACION');
        $this->db->where('idUsuario', $idUsuario);
        
        if ($rol != 'encargado') {
            $this->db->where('tipo !=', NOTIFICACION_NUEVA_SOLICITUD);
        }
        
        $this->db->order_by('fechaEnvio', 'DESC');
        $this->db->limit($limite);
        return $this->db->get()->result();
    }

    public function obtener_notificacion($idNotificacion) {
        $this->db->select('
            idNotificacion,
            idUsuario,
            tipo,
            mensaje,
            leida,
            fechaEnvio
        ');
        $this->db->from('NOTIFICACION');
        $this->db->where('idNotificacion', $idNotificacion);
        return $this->db->get()->row();
    }

public function marcar_como_leida($idNotificacion) {
    $this->db->trans_start();
    
    $this->db->where('idNotificacion', $idNotificacion);
    $this->db->update('NOTIFICACION', ['leida' => TRUE]);
    
    $this->db->trans_complete();
    return $this->db->trans_status();
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


public function obtener_estado_interes($idUsuario, $idPublicacion) {
    $this->db->select('
        ip.estado,
        ip.fechaInteres,
        p.titulo
    ');
    $this->db->from('INTERES_PUBLICACION ip');
    $this->db->join('PUBLICACION p', 'p.idPublicacion = ip.idPublicacion');
    $this->db->where([
        'ip.idUsuario' => $idUsuario,
        'ip.idPublicacion' => $idPublicacion
    ]);
    $query = $this->db->get();
    
    return $query->num_rows() > 0 ? $query->row() : null;
}

public function marcar_todas_leidas($idUsuario, $rol) {
    $this->db->trans_start();
    
    $this->db->where('idUsuario', $idUsuario);
    if ($rol != 'encargado') {
        $this->db->where('tipo !=', NOTIFICACION_NUEVA_SOLICITUD);
    }
    $this->db->where('leida', 0);
    
    $this->db->update('NOTIFICACION', ['leida' => 1]);
    
    $this->db->trans_complete();
    return $this->db->trans_status();
}



public function eliminar_notificaciones_leidas($idUsuario, $rol) {
    $this->db->trans_start();
    
    $this->db->where('idUsuario', $idUsuario);
    if ($rol != 'encargado') {
        $this->db->where('tipo !=', NOTIFICACION_NUEVA_SOLICITUD);
    }
    $this->db->where('leida', 1);
    
    $this->db->delete('NOTIFICACION');
    
    $this->db->trans_complete();
    return $this->db->trans_status();
}


public function validar_notificacion($idNotificacion, $idUsuario, $rol) {
    $this->db->select('tipo');
    $this->db->where('idNotificacion', $idNotificacion);
    $this->db->where('idUsuario', $idUsuario);
    $notificacion = $this->db->get('NOTIFICACION')->row();
    
    if (!$notificacion) return false;
    
    // Validar que el tipo de notificación corresponda al rol
    switch($rol) {
        case 'encargado':
            return in_array($notificacion->tipo, [
                NOTIFICACION_NUEVA_SOLICITUD,
                NOTIFICACION_APROBACION_PRESTAMO,
                NOTIFICACION_RECHAZO_PRESTAMO,
                NOTIFICACION_CANCELACION_SOLICITUD,
                NOTIFICACION_SOLICITUD_EXPIRADA
            ]);
        case 'lector':
            return in_array($notificacion->tipo, [
                NOTIFICACION_SOLICITUD_PRESTAMO,
                NOTIFICACION_APROBACION_PRESTAMO,
                NOTIFICACION_RECHAZO_PRESTAMO,
                NOTIFICACION_DEVOLUCION
            ]);
        case 'administrador':
            return in_array($notificacion->tipo, [
                NOTIFICACION_VENCIMIENTO
                // NO incluir NOTIFICACION_NUEVA_SOLICITUD
            ]);
        default:
            return false;
    }
}

// Método para eliminar una notificación específica
public function eliminar_notificacion($idNotificacion, $idUsuario) {
    $this->db->trans_start();
    
    $this->db->where([
        'idNotificacion' => $idNotificacion,
        'idUsuario' => $idUsuario
    ]);
    $this->db->delete('NOTIFICACION');
    
    $this->db->trans_complete();
    return $this->db->trans_status();
}
public function eliminar_interes_al_notificar($idUsuario, $idPublicacion) {
    $this->db->trans_start();
    
    try {
        // Eliminar el registro de interés
        $this->db->where([
            'idUsuario' => $idUsuario,
            'idPublicacion' => $idPublicacion
        ]);
        $this->db->delete('INTERES_PUBLICACION');
        
        // Registrar en el log del sistema
        log_message('info', "Interés eliminado al notificar - Usuario ID: {$idUsuario}, Publicación ID: {$idPublicacion}");
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    } catch (Exception $e) {
        log_message('error', 'Error al eliminar interés: ' . $e->getMessage());
        $this->db->trans_rollback();
        return false;
    }
}

public function crear_notificacion_disponibilidad($idUsuario, $idPublicacion, $mensaje) {
    $this->db->trans_start();
    
    try {
        // Crear la notificación
        $data = [
            'idUsuario' => $idUsuario,
            'tipo' => NOTIFICACION_DISPONIBILIDAD,
            'mensaje' => $mensaje,
            'fechaEnvio' => date('Y-m-d H:i:s'),
            'leida' => FALSE
        ];

        $this->db->insert('NOTIFICACION', $data);
        $idNotificacion = $this->db->insert_id();
        
        // Eliminar el interés ya que la publicación está disponible
        $this->eliminar_interes_al_notificar($idUsuario, $idPublicacion);
        
        $this->db->trans_complete();
        return $this->db->trans_status() ? $idNotificacion : false;
        
    } catch (Exception $e) {
        log_message('error', 'Error al crear notificación de disponibilidad: ' . $e->getMessage());
        $this->db->trans_rollback();
        return false;
    }
}
public function actualizar_preferencias($idUsuario, $preferencias) {
    $this->db->where('idUsuario', $idUsuario);
    $query = $this->db->get('PREFERENCIAS_NOTIFICACION');
    
    $data = array(
        'idUsuario' => $idUsuario,
        'notificarDisponibilidad' => $preferencias['notificarDisponibilidad'],
        'notificarEmail' => $preferencias['notificarEmail'],
        'notificarSistema' => $preferencias['notificarSistema'],
        'fechaActualizacion' => date('Y-m-d H:i:s')
    );
    
    if ($query->num_rows() > 0) {
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('PREFERENCIAS_NOTIFICACION', $data);
    } else {
        return $this->db->insert('PREFERENCIAS_NOTIFICACION', $data);
    }
}

public function actualizar_estado_interes($idUsuario, $idPublicacion, $nuevoEstado) {
    $this->db->where('idUsuario', $idUsuario);
    $this->db->where('idPublicacion', $idPublicacion);
    return $this->db->update('INTERES_PUBLICACION', [
        'estado' => $nuevoEstado,
        'fechaActualizacion' => date('Y-m-d H:i:s')
    ]);
}

public function eliminar_interes($idUsuario, $idPublicacion) {
    return $this->db->delete('INTERES_PUBLICACION', [
        'idUsuario' => $idUsuario,
        'idPublicacion' => $idPublicacion
    ]);
}

public function agregar_interes_publicacion($data) {
    return $this->db->insert('INTERES_PUBLICACION', $data);
}
}