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

        try {
            $data_solicitud = array(
                'idUsuario' => $idUsuario,
                'fechaSolicitud' => date('Y-m-d H:i:s'),
                'estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
                'estado' => 1,
                'fechaCreacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $idUsuario
            );

            $this->db->insert('SOLICITUD_PRESTAMO', $data_solicitud);
            $idSolicitud = $this->db->insert_id();

            $data_detalle = array(
                'idSolicitud' => $idSolicitud,
                'idPublicacion' => $idPublicacion
            );

            $this->db->insert('DETALLE_SOLICITUD', $data_detalle);

            $this->db->trans_complete();
            return $this->db->trans_status() ? $idSolicitud : false;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error al crear solicitud: ' . $e->getMessage());
            return false;
        }
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

        $this->db->select('
            sp.idSolicitud,
            sp.fechaSolicitud,
            sp.estadoSolicitud,
            sp.idUsuario,
            u.nombres,
            u.apellidoPaterno,
            ds.idPublicacion,
            p.titulo,
            p.ubicacionFisica,
            ds.observaciones,
            e.nombreEditorial
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('USUARIO u', 'sp.idUsuario = u.idUsuario');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
        $this->db->join('EDITORIAL e', 'p.idEditorial = e.idEditorial');
        $this->db->where([
            'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'sp.estado' => 1
        ]);
        $this->db->order_by('sp.fechaSolicitud', 'ASC');
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
        $this->db->join('DETALLE_SOLICITUD', 'DETALLE_SOLICITUD.idSolicitud = SOLICITUD_PRESTAMO.idSolicitud');
        $this->db->join('PUBLICACION', 'PUBLICACION.idPublicacion = DETALLE_SOLICITUD.idPublicacion');
        $this->db->where('SOLICITUD_PRESTAMO.idUsuario', $idUsuario);
        $this->db->where('SOLICITUD_PRESTAMO.estadoSolicitud', 'pendiente'); // Estado pendiente
        $query = $this->db->get();

        return $query->result();
    }
    public function contar_solicitudes_pendientes() {
        if (!$this->_verificar_rol(['administrador', 'encargado'])) {
            return 0;
        }

        $this->db->select('COUNT(sp.idSolicitud) as total');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->where([
            'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'sp.estado' => 1
        ]);
        return $this->db->get()->row()->total;
    }

    public function contar_solicitudes_pendientes_usuario($idUsuario) {
        $this->db->select('COUNT(sp.idSolicitud) as total');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->where([
            'sp.idUsuario' => $idUsuario,
            'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'sp.estado' => 1
        ]);
        return $this->db->get()->row()->total;
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
        if (!$this->_verificar_rol(['lector'])) {
            return [];
        }

        $this->db->select('
            sp.idSolicitud,
            sp.fechaSolicitud,
            sp.estadoSolicitud,
            sp.idUsuario,
            ds.idPublicacion,
            p.titulo,
            p.ubicacionFisica,
            ds.observaciones,
            e.nombreEditorial,
            t.nombreTipo
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
        $this->db->join('EDITORIAL e', 'p.idEditorial = e.idEditorial');
        $this->db->join('TIPO t', 'p.idTipo = t.idTipo');
        $this->db->where('sp.idUsuario', $idUsuario);
        $this->db->order_by('sp.fechaSolicitud', 'DESC');
        return $this->db->get()->result();
    }
    public function obtener_detalle_solicitud($idSolicitud) {
        $this->db->select('
            sp.idSolicitud,
            sp.fechaSolicitud,
            sp.estadoSolicitud,
            sp.idUsuario,
            sp.fechaAprobacionRechazo,
            u.nombres,
            u.apellidoPaterno,
            u.carnet,
            p.titulo,
            p.fechaPublicacion,
            p.ubicacionFisica,
            e.nombreEditorial,
            ds.observaciones,
            t.nombreTipo
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('USUARIO u', 'sp.idUsuario = u.idUsuario');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
        $this->db->join('EDITORIAL e', 'p.idEditorial = e.idEditorial');
        $this->db->join('TIPO t', 'p.idTipo = t.idTipo');
        $this->db->where([
            'sp.idSolicitud' => $idSolicitud,
            'sp.estado' => 1
        ]);
        
        return $this->db->get()->row();
    }

    public function obtener_solicitud($idSolicitud) {
        $this->db->select('
            sp.idSolicitud,
            sp.fechaSolicitud,
            sp.estadoSolicitud,
            sp.idUsuario,
            p.idPublicacion,
            p.titulo,
            ds.observaciones
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
        $this->db->where([
            'sp.idSolicitud' => $idSolicitud,
            'sp.estado' => 1
        ]);
        
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
        $this->db->trans_start();
    
        try {
            // Verificar si la solicitud existe y está pendiente
            $solicitud = $this->db->get_where('SOLICITUD_PRESTAMO', [
                'idSolicitud' => $idSolicitud,
                'estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
                'estado' => 1
            ])->row();
    
            if (!$solicitud) {
                log_message('error', 'Solicitud no encontrada o no está pendiente. ID: ' . $idSolicitud);
                return false;
            }
    
            // Obtener detalles de la solicitud
            $this->db->select('
                sp.idSolicitud,
                sp.idUsuario,
                sp.estadoSolicitud,
                ds.idPublicacion,
                ds.observaciones,
                pub.titulo,
                pub.fechaPublicacion,
                pub.estado as estado_publicacion,
                pub.ubicacionFisica,
                e.nombreEditorial,
                u.nombres,
                u.apellidoPaterno,
                u.carnet,
                u.profesion
            ', FALSE);
            $this->db->from('SOLICITUD_PRESTAMO sp');
            $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
            $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
            $this->db->join('EDITORIAL e', 'pub.idEditorial = e.idEditorial');
            $this->db->join('USUARIO u', 'sp.idUsuario = u.idUsuario');
            $this->db->where('sp.idSolicitud', $idSolicitud);
            
            $publicaciones = $this->db->get()->result();
    
            if (empty($publicaciones)) {
                log_message('error', 'No se encontraron publicaciones para la solicitud ID: ' . $idSolicitud);
                return false;
            }
    
            $fechaActual = date('Y-m-d H:i:s');
            $horaActual = date('H:i:s');
    
            // Verificar disponibilidad de todas las publicaciones
            foreach ($publicaciones as $publicacion) {
                if ($publicacion->estado_publicacion != ESTADO_PUBLICACION_DISPONIBLE) {
                    $this->db->trans_rollback();
                    log_message('error', 'Publicación no disponible ID: ' . $publicacion->idPublicacion);
                    return false;
                }
            }
    
            // Actualizar estado de la solicitud
            $this->db->where('idSolicitud', $idSolicitud);
            $this->db->update('SOLICITUD_PRESTAMO', [
                'estadoSolicitud' => ESTADO_SOLICITUD_APROBADA,
                'fechaAprobacionRechazo' => $fechaActual,
                'fechaActualizacion' => $fechaActual,
                'idUsuarioCreador' => $idEncargado
            ]);
    
            // Crear préstamos para cada publicación
            foreach ($publicaciones as $publicacion) {
                $data_prestamo = [
                    'idSolicitud' => $idSolicitud,
                    'idEncargadoPrestamo' => $idEncargado,
                    'fechaPrestamo' => $fechaActual,
                    'horaInicio' => $horaActual,
                    'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
                    'estado' => 1,
                    'fechaCreacion' => $fechaActual,
                    'idUsuarioCreador' => $idEncargado
                ];
    
                $this->db->insert('PRESTAMO', $data_prestamo);
    
                // Actualizar estado de la publicación
                $this->db->where('idPublicacion', $publicacion->idPublicacion);
                $this->db->update('PUBLICACION', [
                    'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
                    'fechaActualizacion' => $fechaActual,
                    'idUsuarioCreador' => $idEncargado
                ]);
            }
    
            $this->db->trans_complete();
            return $this->db->trans_status() ? $publicaciones : false;
    
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error al aprobar solicitud: ' . $e->getMessage());
            return false;
        }
    }
    
    public function crear_solicitud_multiple($idUsuario, $publicaciones) {
        $this->db->trans_start();
        
        try {
            // Crear la solicitud principal
            $data_solicitud = array(
                'idUsuario' => $idUsuario,
                'fechaSolicitud' => date('Y-m-d H:i:s'),
                'estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
                'estado' => 1,
                'fechaCreacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $idUsuario
            );
            
            $this->db->insert('SOLICITUD_PRESTAMO', $data_solicitud);
            $idSolicitud = $this->db->insert_id();
            
            // Insertar cada publicación en el detalle
            foreach ($publicaciones as $idPublicacion) {
                // Verificar que la publicación esté disponible
                $publicacion = $this->db->get_where('PUBLICACION', [
                    'idPublicacion' => $idPublicacion,
                    'estado' => ESTADO_PUBLICACION_DISPONIBLE
                ])->row();
                
                if (!$publicacion) {
                    $this->db->trans_rollback();
                    return ['success' => false, 'message' => 'Una o más publicaciones no están disponibles'];
                }
                
                $data_detalle = array(
                    'idSolicitud' => $idSolicitud,
                    'idPublicacion' => $idPublicacion,
                    'observaciones' => '' // Opcional, puede ser NULL
                );
                
                $this->db->insert('DETALLE_SOLICITUD', $data_detalle);
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                return ['success' => false, 'message' => 'Error al procesar la solicitud'];
            }
            
            return ['success' => true, 'idSolicitud' => $idSolicitud];
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error al crear solicitud múltiple: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno del servidor'];
        }
    }

    public function verificar_disponibilidad_multiple($publicaciones) {
        $this->db->select('idPublicacion, estado');
        $this->db->from('PUBLICACION');
        $this->db->where_in('idPublicacion', $publicaciones);
        $result = $this->db->get()->result();
        
        $no_disponibles = [];
        foreach ($result as $publicacion) {
            if ($publicacion->estado !== ESTADO_PUBLICACION_DISPONIBLE) {
                $no_disponibles[] = $publicacion->idPublicacion;
            }
        }
        
        return $no_disponibles;
    }
    
    public function obtener_detalle_solicitud_multiple($idSolicitud) {
        $this->db->select('
            sp.idSolicitud,
            sp.fechaSolicitud, 
            sp.estadoSolicitud,
            sp.fechaAprobacionRechazo,
            sp.idUsuario,
            u.nombres,
            u.apellidoPaterno,
            u.carnet,
            u.profesion,
            p.idPublicacion,
            p.titulo,
            p.fechaPublicacion,
            p.ubicacionFisica,
            e.nombreEditorial
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('USUARIO u', 'sp.idUsuario = u.idUsuario');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
        $this->db->join('EDITORIAL e', 'p.idEditorial = e.idEditorial');
        $this->db->where('sp.idSolicitud', $idSolicitud);
        $this->db->where('sp.estado', 1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() === 0) {
            log_message('error', 'No se encontraron detalles para la solicitud ID: ' . $idSolicitud);
            return false;
        }
        
        return $query->result();
    }

    public function obtener_resumen_solicitud_multiple($idSolicitud) {
        $detalles = $this->obtener_detalle_solicitud_multiple($idSolicitud);
        
        if (empty($detalles)) {
            return false;
        }
        
        // Obtener información del encargado que procesa la solicitud
        $idEncargado = $this->session->userdata('idUsuario');
        $encargado = $this->db->select('nombres, apellidoPaterno')
                             ->from('USUARIO')
                             ->where('idUsuario', $idEncargado)
                             ->get()
                             ->row();
        
        // Formatear datos para la ficha de préstamo
        $resumen = [
            'idSolicitud' => $idSolicitud,
            'fechaSolicitud' => $detalles[0]->fechaSolicitud,
            'estadoSolicitud' => $detalles[0]->estadoSolicitud,
            'nombreCompletoLector' => $detalles[0]->nombres . ' ' . $detalles[0]->apellidoPaterno,
            'carnet' => $detalles[0]->carnet,
            'profesion' => $detalles[0]->profesion,
            'fechaPrestamo' => date('Y-m-d H:i:s'),
            'nombreCompletoEncargado' => $encargado ? ($encargado->nombres . ' ' . $encargado->apellidoPaterno) : 'No especificado',
            'publicaciones' => $detalles
        ];
        
        // Agregar información de seguimiento
        log_message('info', 'Generando resumen de solicitud múltiple ID: ' . $idSolicitud . 
                    ' para lector: ' . $resumen['nombreCompletoLector'] . 
                    ' con ' . count($detalles) . ' publicaciones');
        
        return $resumen;
    }
    public function verificar_disponibilidad_solicitud($idSolicitud) {
        $this->db->select('p.estado, p.idPublicacion');
        $this->db->from('DETALLE_SOLICITUD ds');
        $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
        $this->db->where('ds.idSolicitud', $idSolicitud);
        
        $publicaciones = $this->db->get()->result();
        
        foreach ($publicaciones as $pub) {
            if ($pub->estado != ESTADO_PUBLICACION_DISPONIBLE) {
                log_message('warning', 'Publicación ID: ' . $pub->idPublicacion . 
                           ' no está disponible para la solicitud ID: ' . $idSolicitud);
                return false;
            }
        }
        
        return true;
    }
    public function actualizar_estado_publicaciones_solicitud($idSolicitud, $nuevoEstado) {
        $this->db->trans_start();
        
        try {
            // Obtener todas las publicaciones de la solicitud
            $this->db->select('p.idPublicacion');
            $this->db->from('DETALLE_SOLICITUD ds');
            $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
            $this->db->where('ds.idSolicitud', $idSolicitud);
            
            $publicaciones = $this->db->get()->result();
            
            foreach ($publicaciones as $pub) {
                $this->db->where('idPublicacion', $pub->idPublicacion);
                $this->db->update('PUBLICACION', [
                    'estado' => $nuevoEstado,
                    'fechaActualizacion' => date('Y-m-d H:i:s'),
                    'idUsuarioCreador' => $this->session->userdata('idUsuario')
                ]);
            }
            
            $this->db->trans_complete();
            return $this->db->trans_status();
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error al actualizar estado de publicaciones: ' . $e->getMessage());
            return false;
        }
    }    
    public function registrar_historial_solicitud($datos) {
        return $this->db->insert('HISTORIAL_SOLICITUD', $datos);
    }
    
}