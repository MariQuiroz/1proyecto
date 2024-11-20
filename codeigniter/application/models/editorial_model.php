<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Editorial_model extends CI_Model {
    private $table = 'EDITORIAL';
    private $selected_fields = ['idEditorial', 'nombreEditorial', 'estado', 'fechaCreacion', 'fechaActualizacion', 'idUsuarioCreador'];

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function obtener_editoriales($incluir_inactivas = false) {
        $this->db->select($this->selected_fields);
        
        if (!$incluir_inactivas) {
            $this->db->where('estado', 1);
        }
        
        $this->db->order_by('nombreEditorial', 'ASC');
        return $this->db->get($this->table)->result();
    }

    public function obtener_editorial($idEditorial) {
        $this->db->select($this->selected_fields);
        $this->db->where('idEditorial', $idEditorial);
        return $this->db->get($this->table)->row();
    }

    public function agregar_editorial($data) {
        try {
            $this->db->trans_start();
            
            // Validar datos mínimos requeridos
            if (empty($data['nombreEditorial'])) {
                throw new Exception('El nombre de la editorial es requerido');
            }

            // Normalizar el nombre
            $data['nombreEditorial'] = mb_strtoupper(trim($data['nombreEditorial']), 'UTF-8');
            
            $this->db->insert($this->table, $data);
            $id = $this->db->insert_id();
            
            $this->db->trans_complete();
            
            return $this->db->trans_status() ? $id : false;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error al agregar editorial: ' . $e->getMessage());
            return false;
        }
    }

    public function verificar_existencia($nombreEditorial) {
        $this->db->select('COUNT(*) as total');
        $this->db->where('UPPER(nombreEditorial)', mb_strtoupper($nombreEditorial, 'UTF-8'));
        $this->db->where('estado', 1);
        return $this->db->get($this->table)->row()->total > 0;
    }

    public function actualizar_editorial($idEditorial, $data) {
        try {
            $this->db->trans_start();
            
            if (!$this->obtener_editorial($idEditorial)) {
                throw new Exception('Editorial no encontrada');
            }

            $this->db->where('idEditorial', $idEditorial);
            $this->db->update($this->table, $data);
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Error en la transacción de actualización');
            }
            
            return true;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error al actualizar editorial: ' . $e->getMessage());
            return false;
        }
    }

    public function eliminar_editorial($idEditorial, $data) {
        try {
            $this->db->trans_start();
            
            // Verificar si hay publicaciones asociadas
            $this->db->select('COUNT(*) as total');
            $this->db->from('PUBLICACION');
            $this->db->where('idEditorial', $idEditorial);
            $this->db->where('estado', 1);
            
            if ($this->db->get()->row()->total > 0) {
                throw new Exception('No se puede eliminar la editorial porque tiene publicaciones asociadas');
            }
            
            $this->db->where('idEditorial', $idEditorial);
            $this->db->update($this->table, $data);
            
            $this->db->trans_complete();
            
            return $this->db->trans_status();
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error al eliminar editorial: ' . $e->getMessage());
            return false;
        }
    }

    public function restaurar_editorial($idEditorial) {
        $data = array(
            'estado' => 1,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $this->session->userdata('idUsuario')
        );
        
        $this->db->where('idEditorial', $idEditorial);
        return $this->db->update($this->table, $data);
    }

    public function obtener_estadisticas() {
        $this->db->select([
            'COUNT(CASE WHEN e.estado = 1 THEN 1 END) as total_activas',
            'COUNT(CASE WHEN e.estado = 0 THEN 1 END) as total_inactivas',
            'COUNT(DISTINCT p.idPublicacion) as total_publicaciones'
        ]);
        $this->db->from($this->table . ' e');
        $this->db->join('PUBLICACION p', 'e.idEditorial = p.idEditorial', 'left');
        return $this->db->get()->row();
    }
}