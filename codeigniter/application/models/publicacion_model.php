<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Publicacion_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
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

    /*public function obtener_publicaciones_disponibles() {
        $this->db->select('p.idPublicacion, p.titulo, p.portada, e.nombreEditorial, t.nombreTipo');
        $this->db->from('PUBLICACION p');
        $this->db->join('EDITORIAL e', 'p.idEditorial = e.idEditorial');
        $this->db->join('TIPO t', 'p.idTipo = t.idTipo');
        $this->db->where('p.estado', ESTADO_PUBLICACION_DISPONIBLE);
        return $this->db->get()->result();
    }*/

    public function get_publicacion($idPublicacion) {
        $this->db->select('p.*, e.nombreEditorial, t.nombreTipo');
        $this->db->from('PUBLICACION p');
        $this->db->join('EDITORIAL e', 'p.idEditorial = e.idEditorial');
        $this->db->join('TIPO t', 'p.idTipo = t.idTipo');
        $this->db->where('p.idPublicacion', $idPublicacion);
        $query = $this->db->get();
        return $query->row();
    }
    public function agregar_publicacion($data) {
        $this->db->insert('PUBLICACION', $data);
        return $this->db->insert_id();
    }

    public function actualizar_publicacion($idPublicacion, $data) {
        $this->db->where('idPublicacion', $idPublicacion);
        return $this->db->update('PUBLICACION', $data);
    }

    public function obtener_publicacion($idPublicacion) {
        $this->db->select('PUBLICACION.*, EDITORIAL.nombreEditorial, TIPO.nombreTipo');
        $this->db->from('PUBLICACION');
        $this->db->join('EDITORIAL', 'EDITORIAL.idEditorial = PUBLICACION.idEditorial');
        $this->db->join('TIPO', 'TIPO.idTipo = PUBLICACION.idTipo');
        $this->db->where('PUBLICACION.idPublicacion', $idPublicacion);
        return $this->db->get()->row();
    }

    public function listar_publicaciones() {
        $this->db->select('PUBLICACION.*, TIPO.nombreTipo, EDITORIAL.nombreEditorial');
        $this->db->from('PUBLICACION');
        $this->db->join('TIPO', 'TIPO.idTipo = PUBLICACION.idTipo');
        $this->db->join('EDITORIAL', 'EDITORIAL.idEditorial = PUBLICACION.idEditorial');
        $this->db->where('PUBLICACION.estado', 1);
        return $this->db->get()->result();
    }

    public function buscar_publicaciones($termino) {
        $this->db->select('PUBLICACION.*, TIPO.nombreTipo, EDITORIAL.nombreEditorial');
        $this->db->from('PUBLICACION');
        $this->db->join('TIPO', 'TIPO.idTipo = PUBLICACION.idTipo');
        $this->db->join('EDITORIAL', 'EDITORIAL.idEditorial = PUBLICACION.idEditorial');
        $this->db->where('PUBLICACION.estado', 1);
        $this->db->group_start();
        $this->db->like('PUBLICACION.titulo', $termino);
        $this->db->or_like('EDITORIAL.nombreEditorial', $termino);
        $this->db->or_like('TIPO.nombreTipo', $termino);
        $this->db->group_end();
        return $this->db->get()->result();
    }

   
   public function obtener_publicacion_detallada($idPublicacion) {
        $this->db->select('PUBLICACION.*, TIPO.nombreTipo, EDITORIAL.nombreEditorial');
        $this->db->from('PUBLICACION');
        $this->db->join('TIPO', 'TIPO.idTipo = PUBLICACION.idTipo');
        $this->db->join('EDITORIAL', 'EDITORIAL.idEditorial = PUBLICACION.idEditorial');
        $this->db->where('PUBLICACION.idPublicacion', $idPublicacion);
        return $this->db->get()->row();
    }

    public function obtener_publicaciones_disponibles() {
        $this->db->where('estado', ESTADO_PUBLICACION_DISPONIBLE);
        return $this->db->get('PUBLICACION')->result();
    }
    public function cambiar_estado_publicacion($idPublicacion, $nuevoEstado) {
        $data = array(
            'estado' => $nuevoEstado,
            'fechaActualizacion' => date('Y-m-d H:i:s')
        );
        $this->db->where('idPublicacion', $idPublicacion);
        return $this->db->update('PUBLICACION', $data);
    }
    public function listar_todas_publicaciones() {
        $this->db->select('
            PUBLICACION.idPublicacion,
            PUBLICACION.titulo,
            PUBLICACION.fechaPublicacion,
            PUBLICACION.numeroPaginas,
            PUBLICACION.portada,
            PUBLICACION.descripcion,
            PUBLICACION.ubicacionFisica,
            PUBLICACION.estado,
            PUBLICACION.fechaCreacion,
            PUBLICACION.fechaActualizacion,
            TIPO.nombreTipo,
            EDITORIAL.nombreEditorial
        ');
        $this->db->from('PUBLICACION');
        $this->db->join('TIPO', 'TIPO.idTipo = PUBLICACION.idTipo');
        $this->db->join('EDITORIAL', 'EDITORIAL.idEditorial = PUBLICACION.idEditorial');
        // No aplicamos filtro de estado para obtener todas las publicaciones
        $this->db->order_by('PUBLICACION.fechaCreacion', 'DESC');
        return $this->db->get()->result();
    }
    public function obtener_estado_personalizado($idPublicacion, $idUsuario) {
        $this->db->select('PUBLICACION.estado, PRESTAMO.idUsuario AS usuario_prestamo');
        $this->db->from('PUBLICACION');
        $this->db->join('PRESTAMO', 'PUBLICACION.idPublicacion = PRESTAMO.idPublicacion AND PRESTAMO.estadoPrestamo = ' . ESTADO_PRESTAMO_ACTIVO, 'left');
        $this->db->where('PUBLICACION.idPublicacion', $idPublicacion);
        $resultado = $this->db->get()->row();

        if ($resultado) {
            if ($resultado->estado == ESTADO_PUBLICACION_EN_CONSULTA) {
                if ($resultado->usuario_prestamo == $idUsuario) {
                    return 'En préstamo por ti';
                } else {
                    return 'En consulta';
                }
            } else {
                return $this->mapear_estado($resultado->estado);
            }
        }

        return 'Estado desconocido';
    }

    private function mapear_estado($estado) {
        $mapeo_estados = [
            ESTADO_PUBLICACION_DISPONIBLE => 'Disponible',
            ESTADO_PUBLICACION_EN_CONSULTA => 'En préstamo',
            ESTADO_PUBLICACION_EN_MANTENIMIENTO => 'En mantenimiento'
        ];

        return isset($mapeo_estados[$estado]) ? $mapeo_estados[$estado] : 'Estado desconocido';
    }

}