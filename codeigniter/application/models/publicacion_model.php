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
    public function obtener_publicacion() {
        $this->db->select('
            p.idPublicacion,
            p.idEditorial,
            p.idTipo,
            p.titulo,
            p.fechaPublicacion,
            p.numeroPaginas,
            p.portada,
            p.descripcion,
            p.ubicacionFisica,
            p.estado,
            p.fechaCreacion,
            p.fechaActualizacion,
            e.nombreEditorial,
            t.nombreTipo,
            MAX(sp.estadoSolicitud) as estadoSolicitud,
            MAX(pr.estadoPrestamo) as estadoPrestamo
        ');
    
        $this->db->from('PUBLICACION p');
        $this->db->join('EDITORIAL e', 'e.idEditorial = p.idEditorial');
        $this->db->join('TIPO t', 't.idTipo = p.idTipo');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idPublicacion = p.idPublicacion', 'left');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = ds.idSolicitud AND sp.estado = 1', 'left');
        $this->db->join('PRESTAMO pr', 'pr.idSolicitud = sp.idSolicitud AND pr.estadoPrestamo = 1', 'left');
    
        // Asegurarse de que la publicación no esté eliminada
        $this->db->where('p.estado !=', 0); // Asumiendo que 0 es el estado eliminado
    
        // Agrupar por los campos necesarios
        $this->db->group_by([
            'p.idPublicacion',
            'p.idEditorial',
            'p.idTipo',
            'p.titulo',
            'p.fechaPublicacion',
            'p.numeroPaginas',
            'p.portada',
            'p.descripcion',
            'p.ubicacionFisica',
            'p.estado',
            'p.fechaCreacion',
            'p.fechaActualizacion',
            'e.nombreEditorial',
            't.nombreTipo'
        ]);
    
        $this->db->order_by('p.fechaCreacion', 'DESC');
    
        return $this->db->get()->result();
    }
    public function obtener_nombre_estado($estado) {
        switch($estado) {
            case ESTADO_PUBLICACION_DISPONIBLE:
                return 'Disponible';
            case ESTADO_PUBLICACION_EN_CONSULTA:
                return 'En Consulta';
            case ESTADO_PUBLICACION_EN_MANTENIMIENTO:
                return 'En Mantenimiento';
            case ESTADO_PUBLICACION_ELIMINADO:
                return 'Eliminado';
            default:
                return 'Desconocido';
        }
    }
   
    public function obtener_publicaciones_disponibles() {
        $publicaciones_seleccionadas = $this->session->userdata('publicaciones_seleccionadas') ?: array();
    
        $this->db->select('
            p.idPublicacion,
            p.titulo,
            p.ubicacionFisica,
            p.estado,
            p.portada,
            p.fechaPublicacion,
            p.descripcion,
            e.nombreEditorial,
            t.nombreTipo
        ');
        $this->db->from('PUBLICACION p');
        $this->db->join('EDITORIAL e', 'e.idEditorial = p.idEditorial');
        $this->db->join('TIPO t', 't.idTipo = p.idTipo');
        $this->db->where('p.estado', ESTADO_PUBLICACION_DISPONIBLE);
        
        if (!empty($publicaciones_seleccionadas)) {
            $this->db->where_not_in('p.idPublicacion', $publicaciones_seleccionadas);
        }
        
        return $this->db->get()->result();
    }

    public function listar_publicaciones() {
        $this->db->select('
            p.idPublicacion,
            p.titulo,
            p.fechaPublicacion,
            p.portada,
            p.descripcion,
            p.ubicacionFisica,
            p.estado,
            t.nombreTipo,
            e.nombreEditorial,
            pr.estadoPrestamo,
            sp.estadoSolicitud
        ');
        $this->db->from('PUBLICACION p');
        $this->db->join('TIPO t', 't.idTipo = p.idTipo');
        $this->db->join('EDITORIAL e', 'e.idEditorial = p.idEditorial');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idPublicacion = p.idPublicacion', 'left');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = ds.idSolicitud AND sp.estado = 1', 'left');
        $this->db->join('PRESTAMO pr', 'pr.idSolicitud = sp.idSolicitud AND pr.estadoPrestamo = 1', 'left');
        $this->db->where('p.estado', 1);
        $this->db->order_by('p.fechaCreacion', 'DESC');
         // Filtrar explícitamente las publicaciones eliminadas
    $this->db->where('p.estado !=', ESTADO_PUBLICACION_ELIMINADO);
    
    // Ordenar por fecha de publicación
    $this->db->order_by('p.fechaPublicacion', 'DESC');

        return $this->db->get()->result();
    }

    public function buscar_publicaciones($termino) {
        $this->db->select('
            p.idPublicacion,
            p.titulo,
            p.fechaPublicacion,
            p.portada,
            p.estado,
            t.nombreTipo,
            e.nombreEditorial,
            pr.estadoPrestamo
        ');
        $this->db->from('PUBLICACION p');
        $this->db->join('TIPO t', 't.idTipo = p.idTipo');
        $this->db->join('EDITORIAL e', 'e.idEditorial = p.idEditorial');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idPublicacion = p.idPublicacion', 'left');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = ds.idSolicitud AND sp.estado = 1', 'left');
        $this->db->join('PRESTAMO pr', 'pr.idSolicitud = sp.idSolicitud AND pr.estadoPrestamo = 1', 'left');
        $this->db->where('p.estado', 1);
        $this->db->group_start();
        $this->db->like('p.titulo', $termino);
        $this->db->or_like('e.nombreEditorial', $termino);
        $this->db->or_like('t.nombreTipo', $termino);
        $this->db->group_end();
        return $this->db->get()->result();
    }

    public function cambiar_estado_publicacion($idPublicacion, $nuevoEstado) {
        $this->db->trans_start();
    
        // Verificar si la publicación existe y obtener su estado actual
        $publicacion = $this->obtener_publicacion($idPublicacion);
        if (!$publicacion) {
            $this->db->trans_rollback();
            return ['exito' => false, 'mensaje' => 'Publicación no encontrada.'];
        }
    
        // Evitar cambios innecesarios
        if ($publicacion->estado == $nuevoEstado) {
            $this->db->trans_rollback();
            return ['exito' => false, 'mensaje' => 'El estado de la publicación ya es el especificado.'];
        }
    
        // Verificar si hay préstamos activos
        if ($this->tiene_prestamo_activo($idPublicacion) && $nuevoEstado == ESTADO_PUBLICACION_DISPONIBLE) {
            $this->db->trans_rollback();
            return ['exito' => false, 'mensaje' => 'No se puede cambiar el estado porque tiene préstamos activos.'];
        }
    
        // Determinar datos según el nuevo estado
        $data = [
            'estado' => $nuevoEstado,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $this->session->userdata('idUsuario') ?? null,
        ];
    
        switch ($nuevoEstado) {
            case ESTADO_PUBLICACION_ELIMINADO:
                if ($publicacion->estado == ESTADO_PUBLICACION_EN_CONSULTA || $this->tiene_prestamo_activo($idPublicacion)) {
                    $this->db->trans_rollback();
                    return ['exito' => false, 'mensaje' => 'No se puede eliminar una publicación en consulta o con préstamos activos.'];
                }
            
                break;
    
            case ESTADO_PUBLICACION_EN_CONSULTA:
                if ($publicacion->estado == ESTADO_PUBLICACION_ELIMINADO) {
                    $this->db->trans_rollback();
                    return ['exito' => false, 'mensaje' => 'No se puede poner en consulta una publicación eliminada.'];
                }
                break;
    
            case ESTADO_PUBLICACION_DISPONIBLE:
                if ($publicacion->estado == ESTADO_PUBLICACION_ELIMINADO) {
                    $this->db->trans_rollback();
                    return ['exito' => false, 'mensaje' => 'No se puede restaurar una publicación eliminada.'];
                }
                break;
    
            case ESTADO_PUBLICACION_EN_MANTENIMIENTO:
                if ($this->tiene_prestamo_activo($idPublicacion)) {
                    $this->db->trans_rollback();
                    return ['exito' => false, 'mensaje' => 'No se puede poner en mantenimiento una publicación con préstamos activos.'];
                }
                break;
    
            default:
                $this->db->trans_rollback();
                return ['exito' => false, 'mensaje' => 'Estado no válido.'];
        }
    
        // Realizar el cambio de estado
        $this->db->where('idPublicacion', $idPublicacion);
        $this->db->update('PUBLICACION', $data);
    
        // Registrar en el log del sistema
        log_message('info', 'Cambio de estado de publicación - ID: ' . $idPublicacion . 
                            ' - Estado anterior: ' . $publicacion->estado . 
                            ' - Nuevo estado: ' . $nuevoEstado);
    
        $this->db->trans_complete();
    
        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Error en transacción al cambiar estado de publicación ID: ' . $idPublicacion);
            return ['exito' => false, 'mensaje' => 'Error al cambiar el estado de la publicación.'];
        }
    
        return [
            'exito' => true, 
            'mensaje' => 'Estado actualizado correctamente.',
            'estado_anterior' => $publicacion->estado,
            'nuevo_estado' => $nuevoEstado
        ];
    }
    
    // Función auxiliar para verificar préstamos activos
    private function tiene_prestamo_activo($idPublicacion) {
        $this->db->select('pr.idPrestamo');
        $this->db->from('PUBLICACION p');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idPublicacion = p.idPublicacion');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PRESTAMO pr', 'pr.idSolicitud = sp.idSolicitud');
        $this->db->where([
            'p.idPublicacion' => $idPublicacion,
            'pr.estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO
        ]);
    
        return $this->db->get()->num_rows() > 0;
    }
    
    public function listar_todas_publicaciones() {
        $this->db->select('
            p.idPublicacion,
            p.titulo,
            p.fechaPublicacion,
            p.numeroPaginas,
            p.portada,
            p.descripcion,
            p.ubicacionFisica,
            p.estado,
            p.fechaCreacion,
            p.fechaActualizacion,
            t.nombreTipo,
            e.nombreEditorial,
            MAX(COALESCE(pr.estadoPrestamo, 0)) as estadoPrestamo,
            MAX(COALESCE(sp.estadoSolicitud, 0)) as estadoSolicitud,
            MAX(COALESCE(ds.fechaReserva, NULL)) as fechaReserva,
            MAX(COALESCE(ds.estadoReserva, 0)) as estadoReserva,
            MAX(CASE 
                WHEN sp.estado = 1 THEN sp.idUsuario 
                ELSE NULL 
            END) as idUsuarioSolicitud
        ');
        $this->db->from('PUBLICACION p');
        $this->db->join('TIPO t', 't.idTipo = p.idTipo');
        $this->db->join('EDITORIAL e', 'e.idEditorial = p.idEditorial');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idPublicacion = p.idPublicacion', 'left');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = ds.idSolicitud AND sp.estado = 1', 'left');
        $this->db->join('PRESTAMO pr', 'pr.idSolicitud = sp.idSolicitud AND pr.estadoPrestamo = 1', 'left');
        $this->db->where('p.estado !=', ESTADO_PUBLICACION_ELIMINADO);
        $this->db->group_by([
            'p.idPublicacion',
            'p.titulo',
            'p.fechaPublicacion',
            'p.numeroPaginas',
            'p.portada',
            'p.descripcion',
            'p.ubicacionFisica',
            'p.estado',
            'p.fechaCreacion',
            'p.fechaActualizacion',
            't.nombreTipo',
            'e.nombreEditorial'
        ]);
        $this->db->order_by('p.fechaCreacion', 'DESC');
        
        return $this->db->get()->result();
    }
    public function mapear_estado($estado, $idUsuario = null, $estadoReserva = 0, $idUsuarioSolicitud = null) {
        $estado = intval($estado);
        
        switch ($estado) {
            case ESTADO_PUBLICACION_DISPONIBLE:
                return ['estado' => 'Disponible', 'clase' => 'text-success'];
                
            case ESTADO_PUBLICACION_EN_CONSULTA:
                if ($idUsuarioSolicitud && $idUsuarioSolicitud == $idUsuario) {
                    return ['estado' => 'En préstamo por ti', 'clase' => 'text-primary'];
                }
                return ['estado' => 'En consulta', 'clase' => 'text-warning'];
                
            case ESTADO_PUBLICACION_EN_MANTENIMIENTO:
                return ['estado' => 'En mantenimiento', 'clase' => 'text-danger'];
                
            case ESTADO_PUBLICACION_RESERVADA:
                if ($estadoReserva && $idUsuarioSolicitud == $idUsuario) {
                    return ['estado' => 'Reservada por ti', 'clase' => 'text-info'];
                }
                return ['estado' => 'Reservada', 'clase' => 'text-warning'];
                
            case ESTADO_PUBLICACION_ELIMINADO:
                return ['estado' => 'No disponible', 'clase' => 'text-muted'];
                
            default:
                return ['estado' => 'Estado desconocido', 'clase' => 'text-secondary'];
        }
    }

    public function obtener_publicaciones_disponibles_no_seleccionadas($publicaciones_seleccionadas) {
        $this->db->select('
            p.idPublicacion,
            p.titulo,
            p.ubicacionFisica,
            p.estado,
            p.portada,
            p.fechaPublicacion,
            p.descripcion,
            e.nombreEditorial,
            t.nombreTipo
        ');
        $this->db->from('PUBLICACION p');
        $this->db->join('EDITORIAL e', 'e.idEditorial = p.idEditorial');
        $this->db->join('TIPO t', 't.idTipo = p.idTipo');
        $this->db->where('p.estado', ESTADO_PUBLICACION_DISPONIBLE);
        
        if (!empty($publicaciones_seleccionadas)) {
            $this->db->where_not_in('p.idPublicacion', $publicaciones_seleccionadas);
        }
        
        return $this->db->get()->result();
    }
  // Método adicional para validar una única publicación
public function validar_disponibilidad($idPublicacion) {
    $this->db->select('
        p.estado,
        COUNT(pr.idPrestamo) as prestamos_activos
    ');
    $this->db->from('PUBLICACION p');
    $this->db->join('DETALLE_SOLICITUD ds', 'ds.idPublicacion = p.idPublicacion', 'left');
    $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = ds.idSolicitud AND sp.estado = 1', 'left');
    $this->db->join('PRESTAMO pr', 'pr.idSolicitud = sp.idSolicitud AND pr.estadoPrestamo = 1', 'left');
    $this->db->where('p.idPublicacion', $idPublicacion);
    $this->db->group_by('p.idPublicacion, p.estado');
    
    $result = $this->db->get()->row();
    
    if (!$result) {
        return false;
    }
    
    return $result->estado === ESTADO_PUBLICACION_DISPONIBLE && $result->prestamos_activos == 0;
}
    // En el modelo Publicacion_model.php

public function obtener_publicacion_detallada($idPublicacion) {
    log_message('debug', "Obteniendo detalles de publicación ID: {$idPublicacion}");
    
    $this->db->select('
        p.idPublicacion,
        p.titulo,
        p.ubicacionFisica,
        p.estado,
        p.portada,
        p.fechaPublicacion,
        p.descripcion,
        e.nombreEditorial,
        t.nombreTipo
    ');
    $this->db->from('PUBLICACION p');
    $this->db->join('EDITORIAL e', 'e.idEditorial = p.idEditorial');
    $this->db->join('TIPO t', 't.idTipo = p.idTipo');
    $this->db->where('p.idPublicacion', $idPublicacion);
    
    $resultado = $this->db->get()->row();
    log_message('debug', 'Query ejecutado: ' . $this->db->last_query());
    log_message('debug', 'Resultado: ' . ($resultado ? 'encontrado' : 'no encontrado'));
    
    if ($resultado) {
        log_message('debug', 'Estado de la publicación: ' . $resultado->estado);
    }
    
    return $resultado;
}

public function reservar_publicacion($idPublicacion, $idUsuario) {
    $this->db->trans_start();
    
    // Verificar si la publicación está disponible
    $publicacion = $this->obtener_publicacion($idPublicacion);
    if (!$publicacion || $publicacion->estado != ESTADO_PUBLICACION_DISPONIBLE) {
        return false;
    }

    // Actualizar estado de la publicación
    $data = array(
        'estado' => ESTADO_PUBLICACION_DISPONIBLE, // Mantenemos el estado disponible
        'reservadoPor' => $idUsuario,
        'fechaReserva' => date('Y-m-d H:i:s'),
        'fechaActualizacion' => date('Y-m-d H:i:s')
    );

    $this->db->where('idPublicacion', $idPublicacion);
    $this->db->update('PUBLICACION', $data);

    $this->db->trans_complete();
    return $this->db->trans_status();
}

public function liberar_reserva($idPublicacion) {
    $this->db->trans_start();

    $data = array(
        'reservadoPor' => null,
        'fechaReserva' => null,
        'fechaActualizacion' => date('Y-m-d H:i:s')
    );

    $this->db->where('idPublicacion', $idPublicacion);
    $this->db->update('PUBLICACION', $data);

    $this->db->trans_complete();
    return $this->db->trans_status();
}

public function obtener_estado_personalizado($idPublicacion, $idUsuario) {
    $this->db->select('
        p.estado,
        p.idPublicacion,
        sp.idUsuario as usuario_solicitud,
        sp.estadoSolicitud,
        pr.estadoPrestamo,
        pr.idSolicitud as prestamo_activo,
        spr.idUsuario as usuario_prestamo
    ');
    $this->db->from('PUBLICACION p');
    $this->db->join('DETALLE_SOLICITUD ds', 'ds.idPublicacion = p.idPublicacion', 'left');
    $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = ds.idSolicitud AND sp.estado = 1', 'left');
    $this->db->join('PRESTAMO pr', 'pr.idSolicitud = sp.idSolicitud AND pr.estadoPrestamo = ' . ESTADO_PRESTAMO_ACTIVO, 'left');
    $this->db->join('SOLICITUD_PRESTAMO spr', 'spr.idSolicitud = pr.idSolicitud', 'left');
    $this->db->where('p.idPublicacion', $idPublicacion);
    
    $resultado = $this->db->get()->row();

    if (!$resultado) {
        return 'Estado desconocido';
    }

    // Si hay un préstamo activo
    if ($resultado->prestamo_activo) {
        // Si el préstamo es del usuario actual
        if ($resultado->usuario_prestamo == $idUsuario) {
            return 'En préstamo por ti';
        }
        // Si el préstamo es de otro usuario
        return 'En consulta';
    }

    // Si hay una solicitud pendiente
    if ($resultado->estadoSolicitud == ESTADO_SOLICITUD_PENDIENTE) {
        if ($resultado->usuario_solicitud == $idUsuario) {
            $tiempoRestante = $this->calcular_tiempo_restante_reserva($resultado->fechaCreacion);
            if ($tiempoRestante > 0) {
                return 'Reservado por ti - ' . $this->formatear_tiempo_restante($tiempoRestante);
            }
        } else {
            return 'En Reserva';
        }
    }

    // Estados normales
    switch($resultado->estado) {
        case ESTADO_PUBLICACION_DISPONIBLE:
            return 'Disponible';
        case ESTADO_PUBLICACION_EN_CONSULTA:
            return 'En consulta';
        case ESTADO_PUBLICACION_EN_MANTENIMIENTO:
            return 'En mantenimiento';
        default:
            return 'Estado desconocido';
    }
}

public function verificar_disponibilidad($idPublicacion, $idUsuario) {
    $this->db->select('
        p.estado,
        sp.idUsuario as usuario_reserva,
        sp.fechaCreacion as fecha_reserva,
        sp.estadoSolicitud
    ');
    $this->db->from('PUBLICACION p');
    $this->db->join('DETALLE_SOLICITUD ds', 'ds.idPublicacion = p.idPublicacion', 'left');
    $this->db->join('SOLICITUD_PRESTAMO sp', 
        'sp.idSolicitud = ds.idSolicitud AND sp.estadoSolicitud = ' . ESTADO_SOLICITUD_PENDIENTE, 
        'left');
    $this->db->where('p.idPublicacion', $idPublicacion);
    
    $publicacion = $this->db->get()->row();

    if (!$publicacion) {
        return false;
    }

    // Si hay una reserva temporal activa
    if ($publicacion->usuario_reserva) {
        // Si la reserva es del usuario actual, verificar tiempo límite
        if ($publicacion->usuario_reserva == $idUsuario) {
            return $this->calcular_tiempo_restante_reserva($publicacion->fecha_reserva) > 0;
        }
        // Si la reserva es de otro usuario
        return false;
    }

    return $publicacion->estado == ESTADO_PUBLICACION_DISPONIBLE;
}

private function calcular_tiempo_restante_reserva($fechaReserva) {
    $tiempoLimite = strtotime($fechaReserva) + (2 * 3600); // 2 horas en segundos
    return max(0, $tiempoLimite - time());
}

private function formatear_tiempo_restante($segundos) {
    if ($segundos <= 0) {
        return '0 minutos';
    }
    
    $horas = floor($segundos / 3600);
    $minutos = floor(($segundos % 3600) / 60);
    
    $resultado = '';
    if ($horas > 0) {
        $resultado .= $horas . ' hora' . ($horas > 1 ? 's' : '') . ' ';
    }
    if ($minutos > 0) {
        $resultado .= $minutos . ' minuto' . ($minutos > 1 ? 's' : '');
    }
    
    return trim($resultado);
}

public function limpiar_reservas_expiradas() {
    $tiempoLimite = date('Y-m-d H:i:s', strtotime('-2 hours'));
    
    $this->db->select('sp.idSolicitud, sp.idUsuario, ds.idPublicacion');
    $this->db->from('SOLICITUD_PRESTAMO sp');
    $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
    $this->db->where([
        'sp.estadoSolicitud' => ESTADO_SOLICITUD_PENDIENTE,
        'sp.fechaCreacion <' => $tiempoLimite,
        'sp.estado' => 1
    ]);

    $reservas_expiradas = $this->db->get()->result();

    foreach ($reservas_expiradas as $reserva) {
        $this->db->trans_start();

        // Actualizar estado de la solicitud
        $this->db->where('idSolicitud', $reserva->idSolicitud);
        $this->db->update('SOLICITUD_PRESTAMO', [
            'estadoSolicitud' => ESTADO_SOLICITUD_CANCELADA,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'observaciones' => 'Cancelada automáticamente por tiempo expirado'
        ]);

        // Notificar al usuario
        $this->Notificacion_model->crear_notificacion(
            $reserva->idUsuario,
            $reserva->idPublicacion,
            NOTIFICACION_CANCELACION_TIEMPO,
            'Su reserva ha expirado por exceder el tiempo límite.'
        );

        $this->db->trans_complete();
    }
}

public function cancelar_solicitud($idSolicitud, $idUsuario) {
    $this->db->trans_start();
    log_message('debug', "Iniciando cancelación de solicitud ID: {$idSolicitud}");

    try {
        // Verificar que la solicitud existe y pertenece al usuario
        $this->db->select('
            sp.idSolicitud,
            sp.estadoSolicitud,
            sp.idUsuario,
            ds.idPublicacion,
            p.titulo
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
        $this->db->join('PUBLICACION p', 'p.idPublicacion = ds.idPublicacion');
        $this->db->where([
            'sp.idSolicitud' => $idSolicitud,
            'sp.idUsuario' => $idUsuario,
            'sp.estado' => 1
        ]);

        $solicitud = $this->db->get()->row();

        if (!$solicitud) {
            throw new Exception('No se encontró la solicitud o no tienes permiso para cancelarla.');
        }

        // Verificar que la solicitud esté en un estado que permita cancelación
        $estados_permitidos = [
            ESTADO_SOLICITUD_PENDIENTE,  // Para reservas temporales
            ESTADO_SOLICITUD_APROBADA    // Para solicitudes aprobadas pero sin préstamo iniciado
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
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'observaciones' => 'Cancelada por el usuario',
            'idUsuarioCreador' => $idUsuario
        ];

        $this->db->where('idSolicitud', $idSolicitud);
        $this->db->update('SOLICITUD_PRESTAMO', $data_actualizacion);

        // Si la solicitud estaba en estado aprobado, actualizar la publicación a disponible
        if ($solicitud->estadoSolicitud == ESTADO_SOLICITUD_APROBADA) {
            $this->db->where('idPublicacion', $solicitud->idPublicacion);
            $this->db->update('PUBLICACION', [
                'estado' => ESTADO_PUBLICACION_DISPONIBLE,
                'fechaActualizacion' => date('Y-m-d H:i:s')
            ]);
        }

        // Registrar la cancelación en el historial (si existe una tabla de historial)
        $data_historial = [
            'idSolicitud' => $idSolicitud,
            'idUsuario' => $idUsuario,
            'accion' => 'CANCELACION',
            'fechaAccion' => date('Y-m-d H:i:s'),
            'detalles' => 'Cancelación realizada por el usuario'
        ];

        // Si tienes una tabla de historial, descomentar la siguiente línea
        // $this->db->insert('HISTORIAL_SOLICITUD', $data_historial);

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
            if ($solicitud->estadoSolicitud == ESTADO_SOLICITUD_APROBADA) {
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
public function obtener_publicaciones_seleccionadas($ids) {
    if (empty($ids)) {
        return array();
    }

    $this->db->select('
        p.idPublicacion,
        p.titulo,
        p.ubicacionFisica,
        p.estado,
        p.portada,
        p.fechaPublicacion,
        p.descripcion,
        p.numeroPaginas,
        e.nombreEditorial,
        t.nombreTipo'
    );
    $this->db->from('PUBLICACION p');
    $this->db->join('EDITORIAL e', 'e.idEditorial = p.idEditorial');
    $this->db->join('TIPO t', 't.idTipo = p.idTipo');
    
    // Si ids es un array, usamos where_in
    if (is_array($ids)) {
        $this->db->where_in('p.idPublicacion', $ids);
    } else {
        // Si es un solo id, usamos where
        $this->db->where('p.idPublicacion', $ids);
    }
    
    // Verificar que las publicaciones estén activas y disponibles
    $this->db->where('p.estado', ESTADO_PUBLICACION_DISPONIBLE);

    $result = $this->db->get();

    // Verificar si hubo error en la consulta
    if (!$result) {
        log_message('error', 'Error al obtener publicaciones seleccionadas: ' . $this->db->error()['message']);
        return array();
    }

    return $result->result();
}

// Método complementario para verificar disponibilidad
public function verificar_disponibilidad_multiple($ids) {
    if (empty($ids)) {
        return array();
    }

    $this->db->select('
        p.idPublicacion,
        p.titulo,
        p.estado,
        CASE
            WHEN p.estado != ' . ESTADO_PUBLICACION_DISPONIBLE . ' THEN true
            WHEN pr.idPrestamo IS NOT NULL THEN true
            ELSE false
        END as no_disponible
    ');
    $this->db->from('PUBLICACION p');
    $this->db->join('DETALLE_SOLICITUD ds', 'ds.idPublicacion = p.idPublicacion', 'left');
    $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = ds.idSolicitud AND sp.estado = 1', 'left');
    $this->db->join('PRESTAMO pr', 'pr.idSolicitud = sp.idSolicitud AND pr.estadoPrestamo = 1', 'left');
    $this->db->where_in('p.idPublicacion', $ids);
    
    $result = $this->db->get()->result();
    
    $no_disponibles = array();
    foreach ($result as $pub) {
        if ($pub->no_disponible) {
            $no_disponibles[] = array(
                'idPublicacion' => $pub->idPublicacion,
                'titulo' => $pub->titulo,
                'razon' => ($pub->estado != ESTADO_PUBLICACION_DISPONIBLE) ? 
                    'La publicación no está disponible' : 
                    'La publicación está en préstamo'
            );
        }
    }
    
    return $no_disponibles;
}


}