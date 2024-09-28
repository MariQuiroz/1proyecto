<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Publicacion_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function listar_publicaciones() {
        $this->db->select('p.*, t.nombreTipo, e.nombreEditorial, u.nombres, u.apellidoPaterno');
        $this->db->from('PUBLICACION p');
        $this->db->join('TIPO t', 'p.idTipo = t.idTipo');
        $this->db->join('EDITORIAL e', 'p.idEditorial = e.idEditorial');
        $this->db->join('USUARIO u', 'p.idUsuario = u.idUsuario');
        $this->db->where('p.estado !=', 0);
        $query = $this->db->get();
        return $query->result();
    }

    public function agregar_publicacion($data) {
        return $this->db->insert('PUBLICACION', $data);
    }

    public function obtener_publicacion($idPublicacion) {
        $this->db->where('idPublicacion', $idPublicacion);
        $query = $this->db->get('PUBLICACION');
        return $query->row();
    }

    public function obtener_publicacion_detallada($idPublicacion) {
        $this->db->select('p.*, t.nombreTipo, e.nombreEditorial, u.nombres, u.apellidoPaterno');
        $this->db->from('PUBLICACION p');
        $this->db->join('TIPO t', 'p.idTipo = t.idTipo');
        $this->db->join('EDITORIAL e', 'p.idEditorial = e.idEditorial');
        $this->db->join('USUARIO u', 'p.idUsuario = u.idUsuario');
        $this->db->where('p.idPublicacion', $idPublicacion);
        $query = $this->db->get();
        return $query->row();
    }

    public function actualizar_publicacion($idPublicacion, $data) {
        $this->db->where('idPublicacion', $idPublicacion);
        return $this->db->update('PUBLICACION', $data);
    }

    public function cambiar_estado_publicacion($idPublicacion, $data) {
        $this->db->where('idPublicacion', $idPublicacion);
        return $this->db->update('PUBLICACION', $data);
    }

    public function buscar_publicaciones($termino) {
        $this->db->select('p.*, t.nombreTipo, e.nombreEditorial');
        $this->db->from('PUBLICACION p');
        $this->db->join('TIPO t', 'p.idTipo = t.idTipo');
        $this->db->join('EDITORIAL e', 'p.idEditorial = e.idEditorial');
        $this->db->like('p.titulo', $termino);
        $this->db->or_like('p.descripcion', $termino);
        $this->db->or_like('t.nombreTipo', $termino);
        $this->db->or_like('e.nombreEditorial', $termino);
        $this->db->where('p.estado !=', 0);
        $query = $this->db->get();
        return $query->result();
    }

    public function contar_publicaciones() {
        $this->db->where('estado !=', 0);
        return $this->db->count_all_results('PUBLICACION');
    }

    public function obtener_publicaciones_por_estado($estado) {
        $this->db->where('estado', $estado);
        $query = $this->db->get('PUBLICACION');
        return $query->result();
    }

    public function obtener_nombre_estado($estado) {
        switch ($estado) {
            case ESTADO_PUBLICACION_DISPONIBLE:
                return 'Disponible';
            case ESTADO_PUBLICACION_EN_CONSULTA:
                return 'En Consulta';
            case ESTADO_PUBLICACION_EN_MANTENIMIENTO:
                return 'En Mantenimiento';
            default:
                return 'Desconocido';
        }
    }

    public function obtener_publicaciones_disponibles() {
        $this->db->select('p.idPublicacion, p.titulo, p.portada, e.nombreEditorial, t.nombreTipo');
        $this->db->from('PUBLICACION p');
        $this->db->join('EDITORIAL e', 'p.idEditorial = e.idEditorial');
        $this->db->join('TIPO t', 'p.idTipo = t.idTipo');
        $this->db->where('p.estado', ESTADO_PUBLICACION_DISPONIBLE);
        return $this->db->get()->result();
    }

    public function get_publicacion($idPublicacion) {
        $this->db->select('p.*, e.nombreEditorial, t.nombreTipo');
        $this->db->from('PUBLICACION p');
        $this->db->join('EDITORIAL e', 'p.idEditorial = e.idEditorial');
        $this->db->join('TIPO t', 'p.idTipo = t.idTipo');
        $this->db->where('p.idPublicacion', $idPublicacion);
        $query = $this->db->get();
        return $query->row();
    }
}