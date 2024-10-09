<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitud_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    private function _verificar_rol($roles_permitidos) {
        $rol_actual = $this->session->userdata('rol');
        return in_array($rol_actual, $roles_permitidos);
    }

    public function crear_solicitud($idUsuario, $idPublicacion) {
        if (!$this->_verificar_rol(['lector'])) {
            return false;
        }

        $this->db->trans_start();

        $data_solicitud = array(
            'idUsuario' => $idUsuario,
            'idPublicacion' => $idPublicacion,
            'fechaSolicitud' => date('Y-m-d H:i:s'),
            'estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'estado' => 1,
            'fechaCreacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idUsuario
        );

        $this->db->insert('SOLICITUD_PRESTAMO', $data_solicitud);
        $idSolicitud = $this->db->insert_id();

        $this->db->trans_complete();

        return $this->db->trans_status() ? $idSolicitud : false;
    }

    /*public function aprobar_solicitud($idSolicitud, $idEncargado) {
        $this->db->trans_start();
    
        $solicitud = $this->db->get_where('SOLICITUD_PRESTAMO', ['idSolicitud' => $idSolicitud])->row();
    
        if (!$solicitud || $solicitud->estadoSolicitud != ESTADO_SOLICITUD_PENDIENTE) {
            $this->db->trans_rollback();
            return false;
        }
    
        $this->db->where('idSolicitud', $idSolicitud);
        $this->db->update('SOLICITUD_PRESTAMO', [
            'estadoSolicitud' => ESTADO_SOLICITUD_APROBADA,
            'fechaAprobacionRechazo' => date('Y-m-d H:i:s'),
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idEncargado
        ]);
    
        $data_prestamo = array(
            'idSolicitud' => $idSolicitud,
            'idUsuario' => $solicitud->idUsuario,
            'idPublicacion' => $solicitud->idPublicacion,
            'idEncargadoPrestamo' => $idEncargado,
            'fechaPrestamo' => date('Y-m-d H:i:s'),
            'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
            'horaInicio' => date('H:i:s'),
            'estado' => 1,
            'fechaCreacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idEncargado
        );
    
        $this->db->insert('PRESTAMO', $data_prestamo);
    
        $this->db->where('idPublicacion', $solicitud->idPublicacion);
        $this->db->update('PUBLICACION', [
            'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idEncargado
        ]);
    
        $this->db->trans_complete();
    
        return $this->db->trans_status();
    }
*/

    public function rechazar_solicitud($idSolicitud, $idEncargado) {
        if (!$this->_verificar_rol(['administrador', 'encargado'])) {
            return false;
        }
    
        $this->db->trans_start();
    
        $this->db->where('idSolicitud', $idSolicitud);
        $this->db->update('SOLICITUD_PRESTAMO', [
            'estadoSolicitud' => ESTADO_SOLICITUD_RECHAZADA,
            'fechaAprobacionRechazo' => date('Y-m-d H:i:s'),
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idEncargado
        ]);
    
        $this->db->trans_complete();
    
        return $this->db->trans_status();
    }

    public function obtener_solicitudes_pendientes() {
        if (!$this->_verificar_rol(['administrador', 'encargado'])) {
            return [];
        }

        $this->db->select('SP.*, U.nombres, U.apellidoPaterno, P.titulo');
        $this->db->from('SOLICITUD_PRESTAMO SP');
        $this->db->join('USUARIO U', 'SP.idUsuario = U.idUsuario');
        $this->db->join('PUBLICACION P', 'SP.idPublicacion = P.idPublicacion');
        $this->db->where('SP.estadoSolicitud', ESTADO_SOLICITUD_PENDIENTE);
        $this->db->where('SP.estado', 1);
        return $this->db->get()->result();
    }

    public function eliminar_solicitud($idSolicitud, $idUsuario) {
        if (!$this->_verificar_rol(['administrador', 'encargado'])) {
            return false;
        }

        $this->db->trans_start();

        $this->db->where('idSolicitud', $idSolicitud);
        $this->db->update('SOLICITUD_PRESTAMO', [
            'estado' => 0,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idUsuario
        ]);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function get_solicitudes_pendientes() {
        $this->db->select('sp.*, u.nombres, u.apellidos, p.titulo');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('USUARIO u', 'u.idUsuario = sp.idUsuario');
        $this->db->join('PUBLICACION p', 'p.idPublicacion = sp.idPublicacion');
        $this->db->where('sp.estado', 'pendiente');
        return $this->db->get()->result();
    }

    public function actualizar_estado_solicitud($idSolicitud, $estado) {
        $this->db->where('idSolicitud', $idSolicitud);
        $this->db->update('SOLICITUD_PRESTAMO', array('estadoSolicitud' => $estado));
    }
    // Método para obtener las solicitudes de préstamo pendientes de un usuario
    public function obtener_solicitudes_pendientes_usuario($idUsuario) {
        $this->db->select('SOLICITUD_PRESTAMO.*, PUBLICACION.titulo');
        $this->db->from('SOLICITUD_PRESTAMO');
        $this->db->join('DETALLE_PRESTAMO', 'DETALLE_PRESTAMO.idSolicitud = SOLICITUD_PRESTAMO.idSolicitud');
        $this->db->join('PUBLICACION', 'PUBLICACION.idPublicacion = DETALLE_PRESTAMO.idPublicacion');
        $this->db->where('SOLICITUD_PRESTAMO.idUsuario', $idUsuario);
        $this->db->where('SOLICITUD_PRESTAMO.estadoSolicitud', 'pendiente'); // Estado pendiente
        $query = $this->db->get();

        return $query->result();
    }
    public function contar_solicitudes_pendientes() {
        $this->db->where('estado', 'pendiente');
        return $this->db->count_all_results('SOLICITUD_PRESTAMO');
    }

    public function contar_solicitudes_pendientes_usuario($idUsuario) {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->where('estado', 'pendiente');
        return $this->db->count_all_results('SOLICITUD_PRESTAMO');
    }
   /* public function actualizar_estado_solicitud($idSolicitud, $nuevoEstado) {
        $this->db->where('idSolicitud', $idSolicitud);
        $this->db->update('SOLICITUD_PRESTAMO', array('estadoSolicitud' => $nuevoEstado));
        return $this->db->affected_rows() > 0;
    }*/

    public function get_solicitud($idSolicitud) {
        $this->db->where('idSolicitud', $idSolicitud);
        $query = $this->db->get('SOLICITUD_PRESTAMO');
        return $query->row();
    }

    public function get_solicitudes_usuario($idUsuario) {
        $this->db->where('idUsuario', $idUsuario);
        $query = $this->db->get('SOLICITUD_PRESTAMO');
        return $query->result();
    }
    public function obtener_solicitudes_usuario($idUsuario) {
        $this->db->select('s.*, p.titulo, s.estadoSolicitud');
        $this->db->from('SOLICITUD_PRESTAMO s');
        $this->db->join('PUBLICACION p', 'p.idPublicacion = s.idPublicacion');
        $this->db->where('s.idUsuario', $idUsuario);
        $this->db->order_by('s.fechaSolicitud', 'DESC');
        return $this->db->get()->result();
    }
    public function obtener_detalle_solicitud($idSolicitud) {
        $this->db->select('s.*, p.titulo as titulo_publicacion, u.nombres, u.apellidoPaterno');
        $this->db->from('SOLICITUD_PRESTAMO s');
        $this->db->join('PUBLICACION p', 'p.idPublicacion = s.idPublicacion');
        $this->db->join('USUARIO u', 'u.idUsuario = s.idUsuario');
        $this->db->where('s.idSolicitud', $idSolicitud);
        return $this->db->get()->row();
    }
    public function obtener_solicitudes_por_estado($estado) {
        $this->db->select('SP.*, U.nombres, U.apellidoPaterno, P.titulo');
        $this->db->from('SOLICITUD_PRESTAMO SP');
        $this->db->join('USUARIO U', 'SP.idUsuario = U.idUsuario');
        $this->db->join('PUBLICACION P', 'SP.idPublicacion = P.idPublicacion');
        $this->db->where('SP.estadoSolicitud', $estado);
        $this->db->where('SP.estado', 1);
        return $this->db->get()->result();
    }
    
    public function obtener_historial_solicitudes() {
        $this->db->select('SP.*, U.nombres, U.apellidoPaterno, P.titulo');
        $this->db->from('SOLICITUD_PRESTAMO SP');
        $this->db->join('USUARIO U', 'SP.idUsuario = U.idUsuario');
        $this->db->join('PUBLICACION P', 'SP.idPublicacion = P.idPublicacion');
        $this->db->where('SP.estado', 1);
        $this->db->order_by('SP.fechaSolicitud', 'DESC');
        return $this->db->get()->result();
    }
    public function aprobar_solicitud($idSolicitud, $idEncargado) {
        $solicitud = $this->db->get_where('SOLICITUD_PRESTAMO', ['idSolicitud' => $idSolicitud])->row();
        
        if (!$solicitud || $solicitud->estadoSolicitud != ESTADO_SOLICITUD_PENDIENTE) {
            return false;
        }
    
        $this->db->trans_start();
    
        // Actualizar la solicitud
        $this->db->where('idSolicitud', $idSolicitud);
        $this->db->update('SOLICITUD_PRESTAMO', [
            'estadoSolicitud' => ESTADO_SOLICITUD_APROBADA,
            'fechaAprobacionRechazo' => date('Y-m-d H:i:s'),
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idEncargado
        ]);
    
        // Crear el préstamo
        $data_prestamo = [
            'idSolicitud' => $idSolicitud,
            'idUsuario' => $solicitud->idUsuario,
            'idPublicacion' => $solicitud->idPublicacion,
            'idEncargadoPrestamo' => $idEncargado,
            'fechaPrestamo' => date('Y-m-d H:i:s'),
            'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
            'horaInicio' => date('H:i:s'),
            'estado' => 1,
            'fechaCreacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idEncargado
        ];
    
        $this->db->insert('PRESTAMO', $data_prestamo);
        $idPrestamo = $this->db->insert_id();
    
        // Actualizar el estado de la publicación
        $this->db->where('idPublicacion', $solicitud->idPublicacion);
        $this->db->update('PUBLICACION', [
            'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idEncargado
        ]);
    
        $this->db->trans_complete();
    
        return $this->db->trans_status() ? $idPrestamo : false;
    }
    
}