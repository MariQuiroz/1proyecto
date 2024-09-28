<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificacion_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function crear_notificacion($data) {
        $this->db->insert('NOTIFICACION', $data);
        return $this->db->insert_id();
    }

    public function get_notificaciones_usuario($idUsuario) {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->where('leida', 0);
        return $this->db->get('NOTIFICACION')->result();
    }

    public function marcar_como_leida($idNotificacion) {
        $this->db->where('idNotificacion', $idNotificacion);
        return $this->db->update('NOTIFICACION', ['leida' => 1]);
    }

    public function registrar_interes($idUsuario, $idPublicacion) {
        $data = array(
            'idUsuario' => $idUsuario,
            'idPublicacion' => $idPublicacion,
            'mensaje' => 'Interés registrado en esta publicación.',
            'fechaNotificacion' => date('Y-m-d H:i:s')
        );
        return $this->crear_notificacion($data);
    }

    public function get_usuarios_interesados($idPublicacion) {
        $this->db->select('DISTINCT idUsuario');
        $this->db->where('idPublicacion', $idPublicacion);
        $this->db->where('mensaje', 'Interés registrado en esta publicación.');
        return $this->db->get('NOTIFICACION')->result();
    }
}