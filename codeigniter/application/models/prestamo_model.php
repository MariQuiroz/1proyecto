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


    public function finalizar_prestamo($idPrestamo, $idEncargado, $estadoDevolucion = ESTADO_DEVOLUCION_BUENO) {
        $this->db->trans_start();
    
        try {
            $this->db->select('
                p.idPrestamo,
                p.estadoPrestamo,
                ds.idPublicacion,
                sp.idUsuario
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
                'estadoDevolucion' => $estadoDevolucion,
                'idEncargadoDevolucion' => $idEncargado,
                'horaDevolucion' => date('H:i:s'),
                'fechaActualizacion' => $fechaActual,
                'idUsuarioCreador' => $this->session->userdata('idUsuario')
            ];
    
            $this->db->where('idPrestamo', $idPrestamo);
            $this->db->update('PRESTAMO', $data_update);
    
            // Determinar el nuevo estado de la publicación basado en el estado de devolución
            $nuevoEstadoPublicacion = ESTADO_PUBLICACION_DISPONIBLE;
            if ($estadoDevolucion == ESTADO_DEVOLUCION_DAÑADO || 
                $estadoDevolucion == ESTADO_DEVOLUCION_PERDIDO) {
                $nuevoEstadoPublicacion = ESTADO_PUBLICACION_EN_MANTENIMIENTO;
            }
    
            // Actualizar estado de la publicación
            $this->db->where('idPublicacion', $prestamo->idPublicacion);
            $this->db->update('PUBLICACION', [
                'estado' => $nuevoEstadoPublicacion,
                'fechaActualizacion' => $fechaActual
            ]);
    
            $this->db->trans_complete();
            return $prestamo;
    
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error al finalizar préstamo: ' . $e->getMessage());
            return false;
        }
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
        $this->db->select('
            p.idPrestamo,
            p.idSolicitud,
            p.fechaPrestamo,
            p.horaInicio,
            p.horaDevolucion,
            p.estadoPrestamo,
            sp.idUsuario,
            pub.titulo,
            pub.idPublicacion,
            ds.observaciones
        ');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
        $this->db->where('p.idPrestamo', $idPrestamo);
        return $this->db->get()->row();
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
        // Primera consulta para datos básicos
        $this->db->select('
            p.idPrestamo,
            p.fechaPrestamo,
            p.horaInicio,
            p.horaDevolucion,
            p.estadoDevolucion,
            u.nombres as nombreLector,
            u.apellidoPaterno as apellidoLector,
            u.email,
            enc.nombres as nombreEncargado,
            enc.apellidoPaterno as apellidoEncargado
        ');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
        $this->db->join('USUARIO u', 'sp.idUsuario = u.idUsuario');
        $this->db->join('USUARIO enc', 'p.idEncargadoPrestamo = enc.idUsuario');
        $this->db->where('p.idPrestamo', $idPrestamo);
        
        $prestamo = $this->db->get()->row_array();
        
        if ($prestamo) {
            // Segunda consulta para publicaciones
            $this->db->select('
                pub.titulo,
                pub.fechaPublicacion,
                pub.ubicacionFisica,
                ed.nombreEditorial,
                ds.observaciones,
                COALESCE(p.estadoDevolucion, ' . ESTADO_DEVOLUCION_BUENO . ') as estadoDevolucion
            ');
            $this->db->from('PRESTAMO p');
            $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
            $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
            $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
            $this->db->join('EDITORIAL ed', 'pub.idEditorial = ed.idEditorial');
            $this->db->where('p.idPrestamo', $idPrestamo);
            
            $prestamo['publicaciones'] = $this->db->get()->result();
    
            // Convertir estados numéricos a texto para la vista
            foreach ($prestamo['publicaciones'] as &$pub) {
                $pub->estadoDevolucionTexto = $this->_obtener_texto_estado_devolucion($pub->estadoDevolucion);
            }
        }
        
        return $prestamo;
    }
    
    private function _obtener_texto_estado_devolucion($estado) {
        switch ($estado) {
            case ESTADO_DEVOLUCION_BUENO:
                return 'Bueno';
            case ESTADO_DEVOLUCION_DAÑADO:
                return 'Dañado';
            case ESTADO_DEVOLUCION_PERDIDO:
                return 'Perdido';
            default:
                return 'Bueno';
        }
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
    public function finalizar_prestamo_multiple($devoluciones, $idEncargado) {
    $this->db->trans_start();

    try {
        $fechaActual = date('Y-m-d H:i:s');
        $horaActual = date('H:i:s');
        
        foreach ($devoluciones as $idPrestamo => $devolucion) {
            // Verificar que el préstamo esté activo
            $prestamo = $this->obtener_prestamo($idPrestamo);
            if (!$prestamo || $prestamo->estadoPrestamo != ESTADO_PRESTAMO_ACTIVO) {
                $this->db->trans_rollback();
                return [
                    'success' => false,
                    'message' => 'Uno o más préstamos no están activos'
                ];
            }

            // Convertir estado de texto a numérico
            $estadoDevolucion = ESTADO_DEVOLUCION_BUENO;
            switch(strtolower($devolucion['estado'])) {
                case 'dañado':
                    $estadoDevolucion = ESTADO_DEVOLUCION_DAÑADO;
                    break;
                case 'perdido':
                    $estadoDevolucion = ESTADO_DEVOLUCION_PERDIDO;
                    break;
            }

            // Actualizar préstamo
            $data_prestamo = [
                'estadoPrestamo' => ESTADO_PRESTAMO_FINALIZADO,
                'estadoDevolucion' => $estadoDevolucion,
                'idEncargadoDevolucion' => $idEncargado,
                'horaDevolucion' => $horaActual,
                'fechaActualizacion' => $fechaActual,
                'idUsuarioCreador' => $idEncargado
            ];

            if (!empty($devolucion['observaciones'])) {
                $data_prestamo['observacionesDevolucion'] = $devolucion['observaciones'];
            }

            $this->db->where('idPrestamo', $idPrestamo);
            $this->db->update('PRESTAMO', $data_prestamo);

            // Determinar estado de publicación
            $estado_publicacion = ESTADO_PUBLICACION_DISPONIBLE;
            if ($estadoDevolucion == ESTADO_DEVOLUCION_DAÑADO || 
                $estadoDevolucion == ESTADO_DEVOLUCION_PERDIDO) {
                $estado_publicacion = ESTADO_PUBLICACION_EN_MANTENIMIENTO;
            }

            $this->db->where('idPublicacion', $prestamo->idPublicacion);
            $this->db->update('PUBLICACION', [
                'estado' => $estado_publicacion,
                'fechaActualizacion' => $fechaActual
            ]);

            // Crear notificación
            $this->load->model('Notificacion_model');
            $mensaje = "Se ha registrado la devolución de la publicación '{$prestamo->titulo}' en estado " . 
                      $this->_obtener_texto_estado_devolucion($estadoDevolucion);
            
            $this->Notificacion_model->crear_notificacion(
                $prestamo->idUsuario,
                $prestamo->idPublicacion,
                NOTIFICACION_DEVOLUCION,
                $mensaje
            );
        }

        $this->db->trans_complete();
        return [
            'success' => $this->db->trans_status(),
            'message' => $this->db->trans_status() ? 
                'Devoluciones procesadas con éxito' : 
                'Error al procesar las devoluciones'
        ];

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Error al procesar devoluciones múltiples: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error interno del servidor'
        ];
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
    $this->db->where([
        'p.estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
        'p.estado' => 1
    ]);
    $this->db->group_by('
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
    $this->db->order_by('p.fechaPrestamo', 'DESC');
    return $this->db->get()->result();
}
}