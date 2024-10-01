<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tipo_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function obtener_tipos($incluir_inactivos = false) {
        if (!$incluir_inactivos) {
            $this->db->where('estado', 1);
        }
        $this->db->order_by('nombreTipo', 'ASC');
        $query = $this->db->get('TIPO');
        return $query->result();
    }

    public function obtener_tipo($idTipo) {
        $this->db->where('idTipo', $idTipo);
        $query = $this->db->get('TIPO');
        return $query->row();
    }

    public function agregar_tipo($data) {
        $this->db->insert('TIPO', $data);
        return $this->db->insert_id();
    }

    public function actualizar_tipo($idTipo, $data) {
        $this->db->where('idTipo', $idTipo);
        return $this->db->update('TIPO', $data);
    }

    public function eliminar_tipo($idTipo, $data) {
        $this->db->where('idTipo', $idTipo);
        return $this->db->update('TIPO', $data);
    }

    public function existe_tipo($nombreTipo, $idTipo = null) {
        $this->db->where('nombreTipo', $nombreTipo);
        if ($idTipo !== null) {
            $this->db->where('idTipo !=', $idTipo);
        }
        $query = $this->db->get('TIPO');
        return $query->num_rows() > 0;
    }

    public function obtener_total_tipos($solo_activos = true) {
        if ($solo_activos) {
            $this->db->where('estado', 1);
        }
        return $this->db->count_all_results('TIPO');
    }

    public function obtener_tipos_paginados($limite, $offset, $incluir_inactivos = false) {
        if (!$incluir_inactivos) {
            $this->db->where('estado', 1);
        }
        $this->db->order_by('nombreTipo', 'ASC');
        $query = $this->db->get('TIPO', $limite, $offset);
        return $query->result();
    }
}