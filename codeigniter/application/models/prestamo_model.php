<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prestamo_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function iniciar_prestamo($idSolicitud, $idEncargado) {
        $this->db->trans_start();

        try {
            // Obtener información de la solicitud con el detalle
            $this->db->select('
                sp.idSolicitud,
                sp.idUsuario,
                ds.idPublicacion,
                sp.estadoSolicitud,
                p.estado as estado_publicacion
            ');
            $this->db->from('SOLICITUD_PRESTAMO sp');
            $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
            $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
            $this->db->where('sp.idSolicitud', $idSolicitud);
            $solicitud = $this->db->get()->row();

            if (!$solicitud || $solicitud->estado_publicacion != ESTADO_PUBLICACION_DISPONIBLE) {
                $this->db->trans_rollback();
                return false;
            }

            $fechaActual = date('Y-m-d H:i:s');
            $horaActual = date('H:i:s');
            
            // Crear registro de préstamo
            $data_prestamo = [
                'idSolicitud' => $idSolicitud,
                'idEncargadoPrestamo' => $idEncargado,
                'fechaPrestamo' => $fechaActual,
                'horaInicio' => $horaActual,
                'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
                'estado' => 1,
                'fechaCreacion' => $fechaActual,
                'idUsuarioCreador' => $this->session->userdata('idUsuario')
            ];

            $this->db->insert('PRESTAMO', $data_prestamo);

            // Actualizar estado de la publicación
            $this->db->where('idPublicacion', $solicitud->idPublicacion);
            $this->db->update('PUBLICACION', [
                'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
                'fechaActualizacion' => $fechaActual
            ]);

            // Actualizar estado de la solicitud
            $this->db->where('idSolicitud', $idSolicitud);
            $this->db->update('SOLICITUD_PRESTAMO', [
                'estadoSolicitud' => ESTADO_SOLICITUD_FINALIZADA,
                'fechaActualizacion' => $fechaActual
            ]);

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error al iniciar préstamo: ' . $e->getMessage());
            return false;
        }
    }


    public function finalizar_prestamo($idPrestamo, $idEncargado) {
        $this->db->trans_start();

        try {
            // Obtener información del préstamo
            $this->db->select('
                p.idPrestamo,
                ds.idPublicacion,
                p.estadoPrestamo
            ');
            $this->db->from('PRESTAMO p');
            $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
            $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
            $this->db->where('p.idPrestamo', $idPrestamo);
            $prestamo = $this->db->get()->row();

            if (!$prestamo || $prestamo->estadoPrestamo != ESTADO_PRESTAMO_ACTIVO) {
                $this->db->trans_rollback();
                return false;
            }

            $fechaActual = date('Y-m-d H:i:s');
            
            // Actualizar préstamo
            $data_update = [
                'estadoPrestamo' => ESTADO_PRESTAMO_FINALIZADO,
                'idEncargadoDevolucion' => $idEncargado,
                'horaDevolucion' => date('H:i:s'),
                'fechaActualizacion' => $fechaActual,
                'idUsuarioCreador' => $this->session->userdata('idUsuario')
            ];

            $this->db->where('idPrestamo', $idPrestamo);
            $this->db->update('PRESTAMO', $data_update);

            // Actualizar estado de la publicación
            $this->db->where('idPublicacion', $prestamo->idPublicacion);
            $this->db->update('PUBLICACION', [
                'estado' => ESTADO_PUBLICACION_DISPONIBLE,
                'fechaActualizacion' => $fechaActual
            ]);

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error al finalizar préstamo: ' . $e->getMessage());
            return false;
        }
    }

    public function obtener_prestamos_activos() {
        $this->db->select('
            p.idPrestamo,
            p.fechaPrestamo,
            p.horaInicio,
            p.estadoPrestamo,
            u.nombres,
            u.apellidoPaterno,
            pub.titulo,
            ds.observaciones,
            e.nombreEditorial
        ');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('USUARIO u', 'sp.idUsuario = u.idUsuario');
        $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
        $this->db->join('EDITORIAL e', 'pub.idEditorial = e.idEditorial');
        $this->db->where('p.estadoPrestamo', ESTADO_PRESTAMO_ACTIVO);
        $this->db->order_by('p.fechaPrestamo', 'DESC');
        return $this->db->get()->result();
    }


    public function obtener_historial_prestamos() {
        $this->db->select('
            p.idPrestamo,
            p.fechaPrestamo,
            p.horaInicio,
            p.horaDevolucion,
            p.estadoPrestamo,
            u.nombres,
            u.apellidoPaterno,
            pub.titulo,
            ds.observaciones,
            e.nombreEditorial,
            enc.nombres as nombre_encargado,
            enc.apellidoPaterno as apellido_encargado
        ');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('USUARIO u', 'sp.idUsuario = u.idUsuario');
        $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
        $this->db->join('EDITORIAL e', 'pub.idEditorial = e.idEditorial');
        $this->db->join('USUARIO enc', 'p.idEncargadoPrestamo = enc.idUsuario', 'left');
        $this->db->order_by('p.fechaPrestamo', 'DESC');
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
        $this->db->select('
            p.idPrestamo,
            p.fechaPrestamo,
            p.horaInicio,
            p.horaDevolucion,
            pub.titulo,
            u.nombres as nombreLector,
            u.apellidoPaterno as apellidoLector,
            u.email,
            e.nombres as nombreEncargado,
            e.apellidoPaterno as apellidoEncargado,
            ds.observaciones,
            ed.nombreEditorial
        ');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
        $this->db->join('EDITORIAL ed', 'pub.idEditorial = ed.idEditorial');
        $this->db->join('USUARIO u', 'sp.idUsuario = u.idUsuario');
        $this->db->join('USUARIO e', 'p.idEncargadoPrestamo = e.idUsuario');
        $this->db->where('p.idPrestamo', $idPrestamo);
        return $this->db->get()->row_array();
    }
    public function get_prestamos_activos() {
        $this->db->select('
            p.idPrestamo,
            p.fechaPrestamo,
            p.horaInicio,
            p.estadoPrestamo,
            p.estado,
            u.nombres,
            u.apellidoPaterno,
            pub.titulo,
            ds.observaciones,
            e.nombreEditorial,
            t.nombreTipo
        ');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = p.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
        $this->db->join('USUARIO u', 'sp.idUsuario = u.idUsuario');
        $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
        $this->db->join('EDITORIAL e', 'pub.idEditorial = e.idEditorial');
        $this->db->join('TIPO t', 'pub.idTipo = t.idTipo');
        $this->db->where('p.estado', 1);
        $this->db->where('p.estadoPrestamo', ESTADO_PRESTAMO_ACTIVO);
        return $this->db->get()->result();
    }

    public function get_prestamo($idPrestamo) {
        $this->db->select('
            p.idPrestamo,
            p.fechaPrestamo,
            p.horaInicio,
            p.horaDevolucion,
            p.estadoPrestamo,
            p.estado,
            ds.observaciones,
            pub.titulo,
            pub.ubicacionFisica
        ');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = p.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
        $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
        $this->db->where('p.idPrestamo', $idPrestamo);
        return $this->db->get()->row();
    }

    public function actualizar_estado_prestamo($idPrestamo, $estado) {
        $this->db->trans_start();
        
        $data = [
            'estado' => $estado,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $this->session->userdata('idUsuario')
        ];
        
        $this->db->where('idPrestamo', $idPrestamo);
        $this->db->update('PRESTAMO', $data);
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    

    public function set_fecha_devolucion_real($idPrestamo, $fecha) {
        $this->db->trans_start();
        
        $data = [
            'horaDevolucion' => date('H:i:s', strtotime($fecha)),
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $this->session->userdata('idUsuario')
        ];
        
        $this->db->where('idPrestamo', $idPrestamo);
        $this->db->update('PRESTAMO', $data);
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_prestamos_usuario($idUsuario) {
        $this->db->select('
            p.idPrestamo,
            p.fechaPrestamo,
            p.horaInicio,
            p.horaDevolucion,
            p.estadoPrestamo,
            pub.titulo,
            pub.ubicacionFisica,
            ds.observaciones,
            e.nombreEditorial,
            t.nombreTipo
        ');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = p.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
        $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
        $this->db->join('EDITORIAL e', 'pub.idEditorial = e.idEditorial');
        $this->db->join('TIPO t', 'pub.idTipo = t.idTipo');
        $this->db->where('sp.idUsuario', $idUsuario);
        $this->db->order_by('p.fechaPrestamo', 'DESC');
        return $this->db->get()->result();
    }
    
    
    public function obtener_prestamos_activos_usuario($idUsuario) {
        $this->db->select('
            p.idPrestamo,
            p.fechaPrestamo,
            p.horaInicio,
            p.estadoPrestamo,
            pub.titulo,
            ds.observaciones,
            e.nombreEditorial
        ');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = p.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
        $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
        $this->db->join('EDITORIAL e', 'pub.idEditorial = e.idEditorial');
        $this->db->where([
            'sp.idUsuario' => $idUsuario,
            'p.estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
            'p.estado' => 1
        ]);
        return $this->db->get()->result();
    }
    public function contar_prestamos_activos() {
        $this->db->where([
            'DATE(fechaCreacion)' => date('Y-m-d'),
            'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
            'horaDevolucion IS NULL' => null,
            'estado' => 1
        ]);
        return $this->db->count_all_results('PRESTAMO');
    }

    public function contar_prestamos_activos_usuario($idUsuario) {
        $this->db->select('COUNT(p.idPrestamo) as total');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
        $this->db->where([
            'sp.idUsuario' => $idUsuario,
            'p.estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
            'p.horaDevolucion IS NULL' => null,
            'p.estado' => 1
        ]);
        return $this->db->get()->row()->total;
    }
    public function contar_prestamos_no_devueltos() {
        $this->db->select('COUNT(p.idPrestamo) as total');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
        $this->db->where([
            'p.estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
            'p.horaDevolucion IS NULL' => null,
            'p.estado' => 1
        ]);
        return $this->db->get()->row()->total;
    }

}