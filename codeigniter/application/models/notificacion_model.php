<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificacion_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        
            parent::__construct();
            $this->load->model('Notificacion_model');
        
    }

    public function obtener_notificaciones_usuario($idUsuario) {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->order_by('fechaEnvio', 'DESC');
        return $this->db->get('NOTIFICACION')->result();
    }


    public function crear_notificacion($idUsuario, $idPublicacion, $tipo, $mensaje) {
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

    public function obtener_notificaciones($idUsuario, $rol) {
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
        return $this->db->get()->result();
    }

    public function contar_notificaciones_no_leidas($idUsuario, $rol) {
        $this->db->where('leida', 0);
        if ($rol == 'administrador' || $rol == 'encargado') {
            $this->db->group_start()
                ->where('idUsuario', $idUsuario)
                ->or_where('tipo', NOTIFICACION_NUEVA_SOLICITUD)
            ->group_end();
        } else {
            $this->db->where('idUsuario', $idUsuario);
            $this->db->where_not_in('tipo', [NOTIFICACION_NUEVA_SOLICITUD]);
        }
        return $this->db->count_all_results('NOTIFICACION');
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

public function obtener_ultimas_notificaciones($idUsuario, $rol, $limite = 5) {
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

}