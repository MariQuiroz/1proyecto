<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prestamo_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function crear_prestamo($data) {
        $this->db->insert('PRESTAMO', $data);
        return $this->db->insert_id();
    }

    public function get_prestamos_activos() {
        $this->db->select('p.*, u.nombres, u.apellidos, pub.titulo');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = p.idSolicitud');
        $this->db->join('USUARIO u', 'u.idUsuario = sp.idUsuario');
        $this->db->join('PUBLICACION pub', 'pub.idPublicacion = sp.idPublicacion');
        $this->db->where('p.estado', 'activo');
        return $this->db->get()->result();
    }

    public function get_prestamo($idPrestamo) {
        return $this->db->get_where('PRESTAMO', ['idPrestamo' => $idPrestamo])->row();
    }

    public function actualizar_estado_prestamo($idPrestamo, $estado) {
        $this->db->where('idPrestamo', $idPrestamo);
        return $this->db->update('PRESTAMO', ['estado' => $estado]);
    }

    public function set_fecha_devolucion_real($idPrestamo, $fecha) {
        $this->db->where('idPrestamo', $idPrestamo);
        return $this->db->update('PRESTAMO', ['fechaDevolucionReal' => $fecha]);
    }

    public function get_prestamos_usuario($idUsuario) {
        $this->db->select('p.*, pub.titulo');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = p.idSolicitud');
        $this->db->join('PUBLICACION pub', 'pub.idPublicacion = sp.idPublicacion');
        $this->db->where('sp.idUsuario', $idUsuario);
        return $this->db->get()->result();
    }
    public function obtener_prestamos_activos() {
        $this->db->select('*');
        $this->db->from('PRESTAMOS');
        $this->db->where('estado', 'activo');  // Suponiendo que 'estado' es la columna que indica si el préstamo está activo
        $query = $this->db->get();
        return $query->result();
    }
    public function obtener_prestamos_activos_usuario($idUsuario) {
        $this->db->select('PRESTAMO.*, PUBLICACION.titulo');
        $this->db->from('PRESTAMO');
        $this->db->join('PUBLICACION', 'PUBLICACION.idPublicacion = PRESTAMO.idPublicacion');
        $this->db->where('PRESTAMO.idUsuario', $idUsuario);
        $this->db->where('PRESTAMO.estado', 'activo'); // Asegúrate de que el estado "activo" esté correcto
        $query = $this->db->get();

        return $query->result();
    }
    public function contar_prestamos_activos() {
        $this->db->where('DATE(fechaCreacion)', date('Y-m-d'));
        $this->db->where('estadoPrestamo', 'activo');
        $this->db->where('horaDevolucion IS NULL');
        return $this->db->count_all_results('PRESTAMO');
    }

    public function contar_prestamos_activos_usuario($idUsuario) {
        $this->db->join('SOLICITUD_PRESTAMO', 'PRESTAMO.idSolicitud = SOLICITUD_PRESTAMO.idSolicitud');
        $this->db->where('SOLICITUD_PRESTAMO.idUsuario', $idUsuario);
        $this->db->where('PRESTAMO.estadoPrestamo', 'activo');
        $this->db->where('PRESTAMO.horaDevolucion IS NULL');
        return $this->db->count_all_results('PRESTAMO');
    }
    
    public function contar_prestamos_no_devueltos() {
        $this->db->where('estadoPrestamo', 'activo');
        $this->db->where('horaDevolucion IS NULL');
        return $this->db->count_all_results('PRESTAMO');
    }

    

    
   

}