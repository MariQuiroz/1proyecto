<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Editorial_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function obtener_editoriales($incluir_inactivas = false) {
        if (!$incluir_inactivas) {
            $this->db->where('estado', 1);
        }
        $this->db->order_by('nombreEditorial', 'ASC');
        $query = $this->db->get('EDITORIAL');
        return $query->result();
    }

    public function obtener_editorial($idEditorial) {
        $this->db->where('idEditorial', $idEditorial);
        $query = $this->db->get('EDITORIAL');
        return $query->row();
    }

    public function agregar_editorial($data) {
        $this->db->insert('EDITORIAL', $data);
        return $this->db->insert_id();
    }

    public function actualizar_editorial($idEditorial, $data) {
        $this->db->where('idEditorial', $idEditorial);
        return $this->db->update('EDITORIAL', $data);
    }

    public function eliminar_editorial($idEditorial, $data) {
        $this->db->where('idEditorial', $idEditorial);
        return $this->db->update('EDITORIAL', $data);
    }

    public function existe_editorial($nombreEditorial, $idEditorial = null) {
        $this->db->where('nombreEditorial', $nombreEditorial);
        if ($idEditorial !== null) {
            $this->db->where('idEditorial !=', $idEditorial);
        }
        $query = $this->db->get('EDITORIAL');
        return $query->num_rows() > 0;
    }

    public function obtener_total_editoriales($solo_activas = true) {
        if ($solo_activas) {
            $this->db->where('estado', 1);
        }
        return $this->db->count_all_results('EDITORIAL');
    }

    public function obtener_editoriales_paginadas($limite, $offset, $incluir_inactivas = false) {
        if (!$incluir_inactivas) {
            $this->db->where('estado', 1);
        }
        $this->db->order_by('nombreEditorial', 'ASC');
        $query = $this->db->get('EDITORIAL', $limite, $offset);
        return $query->result();
    }
}




