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
    /*public function aprobar_solicitud($idSolicitud, $idEncargado) {
        $this->db->select('SP.*, U.nombres as nombresLector, U.apellidoPaterno as apellidoLector, U.carnet, U.profesion, P.titulo, P.fechaPublicacion, P.ubicacionFisica, E.nombreEditorial, ENC.nombres as nombresEncargado, ENC.apellidoPaterno as apellidoEncargado');
        $this->db->from('SOLICITUD_PRESTAMO SP');
        $this->db->join('USUARIO U', 'SP.idUsuario = U.idUsuario');
        $this->db->join('PUBLICACION P', 'SP.idPublicacion = P.idPublicacion');
        $this->db->join('EDITORIAL E', 'P.idEditorial = E.idEditorial');
        $this->db->join('USUARIO ENC', 'ENC.idUsuario = ' . $idEncargado);
        $this->db->where('SP.idSolicitud', $idSolicitud);
        $solicitud = $this->db->get()->row();
    
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
        $fechaPrestamo = date('Y-m-d H:i:s');
        $data_prestamo = [
            'idSolicitud' => $idSolicitud,
            'idUsuario' => $solicitud->idUsuario,
            'idPublicacion' => $solicitud->idPublicacion,
            'idEncargadoPrestamo' => $idEncargado,
            'fechaPrestamo' => $fechaPrestamo,
            'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
            'horaInicio' => date('H:i:s'),
            'estado' => 1,
            'fechaCreacion' => $fechaPrestamo,
            'idUsuarioCreador' => $idEncargado
        ];
    
        $this->db->insert('PRESTAMO', $data_prestamo);
        $idPrestamo = $this->db->insert_id();
    
        // Actualizar el estado de la publicación
        $this->db->where('idPublicacion', $solicitud->idPublicacion);
        $this->db->update('PUBLICACION', [
            'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
            'fechaActualizacion' => $fechaPrestamo,
            'idUsuarioCreador' => $idEncargado
        ]);
    
        $datos_ficha = [
            'idPrestamo' => $idPrestamo,
            'nombreEditorial' => $solicitud->nombreEditorial,
            'fechaPublicacion' => $solicitud->fechaPublicacion,
            'ubicacionFisica' => $solicitud->ubicacionFisica,
            'titulo' => $solicitud->titulo,
            'nombreCompletoLector' => $solicitud->nombresLector . ' ' . $solicitud->apellidoLector,
            'carnet' => $solicitud->carnet,
            'profesion' => $solicitud->profesion,
            'fechaPrestamo' => $fechaPrestamo,
            'nombreCompletoEncargado' => $solicitud->nombresEncargado . ' ' . $solicitud->apellidoEncargado
        ];
    
        $this->db->trans_complete();
    
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
    
        return $datos_ficha;
    }*/
    /*public function aprobar_solicitud($idSolicitud, $idEncargado) {
        $this->db->trans_start();

        // Obtener detalles de la solicitud y publicación
        $this->db->select('SP.idSolicitud, SP.idUsuario, SP.idPublicacion, SP.estadoSolicitud, U.nombres as nombresLector, U.apellidoPaterno as apellidoLector, U.carnet, U.profesion, P.titulo, P.fechaPublicacion, P.ubicacionFisica, E.nombreEditorial, ENC.nombres as nombresEncargado, ENC.apellidoPaterno as apellidoEncargado');
        $this->db->from('SOLICITUD_PRESTAMO SP');
        $this->db->join('USUARIO U', 'SP.idUsuario = U.idUsuario');
        $this->db->join('PUBLICACION P', 'SP.idPublicacion = P.idPublicacion');
        $this->db->join('EDITORIAL E', 'P.idEditorial = E.idEditorial');
        $this->db->join('USUARIO ENC', 'ENC.idUsuario = ' . $idEncargado);
        $this->db->where('SP.idSolicitud', $idSolicitud);
        $solicitud = $this->db->get()->row();

        if (!$solicitud) {
            $this->db->trans_rollback();
            return false;
        }

        // Verificar el estado de la solicitud
        if (!isset($solicitud->estadoSolicitud) || $solicitud->estadoSolicitud != ESTADO_SOLICITUD_PENDIENTE) {
            $this->db->trans_rollback();
            return false;
        }

        // Actualizar la solicitud aprobada
        $fechaActual = date('Y-m-d H:i:s');
        $this->db->where('idSolicitud', $idSolicitud);
        $this->db->update('SOLICITUD_PRESTAMO', [
            'estadoSolicitud' => ESTADO_SOLICITUD_APROBADA,
            'fechaAprobacionRechazo' => $fechaActual,
            'fechaActualizacion' => $fechaActual,
            'idUsuarioCreador' => $idEncargado
        ]);

        // Rechazar otras solicitudes pendientes para la misma publicación
        $this->db->where('idPublicacion', $solicitud->idPublicacion);
        $this->db->where('idSolicitud !=', $idSolicitud);
        $this->db->where('estadoSolicitud', ESTADO_SOLICITUD_PENDIENTE);
        $this->db->update('SOLICITUD_PRESTAMO', [
            'estadoSolicitud' => ESTADO_SOLICITUD_RECHAZADA,
            'fechaAprobacionRechazo' => $fechaActual,
            'fechaActualizacion' => $fechaActual,
            'idUsuarioCreador' => $idEncargado
        ]);

        // Crear el préstamo
        $data_prestamo = [
            'idSolicitud' => $idSolicitud,
            'idUsuario' => $solicitud->idUsuario,
            'idPublicacion' => $solicitud->idPublicacion,
            'idEncargadoPrestamo' => $idEncargado,
            'fechaPrestamo' => $fechaActual,
            'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
            'horaInicio' => date('H:i:s'),
            'estado' => 1,
            'fechaCreacion' => $fechaActual,
            'idUsuarioCreador' => $idEncargado
        ];
        $this->db->insert('PRESTAMO', $data_prestamo);
        $idPrestamo = $this->db->insert_id();

        // Actualizar el estado de la publicación
        $this->db->where('idPublicacion', $solicitud->idPublicacion);
        $this->db->update('PUBLICACION', [
            'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
            'fechaActualizacion' => $fechaActual,
            'idUsuarioCreador' => $idEncargado
        ]);

        $datos_ficha = [
            'idPrestamo' => $idPrestamo,
            'nombreEditorial' => $solicitud->nombreEditorial,
            'fechaPublicacion' => $solicitud->fechaPublicacion,
            'ubicacionFisica' => $solicitud->ubicacionFisica,
            'titulo' => $solicitud->titulo,
            'nombreCompletoLector' => $solicitud->nombresLector . ' ' . $solicitud->apellidoLector,
            'carnet' => $solicitud->carnet,
            'profesion' => $solicitud->profesion,
            'fechaPrestamo' => $fechaActual,
            'nombreCompletoEncargado' => $solicitud->nombresEncargado . ' ' . $solicitud->apellidoEncargado
        ];

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return $datos_ficha;
    }*/
    public function aprobar_solicitud($idSolicitud, $idEncargado) {
        $this->db->trans_start();
    
        // Obtener detalles de la solicitud y publicación
        $this->db->select('SP.idSolicitud, SP.idUsuario, SP.idPublicacion, SP.estadoSolicitud, U.nombres as nombresLector, U.apellidoPaterno as apellidoLector, U.carnet, U.profesion, P.titulo, P.fechaPublicacion, P.ubicacionFisica, E.nombreEditorial, ENC.nombres as nombresEncargado, ENC.apellidoPaterno as apellidoEncargado');
        $this->db->from('SOLICITUD_PRESTAMO SP');
        $this->db->join('USUARIO U', 'SP.idUsuario = U.idUsuario');
        $this->db->join('PUBLICACION P', 'SP.idPublicacion = P.idPublicacion');
        $this->db->join('EDITORIAL E', 'P.idEditorial = E.idEditorial');
        $this->db->join('USUARIO ENC', 'ENC.idUsuario = ' . $idEncargado);
        $this->db->where('SP.idSolicitud', $idSolicitud);
        $solicitud = $this->db->get()->row();
    
        if (!$solicitud || $solicitud->estadoSolicitud != ESTADO_SOLICITUD_PENDIENTE) {
            $this->db->trans_rollback();
            return false;
        }
    
        $fechaActual = date('Y-m-d H:i:s');
    
        // Actualizar la solicitud aprobada
        $this->db->where('idSolicitud', $idSolicitud);
        $this->db->update('SOLICITUD_PRESTAMO', [
            'estadoSolicitud' => ESTADO_SOLICITUD_APROBADA,
            'fechaAprobacionRechazo' => $fechaActual,
            'fechaActualizacion' => $fechaActual,
            'idUsuarioCreador' => $idEncargado
        ]);
    
        // Notificar al usuario cuya solicitud fue aprobada
        $mensaje_aprobacion = "Tu solicitud de préstamo para la publicación '{$solicitud->titulo}' ha sido aprobada.";
        $this->Notificacion_model->crear_notificacion($solicitud->idUsuario, $solicitud->idPublicacion, NOTIFICACION_APROBACION_PRESTAMO, $mensaje_aprobacion);
    
        // Rechazar otras solicitudes pendientes para la misma publicación
        $this->db->select('idSolicitud, idUsuario');
        $this->db->where('idPublicacion', $solicitud->idPublicacion);
        $this->db->where('idSolicitud !=', $idSolicitud);
        $this->db->where('estadoSolicitud', ESTADO_SOLICITUD_PENDIENTE);
        $solicitudes_rechazadas = $this->db->get('SOLICITUD_PRESTAMO')->result();
    
        foreach ($solicitudes_rechazadas as $solicitud_rechazada) {
            // Actualizar estado de la solicitud rechazada
            $this->db->where('idSolicitud', $solicitud_rechazada->idSolicitud);
            $this->db->update('SOLICITUD_PRESTAMO', [
                'estadoSolicitud' => ESTADO_SOLICITUD_RECHAZADA,
                'fechaAprobacionRechazo' => $fechaActual,
                'fechaActualizacion' => $fechaActual,
                'idUsuarioCreador' => $idEncargado
            ]);
    
            // Notificar al usuario cuya solicitud fue rechazada
            $mensaje_rechazo = "Tu solicitud de préstamo para la publicación '{$solicitud->titulo}' ha sido rechazada debido a que otra solicitud fue aprobada.";
            $this->Notificacion_model->crear_notificacion($solicitud_rechazada->idUsuario, $solicitud->idPublicacion, NOTIFICACION_RECHAZO_PRESTAMO, $mensaje_rechazo);
        }
    
        // Crear el préstamo
        $data_prestamo = [
            'idSolicitud' => $idSolicitud,
            'idUsuario' => $solicitud->idUsuario,
            'idPublicacion' => $solicitud->idPublicacion,
            'idEncargadoPrestamo' => $idEncargado,
            'fechaPrestamo' => $fechaActual,
            'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
            'horaInicio' => date('H:i:s'),
            'estado' => 1,
            'fechaCreacion' => $fechaActual,
            'idUsuarioCreador' => $idEncargado
        ];
        $this->db->insert('PRESTAMO', $data_prestamo);
        $idPrestamo = $this->db->insert_id();
    
        // Actualizar el estado de la publicación
        $this->db->where('idPublicacion', $solicitud->idPublicacion);
        $this->db->update('PUBLICACION', [
            'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
            'fechaActualizacion' => $fechaActual,
            'idUsuarioCreador' => $idEncargado
        ]);
    
        $datos_ficha = [
            'idPrestamo' => $idPrestamo,
            'nombreEditorial' => $solicitud->nombreEditorial,
            'fechaPublicacion' => $solicitud->fechaPublicacion,
            'ubicacionFisica' => $solicitud->ubicacionFisica,
            'titulo' => $solicitud->titulo,
            'nombreCompletoLector' => $solicitud->nombresLector . ' ' . $solicitud->apellidoLector,
            'carnet' => $solicitud->carnet,
            'profesion' => $solicitud->profesion,
            'fechaPrestamo' => $fechaActual,
            'nombreCompletoEncargado' => $solicitud->nombresEncargado . ' ' . $solicitud->apellidoEncargado
        ];
    
        $this->db->trans_complete();
    
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
    
        return $datos_ficha;
    }
    public function obtener_solicitud($idSolicitud) {
        $this->db->select('s.*, p.titulo, u.nombres, u.apellidoPaterno');
        $this->db->from('SOLICITUD_PRESTAMO s');
        $this->db->join('PUBLICACION p', 'p.idPublicacion = s.idPublicacion');
        $this->db->join('USUARIO u', 'u.idUsuario = s.idUsuario');
        $this->db->where('s.idSolicitud', $idSolicitud);
        return $this->db->get()->row();
    }
    
}