<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Editorial_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function obtener_editoriales() {
        $this->db->where('estado', 1);
        $query = $this->db->get('EDITORIAL');
        return $query->result();
    }

    public function obtener_editorial($idEditorial) {
        $this->db->where('idEditorial', $idEditorial);
        $query = $this->db->get('EDITORIAL');
        return $query->row();
    }

    public function agregar_editorial($data) {
        return $this->db->insert('EDITORIAL', $data);
    }

    public function actualizar_editorial($idEditorial, $data) {
        $this->db->where('idEditorial', $idEditorial);
        return $this->db->update('EDITORIAL', $data);
    }

    public function eliminar_editorial($idEditorial, $data) {
        $this->db->where('idEditorial', $idEditorial);
        return $this->db->update('EDITORIAL', $data);
    }
}