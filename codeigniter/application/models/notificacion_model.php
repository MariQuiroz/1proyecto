<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificacion_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        
            parent::__construct();
            $this->load->model('Notificacion_model');
        
    }

    public function crear_notificacion($idUsuario, $idPublicacion, $tipoNotificacion, $mensaje) {
        $data = array(
            'idUsuario' => $idUsuario,
            'idPublicacion' => $idPublicacion,
            'tipoNotificacion' => $tipoNotificacion,
            'mensaje' => $mensaje,
            'fechaEnvio' => date('Y-m-d H:i:s'),
            'leida' => FALSE
        );

        $this->db->trans_start();
        $this->db->insert('NOTIFICACION', $data);
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function obtener_notificaciones_usuario($idUsuario) {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->order_by('fechaEnvio', 'DESC');
        return $this->db->get('NOTIFICACION')->result();
    }

    public function marcar_como_leida($idNotificacion) {
        $this->db->where('idNotificacion', $idNotificacion);
        return $this->db->update('NOTIFICACION', array('leida' => TRUE));
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

    public function agregar_interes_publicacion($idUsuario, $idPublicacion) {
        $data = array(
            'idUsuario' => $idUsuario,
            'idPublicacion' => $idPublicacion,
            'fechaInteres' => date('Y-m-d H:i:s')
        );

        return $this->db->insert('INTERES_PUBLICACION', $data);
    }

    public function obtener_usuarios_interesados($idPublicacion) {
        $this->db->select('idUsuario');
        $this->db->where('idPublicacion', $idPublicacion);
        return $this->db->get('INTERES_PUBLICACION')->result();
    }
   

    public function obtener_ultimas_notificaciones($idUsuario, $limite = 5) {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->order_by('fechaEnvio', 'DESC');
        $this->db->limit($limite);
        return $this->db->get('NOTIFICACION')->result();
    }
    public function contar_notificaciones_no_leidas($idUsuario) {
        if (!$idUsuario) return 0;
        
        $this->db->where('idUsuario', $idUsuario);
        $this->db->where('leida', 0);
        return $this->db->count_all_results('NOTIFICACION');
    }
}