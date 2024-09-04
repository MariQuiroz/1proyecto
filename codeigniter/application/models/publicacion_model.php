<?php
defined('BASEPATH') OR exit('No se permite acceso directo al script');
class Publicacion_model extends CI_Model {
    
 

    public function listar_publicaciones() {
        $this->db->where('estado', 1);
        return $this->db->get('PUBLICACION')->result();
    }

    public function obtener_publicacion($id) {
        return $this->db->get_where('PUBLICACION', array('idPublicacion' => $id))->row();
    }

    public function agregar_publicacion($data) {
        $this->db->insert('PUBLICACION', $data);
        return $this->db->insert_id();
    }

    public function modificar_publicacion($id, $data) {
        $this->db->where('idPublicacion', $id);
        return $this->db->update('PUBLICACION', $data);
    }

    public function eliminar_publicacion($id) {
        $this->db->where('idPublicacion', $id);
        return $this->db->delete('PUBLICACION');
    }

    public function deshabilitar_publicacion($id) {
        $this->db->where('idPublicacion', $id);
        return $this->db->update('PUBLICACION', array('estado' => 0));
    }

    public function habilitar_publicacion($id) {
        $this->db->where('idPublicacion', $id);
        return $this->db->update('PUBLICACION', array('estado' => 1));
    }
}