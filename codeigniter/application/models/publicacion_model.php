<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Publicacion_model extends CI_Model {


    public function listar_publicaciones($limite = NULL, $offset = NULL) {
        $this->db->select('*');
        $this->db->from('PUBLICACION');
        $this->db->where('estado', 1);
        if ($limite && $offset) {
            $this->db->limit($limite, $offset);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function obtener_publicacion($idPublicacion) {
        $this->db->where('idPublicacion', $idPublicacion);
        $query = $this->db->get('PUBLICACION');
        return $query->row();
    }

    public function agregar_publicacion($data) {
        return $this->db->insert('PUBLICACION', $data);
    }

    public function actualizar_publicacion($idPublicacion, $data) {
        $this->db->where('idPublicacion', $idPublicacion);
        return $this->db->update('PUBLICACION', $data);
    }

    public function eliminar_publicacion($idPublicacion) {
        $this->db->where('idPublicacion', $idPublicacion);
        return $this->db->delete('PUBLICACION');
    }

    public function buscar_publicaciones($termino) {
        // Asegurarse de que $termino no sea nulo
        $termino = $termino ?? '';

        // Trim y sanitizar el término de búsqueda
        $termino = $this->db->escape_like_str(trim($termino));

        $this->db->like('titulo', $termino);
        $this->db->or_like('editorial', $termino);
        $this->db->or_like('añoPublicacion', $termino);
        
        $query = $this->db->get('PUBLICACION');
        return $query->result();
    }
    public function contar_publicaciones() {
        return $this->db->count_all('PUBLICACION');
    }

    public function obtener_publicaciones_disponibles() {
        $this->db->select('idPublicacion, titulo');
        $this->db->from('PUBLICACION');
        $this->db->where('estado', 1); // Asumiendo que 1 es el estado para publicaciones disponibles
        return $this->db->get()->result();
    }
    public function actualizar_estado_publicacion($idPublicacion, $estado) {
        return $this->db->update('PUBLICACION', ['estado' => $estado], ['idPublicacion' => $idPublicacion]);
    }

    /*public function listar_publicaciones($estado = null) {
        if ($estado) {
            $this->db->where('estado', $estado);
        }
        return $this->db->get('PUBLICACION')->result();
    }*/

}