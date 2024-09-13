

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function prestamos_por_periodo($fecha_inicio, $fecha_fin) {
        $this->db->select('COUNT(*) as total_prestamos, DATE(fechaPrestamo) as fecha');
        $this->db->from('PRESTAMO');
        $this->db->where('fechaPrestamo >=', $fecha_inicio);
        $this->db->where('fechaPrestamo <=', $fecha_fin);
        $this->db->group_by('DATE(fechaPrestamo)');
        $query = $this->db->get();
        return $query->result();
    }

    public function publicaciones_mas_solicitadas($limite = 10) {
        $this->db->select('p.titulo, COUNT(*) as total_prestamos');
        $this->db->from('PRESTAMO pr');
        $this->db->join('PUBLICACION p', 'pr.idPublicacion = p.idPublicacion');
        $this->db->group_by('pr.idPublicacion');
        $this->db->order_by('total_prestamos', 'DESC');
        $this->db->limit($limite);
        $query = $this->db->get();
        return $query->result();
    }

    public function usuarios_mas_activos($limite = 10) {
        $this->db->select('u.nombres, u.apellidoPaterno, COUNT(*) as total_prestamos');
        $this->db->from('PRESTAMO pr');
        $this->db->join('USUARIO u', 'pr.idUsuario = u.idUsuario');
        $this->db->group_by('pr.idUsuario');
        $this->db->order_by('total_prestamos', 'DESC');
        $this->db->limit($limite);
        $query = $this->db->get();
        return $query->result();
    }

    public function guardar_reporte($data) {
        return $this->db->insert('REPORTE', $data);
    }

    public function obtener_reporte($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('REPORTE');
        return $query->row();
    }

    public function listar_reportes() {
        $query = $this->db->get('REPORTE');
        return $query->result();
    }
}