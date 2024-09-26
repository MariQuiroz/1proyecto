<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tipo_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function obtener_tipos() {
        $this->db->where('estado', 1);
        $query = $this->db->get('TIPO');
        return $query->result();
    }

    public function obtener_tipo($idTipo) {
        $this->db->where('idTipo', $idTipo);
        $query = $this->db->get('TIPO');
        return $query->row();
    }

    public function agregar_tipo($data) {
        return $this->db->insert('TIPO', $data);
    }

    public function actualizar_tipo($idTipo, $data) {
        $this->db->where('idTipo', $idTipo);
        return $this->db->update('TIPO', $data);
    }

    public function eliminar_tipo($idTipo, $data) {
        $this->db->where('idTipo', $idTipo);
        return $this->db->update('TIPO', $data);
    }
}