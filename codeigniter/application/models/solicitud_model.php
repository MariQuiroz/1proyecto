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

             // Establecer la zona horaria correcta para Bolivia
        date_default_timezone_set('America/La_Paz');
        
        // Crear objeto DateTime para manejar las fechas correctamente
        $fechaHoraActual = new DateTime();

            $data_solicitud = array(
                'idUsuario' => $idUsuario,
                'fechaSolicitud' => $fechaHoraActual->format('Y-m-d'),
                'estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
                'estado' => 1,
                'fechaCreacion' => $fechaHoraActual->format('Y-m-d H:i:s'),
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
    
        // Mejorar la selección de campos específicos evitando *
        $this->db->select('
            sp.idSolicitud,
            sp.fechaSolicitud,
            sp.estadoSolicitud,
            sp.idUsuario,
            u.nombres,
            u.apellidoPaterno,
            u.carnet,                    
            ds.idPublicacion,
            ds.fechaExpiracionReserva,   
            ds.estadoReserva,            
            ds.observaciones,
            p.titulo,
            p.ubicacionFisica,
            e.nombreEditorial,
            TIMESTAMPDIFF(MINUTE, sp.fechaSolicitud, NOW()) as minutos_transcurridos
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
    
        $resultado = $this->db->get()->result();
    
        // Agregar cálculo de tiempo restante para cada solicitud
        foreach ($resultado as $solicitud) {
            if (!isset($solicitud->fechaExpiracionReserva)) {
                $solicitud->fechaExpiracionReserva = date('Y-m-d H:i:s', 
                    strtotime($solicitud->fechaSolicitud . ' +2 minutes'));
            }
        }
    
        return $resultado;
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
    
        // Verificar y procesar solicitudes expiradas primero
        $this->verificar_y_procesar_expiraciones();
    
        // Consulta principal con información detallada
        $this->db->select('
            sp.idSolicitud,
            sp.fechaSolicitud,
            sp.estadoSolicitud,
            sp.idUsuario,
            ds.idPublicacion,
            ds.fechaExpiracionReserva,
            ds.observaciones,
            p.titulo,
            p.ubicacionFisica,
            p.estado as estadoPublicacion,
            e.nombreEditorial,
            t.nombreTipo
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
        $this->db->join('EDITORIAL e', 'p.idEditorial = e.idEditorial');
        $this->db->join('TIPO t', 'p.idTipo = t.idTipo');
        $this->db->where([
            'sp.idUsuario' => $idUsuario,
            'sp.estado' => 1
        ]);
        $this->db->order_by('sp.fechaSolicitud', 'DESC');
    
        $solicitudes = $this->db->get()->result();
    
        // Procesar información adicional para cada solicitud
        foreach ($solicitudes as &$solicitud) {
            $solicitud->tiempo_restante = null;
            $solicitud->estado_texto = $this->_mapear_estado_solicitud($solicitud->estadoSolicitud);
            $solicitud->estado_clase = $this->_mapear_clase_estado($solicitud->estadoSolicitud);
    
            if ($solicitud->estadoSolicitud == ESTADO_SOLICITUD_PENDIENTE && $solicitud->fechaExpiracionReserva) {
                $solicitud->tiempo_restante = $this->_calcular_tiempo_restante($solicitud->fechaExpiracionReserva);
            }
        }
    
        return $solicitudes;
    }
    
    // Métodos privados auxiliares
    private function _mapear_estado_solicitud($estado) {
        $estados = [
            ESTADO_SOLICITUD_PENDIENTE => 'Pendiente',
            ESTADO_SOLICITUD_APROBADA => 'Aprobada',
            ESTADO_SOLICITUD_RECHAZADA => 'Rechazada',
            ESTADO_SOLICITUD_FINALIZADA => 'Finalizada',
            ESTADO_SOLICITUD_EXPIRADA => 'Expirada',
            ESTADO_SOLICITUD_CANCELADA => 'Cancelada'
        ];
        
        return isset($estados[$estado]) ? $estados[$estado] : 'Desconocido';
    }
    
    private function _mapear_clase_estado($estado) {
        $clases = [
            ESTADO_SOLICITUD_PENDIENTE => 'badge-warning',
            ESTADO_SOLICITUD_APROBADA => 'badge-success',
            ESTADO_SOLICITUD_RECHAZADA => 'badge-danger',
            ESTADO_SOLICITUD_FINALIZADA => 'badge-info',
            ESTADO_SOLICITUD_EXPIRADA => 'badge-secondary',
            ESTADO_SOLICITUD_CANCELADA => 'badge-dark'
        ];
        
        return isset($clases[$estado]) ? $clases[$estado] : 'badge-secondary';
    }
    
    private function _calcular_tiempo_restante($fecha_expiracion) {
        $tiempo_expiracion = strtotime($fecha_expiracion);
        $tiempo_actual = time();
        $tiempo_restante = $tiempo_expiracion - $tiempo_actual;
    
        if ($tiempo_restante <= 0) {
            return [
                'segundos' => 0,
                'formato' => 'Expirado',
                'expirado' => true
            ];
        }
    
        $minutos = floor($tiempo_restante / 60);
        $segundos = $tiempo_restante % 60;
    
        return [
            'segundos' => $tiempo_restante,
            'formato' => sprintf('%02d:%02d', $minutos, $segundos),
            'expirado' => false
        ];
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
        log_message('debug', "=== Iniciando aprobación de solicitud {$idSolicitud} ===");
    
        try {

           

            // Verificar solicitud existente
            $solicitud = $this->db->get_where('SOLICITUD_PRESTAMO', [
                'idSolicitud' => $idSolicitud,
                'estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
                'estado' => 1
            ])->row();
    
            if (!$solicitud) {
                log_message('error', "Solicitud {$idSolicitud} no encontrada o no pendiente");
                return false;
            }
            
            // Obtener detalles con logging
            $this->db->select('
                sp.idSolicitud,
                sp.idUsuario,
                sp.estadoSolicitud,
                ds.idPublicacion,
                ds.idDetalleSolicitud,
                ds.observaciones,
                pub.titulo,
                pub.estado as estado_publicacion
            ');
            $this->db->from('SOLICITUD_PRESTAMO sp');
            $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
            $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
            $this->db->where('sp.idSolicitud', $idSolicitud);
            
            $publicaciones = $this->db->get()->result();
            log_message('debug', "Publicaciones encontradas para solicitud {$idSolicitud}: " . count($publicaciones));
            
            // Rastrear publicaciones únicas
            $publicacionesProcesadas = [];
            $publicacionesUnicas = [];
            
            foreach ($publicaciones as $publicacion) {
                log_message('debug', "Procesando publicación ID: {$publicacion->idPublicacion}, Título: {$publicacion->titulo}");
                
                if (isset($publicacionesProcesadas[$publicacion->idPublicacion])) {
                    log_message('warning', "Publicación duplicada encontrada - ID: {$publicacion->idPublicacion}");
                    continue;
                }

                // Verificar si ya existe un préstamo activo
                $prestamoExistente = $this->db->get_where('PRESTAMO', [
                    'idSolicitud' => $idSolicitud,
                    'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO
                ])->row();

                if ($prestamoExistente) {
                    log_message('warning', "Préstamo existente encontrado para solicitud {$idSolicitud}");
                    continue;
                }
                
                if ($publicacion->estado_publicacion != ESTADO_PUBLICACION_DISPONIBLE) {
                    log_message('error', "Publicación {$publicacion->idPublicacion} no disponible");
                    $this->db->trans_rollback();
                    return false;
                }
                
                $publicacionesProcesadas[$publicacion->idPublicacion] = true;
                $publicacionesUnicas[] = $publicacion;
                
                log_message('debug', "Publicación {$publicacion->idPublicacion} agregada a lista única");
            }
            
            log_message('info', "Total publicaciones únicas a procesar: " . count($publicacionesUnicas));
            
           // Establecer la zona horaria correcta para Bolivia
        date_default_timezone_set('America/La_Paz');
        
        // Crear objeto DateTime para manejar las fechas correctamente
        $fechaHoraActual = new DateTime();
            
            // Actualizar estado de solicitud
            $this->db->where('idSolicitud', $idSolicitud);
            $this->db->update('SOLICITUD_PRESTAMO', [
                'estadoSolicitud' => ESTADO_SOLICITUD_APROBADA,
                'fechaAprobacionRechazo' => $fechaHoraActual->format('Y-m-d'),
                'fechaActualizacion' => $fechaHoraActual->format('Y-m-d H:i:s'),
                'idUsuarioCreador' =>$fechaHoraActual->format('Y-m-d H:i:s')
            ]);
            
            // Procesar cada publicación única
            foreach ($publicacionesUnicas as $pub) {
                log_message('debug', "Creando préstamo para publicación {$pub->idPublicacion}");
                
                $data_prestamo = [
                    'idSolicitud' => $idSolicitud,
                    'idEncargadoPrestamo' => $idEncargado,
                    'fechaPrestamo' => $fechaHoraActual->format('Y-m-d'),
                    'horaInicio' => $fechaHoraActual->format('H:i:s'),
                    'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
                    'estado' => 1,
                    'fechaCreacion' =>$fechaHoraActual->format('Y-m-d H:i:s'),
                    'idUsuarioCreador' => $idEncargado
                ];
                
                $this->db->insert('PRESTAMO', $data_prestamo);
                log_message('debug', "Préstamo creado para publicación {$pub->idPublicacion}");
                
                // Actualizar estado publicación
                $this->db->where('idPublicacion', $pub->idPublicacion);
                $this->db->update('PUBLICACION', [
                    'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
                    'fechaActualizacion' => $fechaHoraActual->format('Y-m-d H:i:s'),
                    'idUsuarioCreador' => $idEncargado
                ]);
                log_message('debug', "Estado de publicación {$pub->idPublicacion} actualizado");
            }
            
            $this->db->trans_complete();
            log_message('info', "=== Finalizada aprobación de solicitud {$idSolicitud} ===");
            
            return $this->db->trans_status() ? $publicacionesUnicas : false;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', "Error en aprobación de solicitud {$idSolicitud}: " . $e->getMessage());
            return false;
        }
    }
    
    public function crear_solicitud_multiple($idUsuario, $publicaciones) {
        $this->db->trans_start();
        
        try {
            // Establecer la zona horaria correcta para Bolivia
        date_default_timezone_set('America/La_Paz');
        
        // Crear objeto DateTime para manejar las fechas correctamente
        $fechaHoraActual = new DateTime();
        
            // Crear la solicitud principal
            $data_solicitud = array(
                'idUsuario' => $idUsuario,
                'fechaSolicitud' => $fechaHoraActual->format('Y-m-d H:i:s'),
                'estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
                'estado' => 1,
                'fechaCreacion' => $fechaHoraActual->format('Y-m-d H:i:s'),
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

            // Establecer la zona horaria correcta para Bolivia
        date_default_timezone_set('America/La_Paz');
        
        // Crear objeto DateTime para manejar las fechas correctamente
        $fechaHoraActual = new DateTime();
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
                    'fechaActualizacion' => $fechaHoraActual->format('Y-m-d H:i:s'),
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
    public function tiene_solicitud_pendiente($idPublicacion) {
        $this->db->select('sp.idSolicitud');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->where([
            'ds.idPublicacion' => $idPublicacion,
            'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'sp.estado' => 1
        ]);
        return $this->db->get()->num_rows() > 0;
    }

    public function crear_solicitud_individual($data_solicitud, $idPublicacion) {
        $this->db->trans_start();

        try {
            // Insertar la solicitud principal
            $this->db->insert('SOLICITUD_PRESTAMO', $data_solicitud);
            $idSolicitud = $this->db->insert_id();

            // Insertar el detalle de la solicitud
            $detalle = array(
                'idSolicitud' => $idSolicitud,
                'idPublicacion' => $idPublicacion,
                'observaciones' => 'Solicitud pendiente de aprobación'
            );
            $this->db->insert('DETALLE_SOLICITUD', $detalle);

            $this->db->trans_complete();
            return $this->db->trans_status() ? $idSolicitud : false;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error al crear solicitud individual: ' . $e->getMessage());
            return false;
        }
    }

    public function verificar_tiempo_limite() {
        $fecha_actual = date('Y-m-d H:i:s');
        
        // Obtener solicitudes que han excedido el tiempo límite
        $this->db->select('sp.idSolicitud, sp.idUsuario, ds.idPublicacion');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->where([
            'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'sp.estado' => 1,
            'sp.fechaLimiteAprobacion <' => $fecha_actual
        ]);

        $solicitudes_vencidas = $this->db->get()->result();

        foreach ($solicitudes_vencidas as $solicitud) {
            // Actualizar estado de la solicitud
            $this->db->where('idSolicitud', $solicitud->idSolicitud);
            $this->db->update('SOLICITUD_PRESTAMO', [
                'estadoSolicitud' => ESTADO_SOLICITUD_CANCELADA,
                'fechaActualizacion' => $fecha_actual,
                'observaciones' => 'Cancelada automáticamente por tiempo límite excedido'
            ]);

            // Liberar la publicación
            $this->db->where('idPublicacion', $solicitud->idPublicacion);
            $this->db->update('PUBLICACION', [
                'estado' => ESTADO_PUBLICACION_DISPONIBLE,
                'fechaActualizacion' => $fecha_actual
            ]);

            // Notificar al usuario
            $this->Notificacion_model->crear_notificacion(
                $solicitud->idUsuario,
                $solicitud->idPublicacion,
                NOTIFICACION_CANCELACION_TIEMPO,
                'Su solicitud ha sido cancelada por exceder el tiempo límite de aprobación.'
            );
        }
    }
  
    public function verificar_disponibilidad_reserva($idPublicacion) {
        // Verificar si hay alguna reserva activa para la publicación
        $tiempoLimite = date('Y-m-d H:i:s', strtotime('-2 minutes'));
        
        $this->db->select('
            sp.idSolicitud,
            sp.idUsuario,
            sp.fechaCreacion,
            u.nombres,
            u.apellidoPaterno
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
        $this->db->join('USUARIO u', 'u.idUsuario = sp.idUsuario');
        $this->db->where([
            'ds.idPublicacion' => $idPublicacion,
            'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'sp.estado' => 1,
            'sp.fechaCreacion >' => $tiempoLimite
        ]);
    
        $reserva = $this->db->get()->row();
    
        if ($reserva) {
            return [
                'disponible' => false,
                'motivo' => 'Reservada por otro usuario',
                'tiempo_restante' => strtotime($tiempoLimite) - strtotime($reserva->fechaCreacion),
                'usuario' => $reserva->nombres . ' ' . $reserva->apellidoPaterno
            ];
        }
    
        return ['disponible' => true];
    }
    
    
    public function formatear_tiempo_restante($segundos) {
        if ($segundos <= 0) {
            return '0 minutos';
        }
    
        $horas = floor($segundos / 3600);
        $minutos = floor(($segundos % 3600) / 60);
        $segundosRestantes = $segundos % 60;
    
        $partes = [];
        if ($horas > 0) {
            $partes[] = $horas . ' hora' . ($horas > 1 ? 's' : '');
        }
        if ($minutos > 0) {
            $partes[] = $minutos . ' minuto' . ($minutos > 1 ? 's' : '');
        }
        if ($segundosRestantes > 0 && count($partes) == 0) {
            $partes[] = $segundosRestantes . ' segundo' . ($segundosRestantes > 1 ? 's' : '');
        }
    
        return implode(', ', $partes);
    }
    public function contar_solicitudes_activas($idUsuario) {
        // Obtener el tiempo límite (2 horas atrás)
        $tiempoLimite = date('Y-m-d H:i:s', strtotime('-2 minutes'));
        
        $this->db->select('COUNT(DISTINCT sp.idSolicitud) as total');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->where([
            'sp.idUsuario' => $idUsuario,
            'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'sp.estado' => 1,
            'sp.fechaCreacion >' => $tiempoLimite
        ]);
    
        $resultado = $this->db->get()->row();
        return $resultado ? $resultado->total : 0;
    }
    
    // Método adicional para contar solicitudes activas por publicación
    public function contar_solicitudes_activas_publicacion($idPublicacion) {
        $tiempoLimite = date('Y-m-d H:i:s', strtotime('-2 minutes'));
        
        $this->db->select('COUNT(DISTINCT sp.idSolicitud) as total');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
        $this->db->where([
            'ds.idPublicacion' => $idPublicacion,
            'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'sp.estado' => 1,
            'sp.fechaCreacion >' => $tiempoLimite
        ]);
    
        $resultado = $this->db->get()->row();
        return $resultado ? $resultado->total : 0;
    }
    
    // Método útil para obtener el resumen de solicitudes activas
    public function obtener_resumen_solicitudes_activas($idUsuario) {
        $tiempoLimite = date('Y-m-d H:i:s', strtotime('-2 minutes'));
        
        $this->db->select('
            sp.idSolicitud,
            sp.fechaCreacion,
            GROUP_CONCAT(p.titulo SEPARATOR ", ") as publicaciones,
            COUNT(ds.idPublicacion) as total_publicaciones
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
        $this->db->join('PUBLICACION p', 'p.idPublicacion = ds.idPublicacion');
        $this->db->where([
            'sp.idUsuario' => $idUsuario,
            'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'sp.estado' => 1,
            'sp.fechaCreacion >' => $tiempoLimite
        ]);
        $this->db->group_by('sp.idSolicitud');
        
        return $this->db->get()->result();
    }
    
    // Método para verificar si se puede crear una nueva solicitud
    public function puede_crear_nueva_solicitud($idUsuario) {
        $solicitudes_activas = $this->contar_solicitudes_activas($idUsuario);
        $limite_solicitudes = 5; // Puedes hacer esto configurable
        
        return [
            'puede_crear' => $solicitudes_activas < $limite_solicitudes,
            'solicitudes_activas' => $solicitudes_activas,
            'solicitudes_restantes' => $limite_solicitudes - $solicitudes_activas,
            'limite_solicitudes' => $limite_solicitudes
        ];
    }  
 

    public function tiene_reserva_activa($idPublicacion) {
        $this->db->where('DETALLE_SOLICITUD.idPublicacion', $idPublicacion);
        $this->db->where('SOLICITUD_PRESTAMO.estadoSolicitud', ESTADO_SOLICITUD_RESERVA_TEMPORAL);
        $this->db->where('SOLICITUD_PRESTAMO.fechaSolicitud >', date('Y-m-d H:i:s', strtotime('-2 hours')));
        $this->db->join('DETALLE_SOLICITUD', 'SOLICITUD_PRESTAMO.idSolicitud = DETALLE_SOLICITUD.idSolicitud');
        return $this->db->get('SOLICITUD_PRESTAMO')->num_rows() > 0;
    }

    public function crear_reserva_temporal($idPublicacion, $idUsuario) {
        $data = array(
            'idUsuario' => $idUsuario,
            'fechaSolicitud' => date('Y-m-d H:i:s'),
            'estadoSolicitud' => ESTADO_SOLICITUD_RESERVA_TEMPORAL,
            'estado' => 1,
            'fechaCreacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idUsuario
        );

        $this->db->insert('SOLICITUD_PRESTAMO', $data);
        $idSolicitud = $this->db->insert_id();

        $data_detalle = array(
            'idSolicitud' => $idSolicitud,
            'idPublicacion' => $idPublicacion,
            'observaciones' => 'Reserva temporal'
        );

        $this->db->insert('DETALLE_SOLICITUD', $data_detalle);
        
        if ($this->db->affected_rows() > 0) {
            return ['exito' => true, 'mensaje' => 'Reserva creada correctamente.'];
        } else {
            return ['exito' => false, 'mensaje' => 'Error al crear la reserva.'];
        }
    }

    public function obtener_tiempo_restante_reserva($idPublicacion) {
        $this->db->select('SOLICITUD_PRESTAMO.fechaSolicitud');
        $this->db->where('DETALLE_SOLICITUD.idPublicacion', $idPublicacion);
        $this->db->where('SOLICITUD_PRESTAMO.estadoSolicitud', ESTADO_SOLICITUD_RESERVA_TEMPORAL);
        $this->db->join('DETALLE_SOLICITUD', 'SOLICITUD_PRESTAMO.idSolicitud = DETALLE_SOLICITUD.idSolicitud');
        $reserva = $this->db->get('SOLICITUD_PRESTAMO')->row();

        if ($reserva) {
            $fechaExpiracion = new DateTime($reserva->fechaSolicitud);
            $fechaExpiracion->add(new DateInterval('PT2M')); // Añadir 2 horas
            $ahora = new DateTime();
            $intervalo = $ahora->diff($fechaExpiracion);
            return $intervalo->format('%H:%I:%S');
        }

        return '00:00:00';
    }


    public function cancelar_solicitud($idSolicitud, $idUsuario) {
        // ...
    }

    public function cancelar_reserva_temporal($idPublicacion, $idUsuario) {
        log_message('debug', "=== INICIO cancelar_reserva_temporal() ===");
        log_message('debug', "Publicación: {$idPublicacion}, Usuario: {$idUsuario}");
    
        $this->db->trans_start();
    
        try {
            // Buscar la reserva temporal
            $this->db->select('
                sp.idSolicitud, 
                ds.idDetalleSolicitud
            ');
            $this->db->from('SOLICITUD_PRESTAMO sp');
            $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
            $this->db->where([
                'ds.idPublicacion' => $idPublicacion,
                'sp.idUsuario' => $idUsuario,
                'sp.estadoSolicitud' => ESTADO_SOLICITUD_RESERVA_TEMPORAL
            ]);
    
            $reserva = $this->db->get()->row();
            
            if ($reserva) {
                log_message('debug', "Reserva encontrada - ID Solicitud: {$reserva->idSolicitud}");
    
                // Eliminar el detalle de la solicitud
                $this->db->where('idDetalleSolicitud', $reserva->idDetalleSolicitud);
                if (!$this->db->delete('DETALLE_SOLICITUD')) {
                    throw new Exception('Error al eliminar el detalle de la solicitud');
                }
    
                // Eliminar la solicitud principal
                $this->db->where('idSolicitud', $reserva->idSolicitud);
                if (!$this->db->delete('SOLICITUD_PRESTAMO')) {
                    throw new Exception('Error al eliminar la solicitud');
                }
    
                $this->db->trans_complete();
                
                if ($this->db->trans_status() === FALSE) {
                    throw new Exception('Error en la transacción');
                }
    
                log_message('debug', "Reserva temporal cancelada exitosamente");
                return true;
            }
    
            log_message('debug', "No se encontró reserva temporal para cancelar");
            $this->db->trans_commit();
            return true; // Retornamos true si no hay reserva que cancelar
    
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', "Error en cancelar_reserva_temporal: " . $e->getMessage());
            return false;
        } finally {
            log_message('debug', "=== FIN cancelar_reserva_temporal() ===");
        }
    }
   
    // Método para cancelar reservas expiradas
    public function cancelar_reservas_expiradas() {
        $fecha_limite = date('Y-m-d H:i:s', time() - TIEMPO_RESERVA);
        
        $this->db->select('sp.idSolicitud, ds.idPublicacion');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->where([
            'sp.estadoSolicitud' => ESTADO_SOLICITUD_RESERVA_TEMPORAL,
            'sp.fechaCreacion <' => $fecha_limite,
            'sp.estado' => 1
        ]);
        
        $reservas_expiradas = $this->db->get()->result();

        foreach ($reservas_expiradas as $reserva) {
            $this->db->trans_start();
            
            // Actualizar la solicitud
            $this->db->where('idSolicitud', $reserva->idSolicitud);
            $this->db->update('SOLICITUD_PRESTAMO', [
                'estadoSolicitud' => ESTADO_SOLICITUD_EXPIRADA,
                'fechaActualizacion' => date('Y-m-d H:i:s')
            ]);

            // Liberar la publicación
            $this->db->where('idPublicacion', $reserva->idPublicacion);
            $this->db->update('PUBLICACION', [
                'estado' => ESTADO_PUBLICACION_DISPONIBLE,
                'fechaActualizacion' => date('Y-m-d H:i:s')
            ]);

            $this->db->trans_complete();
        }

        return count($reservas_expiradas);
    }
    public function verificar_solicitud_existente($idPublicacion, $idUsuario) {
        $this->db->select('sp.idSolicitud');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->where([
            'ds.idPublicacion' => $idPublicacion,
            'sp.idUsuario' => $idUsuario,
            'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'sp.estado' => 1
        ]);
        return $this->db->get()->num_rows() > 0;
    }

    public function verificar_solicitudes_expiradas() {
        $limite_tiempo = date('Y-m-d H:i:s', strtotime('-2 minutes'));
        
        $this->db->select('
            sp.idSolicitud, 
            sp.idUsuario, 
            ds.idPublicacion, 
            p.titulo
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
        $this->db->where([
            'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'sp.fechaCreacion <=' => $limite_tiempo,
            'sp.estado' => 1
        ]);

        $solicitudes_expiradas = $this->db->get()->result();

        foreach ($solicitudes_expiradas as $solicitud) {
            // Actualizar la solicitud a estado rechazada
            $this->db->where('idSolicitud', $solicitud->idSolicitud);
            $this->db->update('SOLICITUD_PRESTAMO', [
                'estadoSolicitud' => ESTADO_SOLICITUD_RECHAZADA,
                'fechaActualizacion' => date('Y-m-d H:i:s')
            ]);

            // Liberar la publicación
            $this->db->where('idPublicacion', $solicitud->idPublicacion);
            $this->db->update('PUBLICACION', [
                'estado' => ESTADO_PUBLICACION_DISPONIBLE,
                'fechaActualizacion' => date('Y-m-d H:i:s')
            ]);
        }

        return $solicitudes_expiradas;
    }
    public function verificar_reserva_vigente($idPublicacion, $idUsuario) {
        // Verificar si ya existe una solicitud activa o pendiente
        $this->db->select('sp.idSolicitud, sp.estadoSolicitud, sp.fechaCreacion');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->where([
            'ds.idPublicacion' => $idPublicacion,
            'sp.estado' => 1
        ]);
        $this->db->where_in('sp.estadoSolicitud', [
            ESTADO_SOLICITUD_PENDIENTE,
            ESTADO_SOLICITUD_APROBADA
        ]);

        $solicitud_existente = $this->db->get()->row();

        if ($solicitud_existente) {
            if ($solicitud_existente->idUsuario == $idUsuario) {
                return false; // El mismo usuario ya tiene una solicitud
            }
            return false; // Otro usuario tiene una solicitud activa
        }

        return true; // No hay solicitudes activas
    }

    public function convertir_reserva_en_solicitud($idPublicacion, $idUsuario) {
        $fecha_actual = date('Y-m-d H:i:s');
        $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+2 minutes'));

        // Verificar estado de la publicación
        $publicacion = $this->db->get_where('PUBLICACION', [
            'idPublicacion' => $idPublicacion,
            'estado' => ESTADO_PUBLICACION_DISPONIBLE
        ])->row();

        if (!$publicacion) {
            return [
                'exito' => false,
                'mensaje' => 'La publicación no está disponible.'
            ];
        }

        // Crear solicitud con ID único
        $data_solicitud = [
            'idUsuario' => $idUsuario,
            'fechaSolicitud' => $fecha_actual,
            'fechaExpiracion' => $fecha_expiracion,
            'estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'estado' => 1,
            'fechaCreacion' => $fecha_actual,
            'idUsuarioCreador' => $idUsuario
        ];

        $this->db->insert('SOLICITUD_PRESTAMO', $data_solicitud);
        $idSolicitud = $this->db->insert_id();

        // Crear detalle de solicitud
        $data_detalle = [
            'idSolicitud' => $idSolicitud,
            'idPublicacion' => $idPublicacion,
            'observaciones' => 'Reserva temporal - Expira: ' . $fecha_expiracion
        ];

        $this->db->insert('DETALLE_SOLICITUD', $data_detalle);

        // Actualizar estado de la publicación a reservada
        $this->db->where('idPublicacion', $idPublicacion);
        $this->db->update('PUBLICACION', [
            'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
            'fechaActualizacion' => $fecha_actual
        ]);

        return [
            'exito' => true,
            'idSolicitud' => $idSolicitud
        ];
    }
    public function tiene_solicitud_activa($idUsuario, $idPublicacion) {
        $this->db->select('sp.idSolicitud');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->where([
            'sp.idUsuario' => $idUsuario,
            'ds.idPublicacion' => $idPublicacion,
            'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'sp.estado' => 1,
            'ds.fechaExpiracionReserva >' => date('Y-m-d H:i:s')
        ]);
        
        return $this->db->get()->num_rows() > 0;
    }
    
    public function registrar_verificacion_expiracion($idSolicitud, $fecha_expiracion) {
        // Registrar en tabla de auditoría o log del sistema
        log_message('info', "Registrada verificación de expiración para solicitud {$idSolicitud} - Expira: {$fecha_expiracion}");
    }
    
    public function verificar_y_procesar_expiraciones() {
        $fecha_actual = date('Y-m-d H:i:s');
        
        // Obtener solicitudes expiradas con información detallada
        $this->db->select('
            sp.idSolicitud, 
            sp.idUsuario, 
            sp.fechaCreacion,
            ds.idPublicacion,
            p.titulo,
            u.nombres,
            u.apellidoPaterno,
            u.email
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
        $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
        $this->db->join('USUARIO u', 'sp.idUsuario = u.idUsuario');
        $this->db->where([
            'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
            'sp.estado' => 1,
            'ds.fechaExpiracionReserva <=' => $fecha_actual
        ]);
        
        $solicitudes_expiradas = $this->db->get()->result();
        
        foreach ($solicitudes_expiradas as $solicitud) {
            $this->db->trans_start();
            
            try {
                // Actualizar estado de solicitud
                $this->db->where('idSolicitud', $solicitud->idSolicitud);
                $this->db->update('SOLICITUD_PRESTAMO', [
                    'estadoSolicitud' => ESTADO_SOLICITUD_EXPIRADA,
                    'fechaActualizacion' => $fecha_actual,
                    'observaciones' => 'Expirada por tiempo límite'
                ]);
                
                // Liberar publicación
                $this->db->where('idPublicacion', $solicitud->idPublicacion);
                $this->db->update('PUBLICACION', [
                    'estado' => ESTADO_PUBLICACION_DISPONIBLE,
                    'fechaActualizacion' => $fecha_actual
                ]);
                
                // Notificar al usuario solicitante
                $mensaje_usuario = sprintf(
                    "Tu solicitud para '%s' ha expirado por exceder el tiempo límite de espera.",
                    $solicitud->titulo
                );
    
                $this->Notificacion_model->crear_notificacion(
                    $solicitud->idUsuario,
                    $solicitud->idPublicacion,
                    NOTIFICACION_SOLICITUD_EXPIRADA,
                    $mensaje_usuario
                );

                // Notificar a usuarios interesados
                $usuarios_interesados = $this->Notificacion_model->obtener_usuarios_interesados_excepto(
                    $solicitud->idPublicacion,
                    $solicitud->idUsuario // Excluimos al usuario cuya solicitud expiró
                );

                foreach ($usuarios_interesados as $usuario) {
                    // Crear notificación de disponibilidad
                    $this->Notificacion_model->crear_notificacion(
                        $usuario->idUsuario,
                        $solicitud->idPublicacion,
                        NOTIFICACION_DISPONIBILIDAD,
                        sprintf(
                            'La publicación "%s" está nuevamente disponible para préstamo.',
                            $solicitud->titulo
                        )
                    );

                    // Actualizar estado del interés a notificado
                    $this->Notificacion_model->actualizar_estado_interes(
                        $usuario->idUsuario,
                        $solicitud->idPublicacion,
                        ESTADO_INTERES_NOTIFICADO
                    );
                }
    
                // Notificar a los encargados
                $encargados = $this->Usuario_model->obtener_encargados_activos();
                $mensaje_encargado = sprintf(
                    "Solicitud expirada\nUsuario: %s %s\nPublicación: %s\nFecha solicitud: %s\nMotivo: Tiempo límite excedido",
                    $solicitud->nombres,
                    $solicitud->apellidoPaterno,
                    $solicitud->titulo,
                    date('d/m/Y H:i', strtotime($solicitud->fechaCreacion))
                );
    
                foreach ($encargados as $encargado) {
                    $this->Notificacion_model->crear_notificacion(
                        $encargado->idUsuario,
                        $solicitud->idPublicacion,
                        NOTIFICACION_SOLICITUD_EXPIRADA,
                        $mensaje_encargado
                    );
                }
                
                $this->db->trans_complete();
                
                if ($this->db->trans_status() === FALSE) {
                    throw new Exception("Error al procesar expiración de solicitud {$solicitud->idSolicitud}");
                }
                
                // Registrar en el log del sistema
                log_message('info', sprintf(
                    'Solicitud %d expirada - Usuario: %s %s, Publicación: %s',
                    $solicitud->idSolicitud,
                    $solicitud->nombres,
                    $solicitud->apellidoPaterno,
                    $solicitud->titulo
                ));
                
            } catch (Exception $e) {
                $this->db->trans_rollback();
                log_message('error', $e->getMessage());
                continue;
            }
        }
    
        return count($solicitudes_expiradas);
    }

    public function tiene_tiempo_valido($idSolicitud) {
        $this->db->select('sp.fechaCreacion');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->where('sp.idSolicitud', $idSolicitud);
        
        $solicitud = $this->db->get()->row();
        
        if (!$solicitud) {
            return false;
        }
        
        $fechaCreacion = new DateTime($solicitud->fechaCreacion);
        $ahora = new DateTime();
        $diferencia = $fechaCreacion->diff($ahora);
        
        // La solicitud es válida si no han pasado más de 2 minutos
        return $diferencia->i < 2;
    }

    public function verificar_expiraciones_pendientes() {
    log_message('debug', "=== INICIO verificar_expiraciones_pendientes() ===");
    $fecha_actual = date('Y-m-d H:i:s');
    
    log_message('debug', "Fecha actual: " . $fecha_actual);

    // Buscar solicitudes pendientes que hayan expirado
    $this->db->select('
        sp.idSolicitud, 
        sp.idUsuario, 
        sp.estadoSolicitud,
        sp.fechaCreacion,
        ds.idPublicacion,
        ds.fechaExpiracionReserva,
        p.titulo'
    );
    $this->db->from('SOLICITUD_PRESTAMO sp');
    $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
    $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
    $this->db->where([
        'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
        'sp.estado' => 1,
        'ds.estadoReserva' => 1,
        'ds.fechaExpiracionReserva <=' => $fecha_actual
    ]);

    $query = $this->db->get();
    log_message('debug', 'Query ejecutada: ' . $this->db->last_query());
    
    $solicitudes_expiradas = $query->result();
    log_message('info', "Solicitudes expiradas encontradas: " . count($solicitudes_expiradas));
    
    $solicitudes_procesadas = [];
    foreach ($solicitudes_expiradas as $solicitud) {
        log_message('debug', "Procesando solicitud ID: {$solicitud->idSolicitud}");
        
        $this->db->trans_start();
        try {
            // 1. Actualizar estado de solicitud
            $data_solicitud = [
                'estadoSolicitud' => ESTADO_SOLICITUD_EXPIRADA,
                'fechaActualizacion' => $fecha_actual,
                'observaciones' => 'Solicitud expirada automáticamente - ' . $fecha_actual
            ];
            
            $this->db->where('idSolicitud', $solicitud->idSolicitud);
            $this->db->update('SOLICITUD_PRESTAMO', $data_solicitud);
            log_message('debug', "Estado de solicitud actualizado a EXPIRADA");
            
            // 2. Liberar publicación
            $data_publicacion = [
                'estado' => ESTADO_PUBLICACION_DISPONIBLE,
                'fechaActualizacion' => $fecha_actual
            ];
            
            $this->db->where('idPublicacion', $solicitud->idPublicacion);
            $this->db->update('PUBLICACION', $data_publicacion);
            log_message('debug', "Publicación {$solicitud->idPublicacion} liberada");

            // 3. Actualizar detalle de solicitud
            $data_detalle = [
                'estadoReserva' => 0,
                'observaciones' => 'Reserva expirada - ' . $fecha_actual
            ];
            
            $this->db->where(['idSolicitud' => $solicitud->idSolicitud]);
            $this->db->update('DETALLE_SOLICITUD', $data_detalle);
            
            // 4. Crear notificación
            $mensaje = "Tu solicitud para '{$solicitud->titulo}' ha expirado por exceder el tiempo límite.";
            $this->Notificacion_model->crear_notificacion(
                $solicitud->idUsuario,
                $solicitud->idPublicacion,
                NOTIFICACION_SOLICITUD_EXPIRADA,
                $mensaje
            );
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception("Error en transacción para solicitud {$solicitud->idSolicitud}");
            }
            
            $solicitudes_procesadas[] = $solicitud;
            log_message('info', "Solicitud {$solicitud->idSolicitud} procesada exitosamente");
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', "Error procesando solicitud {$solicitud->idSolicitud}: " . $e->getMessage());
        }
    }
    
    log_message('debug', "=== FIN verificar_expiraciones_pendientes() ===");
    return $solicitudes_procesadas;
}

public function cancelar_solicitud_enviada($idSolicitud, $idUsuario) {
    $this->db->trans_start();
    
    try {
         // Establecer la zona horaria correcta para Bolivia
         date_default_timezone_set('America/La_Paz');
        
         // Crear objeto DateTime para manejar las fechas correctamente
         $fechaHoraActual = new DateTime();
        // Obtener detalles de la solicitud
        $this->db->select('
            sp.idSolicitud,
            sp.estadoSolicitud,
            sp.idUsuario,
            ds.idPublicacion,
            p.titulo,
            p.estado as estadoPublicacion
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
        $this->db->where([
            'sp.idSolicitud' => $idSolicitud,
            'sp.idUsuario' => $idUsuario,
            'sp.estado' => 1
        ]);

        $solicitud = $this->db->get()->row();

        if (!$solicitud) {
            throw new Exception('No se encontró la solicitud o no tienes permisos para cancelarla.');
        }

        // Verificar que la solicitud esté en un estado que permita cancelación
        $estados_permitidos = [
            ESTADO_SOLICITUD_PENDIENTE,
            //ESTADO_SOLICITUD_APROBADA  // Solo si aún no tiene préstamo activo
        ];

        if (!in_array($solicitud->estadoSolicitud, $estados_permitidos)) {
            throw new Exception('La solicitud no puede ser cancelada en su estado actual.');
        }

        // Verificar que no haya un préstamo activo
        $this->db->select('idPrestamo');
        $this->db->from('PRESTAMO');
        $this->db->where([
            'idSolicitud' => $idSolicitud,
            'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
            'estado' => 1
        ]);

        if ($this->db->get()->num_rows() > 0) {
            throw new Exception('No se puede cancelar una solicitud con préstamo activo.');
        }

        // Actualizar estado de la solicitud
        $data_actualizacion = [
            'estadoSolicitud' => ESTADO_SOLICITUD_CANCELADA,
            'fechaActualizacion' => $fechaHoraActual->format('Y-m-d H:i:s'),
            'observaciones' => 'Cancelada por el usuario',
            'idUsuarioCreador' => $idUsuario
        ];

        $this->db->where('idSolicitud', $idSolicitud);
        $this->db->update('SOLICITUD_PRESTAMO', $data_actualizacion);

        // Si la solicitud estaba en estado PENDIENTE, actualizar la publicación a disponible
        if ($solicitud->estadoSolicitud == ESTADO_SOLICITUD_PENDIENTE) {
            $this->db->where('idPublicacion', $solicitud->idPublicacion);
            $this->db->update('PUBLICACION', [
                'estado' => ESTADO_PUBLICACION_DISPONIBLE,
                'fechaActualizacion' => $fechaHoraActual->format('Y-m-d H:i:s')
            ]);
        }

        // Actualizar el detalle de la solicitud
        $this->db->where([
            'idSolicitud' => $idSolicitud,
            'idPublicacion' => $solicitud->idPublicacion
        ]);
        $this->db->update('DETALLE_SOLICITUD', [
            'observaciones' => 'Solicitud cancelada por el usuario',
            'fechaActualizacion' => $fechaHoraActual->format('Y-m-d H:i:s')
        ]);

        // Crear notificación para el usuario
        if (isset($this->Notificacion_model)) {
            $mensaje = "Has cancelado tu solicitud para: " . $solicitud->titulo;
            $this->Notificacion_model->crear_notificacion(
                $idUsuario,
                $solicitud->idPublicacion,
                NOTIFICACION_CANCELACION_USUARIO,
                $mensaje
            );

            // Si la solicitud estaba aprobada, notificar a los encargados
            if ($solicitud->estadoSolicitud == ESTADO_SOLICITUD_PENDIENTE) {
                $this->notificar_encargados_cancelacion($solicitud);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            throw new Exception('Error en la transacción de la base de datos.');
        }

        log_message('info', "Solicitud {$idSolicitud} cancelada exitosamente por usuario {$idUsuario}");

        return [
            'exito' => true,
            'mensaje' => 'Solicitud cancelada correctamente.'
        ];

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', "Error al cancelar solicitud {$idSolicitud}: " . $e->getMessage());
        
        return [
            'exito' => false,
            'mensaje' => $e->getMessage()
        ];
    }
}

private function notificar_encargados_cancelacion($solicitud) {
    // Obtener encargados activos
    $this->db->select('idUsuario');
    $this->db->from('USUARIO');
    $this->db->where([
        'rol' => 'encargado',
        'estado' => 1
    ]);
    $encargados = $this->db->get()->result();

    $mensaje = "El usuario ha cancelado su solicitud para: " . $solicitud->titulo;

    foreach ($encargados as $encargado) {
        $this->Notificacion_model->crear_notificacion(
            $encargado->idUsuario,
            $solicitud->idPublicacion,
            NOTIFICACION_CANCELACION_USUARIO,
            $mensaje
        );
    }
}
// En Solicitud_model.php
public function procesar_aprobacion($idSolicitud, $idEncargado) {
    $this->db->trans_start();
    
    try {
                // Establecer la zona horaria correcta para Bolivia
                date_default_timezone_set('America/La_Paz');
        
                // Crear objeto DateTime para manejar las fechas correctamente
                $fechaHoraActual = new DateTime();
        
        // Obtener la solicitud
        $this->db->select('sp.idSolicitud, sp.idUsuario, ds.idPublicacion, p.titulo, p.estado')
                 ->from('SOLICITUD_PRESTAMO sp')
                 ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud')
                 ->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion')
                 ->where('sp.idSolicitud', $idSolicitud)
                 ->where('sp.estadoSolicitud', ESTADO_SOLICITUD_PENDIENTE);
        
        $publicaciones = $this->db->get()->result();
        
        if (empty($publicaciones)) {
            throw new Exception('No se encontraron detalles de la solicitud');
        }
        
        foreach ($publicaciones as $pub) {
            // Verificar disponibilidad
            if ($pub->estado != ESTADO_PUBLICACION_DISPONIBLE) {
                continue;
            }
            
            // Crear el préstamo
            $data_prestamo = [
                'idSolicitud' => $idSolicitud,
                'idEncargadoPrestamo' => $idEncargado,
                'fechaPrestamo' => $fechaHoraActual->format('Y-m-d'),
                'horaInicio' =>  $fechaHoraActual->format('H:i:s'),
                'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
                'estado' => 1,
                'fechaCreacion' => $fechaHoraActual->format('Y-m-d H:i:s'),
                'idUsuarioCreador' => $idEncargado
            ];
            
            $this->db->insert('PRESTAMO', $data_prestamo);
            
            // Actualizar estado de la publicación
            $this->db->where('idPublicacion', $pub->idPublicacion)
                     ->update('PUBLICACION', [
                         'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
                         'fechaActualizacion' => $fechaActual
                     ]);
        }
        
        // Actualizar estado de la solicitud
        $this->db->where('idSolicitud', $idSolicitud)
                 ->update('SOLICITUD_PRESTAMO', [
                     'estadoSolicitud' => ESTADO_SOLICITUD_APROBADA,
                     'fechaAprobacionRechazo' => $fechaHoraActual->format('Y-m-d H:i:s'),
                     'fechaActualizacion' => $fechaHoraActual->format('Y-m-d H:i:s'),
                     'idUsuarioCreador' => $idEncargado
                 ]);
        
        $this->db->trans_complete();
        return $this->db->trans_status() ? $publicaciones : false;
        
    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Error en procesar_aprobacion: ' . $e->getMessage());
        return false;
    }
}

public function obtener_solicitud_por_publicacion($idPublicacion, $idUsuario) {
    $this->db->select('
        sp.idSolicitud,
        sp.fechaSolicitud,
        sp.estadoSolicitud,
        sp.idUsuario,
        ds.idPublicacion,
        ds.observaciones
    ');
    $this->db->from('SOLICITUD_PRESTAMO sp');
    $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
    $this->db->where([
        'ds.idPublicacion' => $idPublicacion,
        'sp.idUsuario' => $idUsuario,
        'sp.estado' => 1
    ]);
    $this->db->order_by('sp.fechaSolicitud', 'DESC');
    $this->db->limit(1);
    
    return $this->db->get()->row();
}
}