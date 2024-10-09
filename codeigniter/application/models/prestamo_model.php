<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prestamo_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function iniciar_prestamo($idSolicitud, $idEncargado) {
        $solicitud = $this->db->get_where('SOLICITUD_PRESTAMO', ['idSolicitud' => $idSolicitud])->row();
        
        if (!$solicitud) {
            return false;
        }

        $publicacion = $this->db->get_where('PUBLICACION', ['idPublicacion' => $solicitud->idPublicacion])->row();
        
        if ($publicacion->estado != ESTADO_PUBLICACION_DISPONIBLE) {
            return false;
        }

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
            'idUsuarioCreador' => $this->session->userdata('idUsuario') // Usar el ID del usuario en sesión
        ];

        $this->db->insert('PRESTAMO', $data_prestamo);

        $this->db->where('idPublicacion', $solicitud->idPublicacion);
        $this->db->update('PUBLICACION', ['estado' => ESTADO_PUBLICACION_EN_CONSULTA]);

        $this->db->where('idSolicitud', $idSolicitud);
        $this->db->update('SOLICITUD_PRESTAMO', ['estadoSolicitud' => ESTADO_SOLICITUD_FINALIZADA]);

        return true;
    }


    public function finalizar_prestamo($idPrestamo, $idEncargado) {
        $prestamo = $this->db->get_where('PRESTAMO', ['idPrestamo' => $idPrestamo])->row();
        
        if (!$prestamo) {
            return false;
        }
    
        $data_update = [
            'estadoPrestamo' => ESTADO_PRESTAMO_FINALIZADO,
            'idEncargadoDevolucion' => $idEncargado,
            'horaDevolucion' => date('H:i:s'),
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $this->session->userdata('idUsuario') // Usar el ID del usuario en sesión
        ];
    
        $this->db->trans_start(); // Iniciar transacción
    
        $this->db->where('idPrestamo', $idPrestamo);
        $this->db->update('PRESTAMO', $data_update);
    
        $this->db->where('idPublicacion', $prestamo->idPublicacion);
        $this->db->update('PUBLICACION', ['estado' => ESTADO_PUBLICACION_DISPONIBLE]);
    
        $this->db->trans_complete(); // Completar transacción
    
        return $this->db->trans_status();
    }

    public function obtener_prestamos_activos() {
        $this->db->select('P.*, U.nombres, U.apellidoPaterno, PUB.titulo');
        $this->db->from('PRESTAMO P');
        $this->db->join('USUARIO U', 'P.idUsuario = U.idUsuario');
        $this->db->join('PUBLICACION PUB', 'P.idPublicacion = PUB.idPublicacion');
        $this->db->where('P.estadoPrestamo', ESTADO_PRESTAMO_ACTIVO);
        return $this->db->get()->result();
    }

    public function obtener_historial_prestamos() {
        $this->db->select('P.*, U.nombres, U.apellidoPaterno, PUB.titulo');
        $this->db->from('PRESTAMO P');
        $this->db->join('USUARIO U', 'P.idUsuario = U.idUsuario');
        $this->db->join('PUBLICACION PUB', 'P.idPublicacion = PUB.idPublicacion');
        $this->db->order_by('P.fechaPrestamo', 'DESC');
        return $this->db->get()->result();
    }

    public function obtener_prestamos_usuario($idUsuario) {
        $this->db->select('P.*, PUB.titulo');
        $this->db->from('PRESTAMO P');
        $this->db->join('PUBLICACION PUB', 'P.idPublicacion = PUB.idPublicacion');
        $this->db->where('P.idUsuario', $idUsuario);
        $this->db->order_by('P.fechaPrestamo', 'DESC');
        return $this->db->get()->result();
    }

    public function obtener_prestamo($idPrestamo) {
        return $this->db->get_where('PRESTAMO', ['idPrestamo' => $idPrestamo])->row();
    }
    public function obtener_prestamo_detallado($idPrestamo) {
        $this->db->select('P.*, PUB.titulo, PUB.fechaPublicacion, PUB.ubicacionFisica, PUB.signatura_topografica, U.carnet, U.profesion, E.nombres AS nombres_encargado, E.apellidoPaterno AS apellidoPaterno_encargado');
        $this->db->from('PRESTAMO P');
        $this->db->join('PUBLICACION PUB', 'P.idPublicacion = PUB.idPublicacion');
        $this->db->join('USUARIO U', 'P.idUsuario = U.idUsuario');
        $this->db->join('USUARIO E', 'P.idEncargadoPrestamo = E.idUsuario');
        $this->db->where('P.idPrestamo', $idPrestamo);
        return $this->db->get()->row();
    }
    public function obtener_datos_ficha_prestamo($idPrestamo) {
        $this->db->select('P.*, PUB.titulo, PUB.fechaPublicacion, ED.nombreEditorial, PUB.ubicacionFisica, U.carnet, U.profesion, EN.nombres AS nombreEncargado, EN.apellidoPaterno AS apellidoEncargado');
        $this->db->from('PRESTAMO P');
        $this->db->join('PUBLICACION PUB', 'P.idPublicacion = PUB.idPublicacion');
        $this->db->join('EDITORIAL ED', 'PUB.idEditorial = ED.idEditorial');
        $this->db->join('USUARIO U', 'P.idUsuario = U.idUsuario');
        $this->db->join('USUARIO EN', 'P.idEncargadoPrestamo = EN.idUsuario');
        $this->db->where('P.idPrestamo', $idPrestamo);
        return $this->db->get()->row_array(); // Cambiamos row() por row_array()
    }
    public function obtener_datos_ficha_devolucion($idPrestamo) {
        $this->db->select('P.*, PUB.titulo, U.nombres AS nombreLector, U.apellidoPaterno AS apellidoLector, U.email, EN.nombres AS nombreEncargado, EN.apellidoPaterno AS apellidoEncargado');
        $this->db->from('PRESTAMO P');
        $this->db->join('PUBLICACION PUB', 'P.idPublicacion = PUB.idPublicacion');
        $this->db->join('USUARIO U', 'P.idUsuario = U.idUsuario');
        $this->db->join('USUARIO EN', 'P.idEncargadoDevolucion = EN.idUsuario');
        $this->db->where('P.idPrestamo', $idPrestamo);
        return $this->db->get()->row_array();
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