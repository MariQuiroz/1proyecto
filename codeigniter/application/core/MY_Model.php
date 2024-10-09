<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {

    protected $table;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    protected function _update($where, $data) {
        // Remover idUsuarioCreador si está presente
        unset($data['idUsuarioCreador']);

        $data['fechaActualizacion'] = date('Y-m-d H:i:s');

        $this->db->where($where);
        $resultado = $this->db->update($this->table, $data);

        // Agregar log para depuración
        log_message('debug', 'SQL Query: ' . $this->db->last_query());

        return $resultado;
    }

  

    protected function _insert($data)
    {
        $data['idUsuarioCreador'] = $this->session->userdata('idUsuario');
        $data['fechaCreacion'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    
}