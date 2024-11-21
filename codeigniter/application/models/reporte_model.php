<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function obtener_estadisticas_profesion($filtros = array()) {
        $this->db->select('
            u.profesion,
            COUNT(DISTINCT u.idUsuario) as total_lectores,
            COUNT(DISTINCT sp.idSolicitud) as total_solicitudes,
            COUNT(DISTINCT p.idPrestamo) as total_prestamos,
            ROUND(AVG(
                CASE 
                    WHEN p.fechaDevolucion IS NOT NULL 
                    THEN TIMESTAMPDIFF(HOUR, p.fechaPrestamo, p.fechaDevolucion)/24.0 
                    ELSE NULL 
                END
            ), 1) as promedio_dias_prestamo
        ');
        $this->db->from('USUARIO u');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idUsuario = u.idUsuario', 'left');
        $this->db->join('PRESTAMO p', 'p.idSolicitud = sp.idSolicitud', 'left');
        
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(sp.fechaSolicitud) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(sp.fechaSolicitud) <=', $filtros['fecha_fin']);
        }
        
        $this->db->where('u.rol', 'lector');
        $this->db->where('u.profesion IS NOT NULL');
        $this->db->group_by('u.profesion');
        $this->db->order_by('total_prestamos', 'DESC');
        
        return $this->db->get()->result();
    }

    public function obtener_estadisticas_solicitudes($filtros = array()) {
        $this->db->select('
            MONTH(sp.fechaSolicitud) as mes,
            YEAR(sp.fechaSolicitud) as anio,
            COUNT(sp.idSolicitud) as total_solicitudes,
            COUNT(DISTINCT ds.idPublicacion) as total_publicaciones_solicitadas,
            SUM(CASE WHEN sp.estadoSolicitud = ' . ESTADO_SOLICITUD_APROBADA . ' THEN 1 ELSE 0 END) as aprobadas,
            SUM(CASE WHEN sp.estadoSolicitud = ' . ESTADO_SOLICITUD_RECHAZADA . ' THEN 1 ELSE 0 END) as rechazadas,
            SUM(CASE WHEN sp.estadoSolicitud = ' . ESTADO_SOLICITUD_PENDIENTE . ' THEN 1 ELSE 0 END) as pendientes,
            SUM(CASE WHEN sp.estadoSolicitud = ' . ESTADO_SOLICITUD_FINALIZADA . ' THEN 1 ELSE 0 END) as finalizadas,
            ROUND((SUM(CASE WHEN sp.estadoSolicitud = ' . ESTADO_SOLICITUD_APROBADA . ' THEN 1 ELSE 0 END) / 
                COUNT(sp.idSolicitud)) * 100, 2) as porcentaje_aprobacion
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud', 'left');
        
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(sp.fechaSolicitud) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(sp.fechaSolicitud) <=', $filtros['fecha_fin']);
        }
        
        $this->db->group_by('YEAR(sp.fechaSolicitud), MONTH(sp.fechaSolicitud)');
        $this->db->order_by('anio, mes');
        
        return $this->db->get()->result();
    }

    public function obtener_estadisticas_tipos($filtros = array()) {
        $this->db->select('
            t.nombreTipo,
            COUNT(DISTINCT p.idPublicacion) as total_publicaciones,
            COUNT(DISTINCT ds.idSolicitud) as total_solicitudes,
            COUNT(DISTINCT pr.idPrestamo) as total_prestamos,
            ROUND(AVG(
                CASE 
                    WHEN pr.fechaDevolucion IS NOT NULL 
                    THEN TIMESTAMPDIFF(HOUR, pr.fechaPrestamo, pr.fechaDevolucion)/24.0
                    ELSE NULL 
                END
            ), 1) as promedio_dias_prestamo
        ');
        $this->db->from('TIPO t');
        $this->db->join('PUBLICACION p', 'p.idTipo = t.idTipo');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idPublicacion = p.idPublicacion', 'left');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = ds.idSolicitud', 'left');
        $this->db->join('PRESTAMO pr', 'pr.idSolicitud = sp.idSolicitud', 'left');
        
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(sp.fechaSolicitud) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(sp.fechaSolicitud) <=', $filtros['fecha_fin']);
        }
        
        $this->db->group_by('t.idTipo, t.nombreTipo');
        $this->db->order_by('total_solicitudes', 'DESC');
        
        return $this->db->get()->result();
    }

    public function obtener_estadisticas_devoluciones($filtros = array()) {
        $this->db->select('
            MONTH(pr.fechaDevolucion) as mes,
            YEAR(pr.fechaDevolucion) as anio,
            COUNT(pr.idPrestamo) as total_devoluciones,
            SUM(CASE WHEN pr.estadoDevolucion = ' . ESTADO_DEVOLUCION_BUENO . ' THEN 1 ELSE 0 END) as estado_bueno,
            SUM(CASE WHEN pr.estadoDevolucion = ' . ESTADO_DEVOLUCION_DAÑADO . ' THEN 1 ELSE 0 END) as estado_dañado,
            SUM(CASE WHEN pr.estadoDevolucion = ' . ESTADO_DEVOLUCION_PERDIDO . ' THEN 1 ELSE 0 END) as estado_perdido,
            ROUND(AVG(TIMESTAMPDIFF(HOUR, pr.fechaPrestamo, pr.fechaDevolucion)/24.0), 1) as promedio_dias_prestamo,
            COUNT(CASE 
                WHEN TIMESTAMPDIFF(HOUR, pr.fechaPrestamo, pr.fechaDevolucion) > 24 
                THEN 1 END) as devoluciones_tardias
        ');
        $this->db->from('PRESTAMO pr');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'pr.idSolicitud = sp.idSolicitud');
        
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(pr.fechaDevolucion) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(pr.fechaDevolucion) <=', $filtros['fecha_fin']);
        }
        
        $this->db->where('pr.estadoPrestamo', ESTADO_PRESTAMO_FINALIZADO);
        $this->db->where('pr.fechaDevolucion IS NOT NULL');
        $this->db->group_by('YEAR(pr.fechaDevolucion), MONTH(pr.fechaDevolucion)');
        $this->db->order_by('anio, mes');
        
        return $this->db->get()->result();
    }

    public function obtener_metricas_generales($filtros = array()) {
        // Usuarios activos
        $this->db->select('COUNT(DISTINCT u.idUsuario) as total_usuarios_activos');
        $this->db->from('USUARIO u');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idUsuario = u.idUsuario');
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(sp.fechaSolicitud) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(sp.fechaSolicitud) <=', $filtros['fecha_fin']);
        }
        $usuarios_activos = $this->db->get()->row()->total_usuarios_activos;

        // Tasa de aprobación
        $this->db->select('
            ROUND((COUNT(CASE WHEN estadoSolicitud = ' . ESTADO_SOLICITUD_APROBADA . ' THEN 1 END) / 
            NULLIF(COUNT(*), 0)) * 100, 2) as tasa_aprobacion
        ');
        $this->db->from('SOLICITUD_PRESTAMO');
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(fechaSolicitud) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(fechaSolicitud) <=', $filtros['fecha_fin']);
        }
        $tasa_aprobacion = $this->db->get()->row()->tasa_aprobacion;

        // Tiempo promedio de préstamo
        $this->db->select('
            ROUND(AVG(TIMESTAMPDIFF(HOUR, fechaPrestamo, fechaDevolucion)/24.0), 1) as promedio_dias_prestamo
        ');
        $this->db->from('PRESTAMO');
        $this->db->where([
            'estadoPrestamo' => ESTADO_PRESTAMO_FINALIZADO,
            'fechaDevolucion IS NOT NULL' => NULL
        ]);
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(fechaPrestamo) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(fechaPrestamo) <=', $filtros['fecha_fin']);
        }
        $promedio_dias = $this->db->get()->row()->promedio_dias_prestamo;

        return array(
            'usuarios_activos' => $usuarios_activos,
            'tasa_aprobacion' => $tasa_aprobacion ?: 0,
            'promedio_dias_prestamo' => $promedio_dias ?: 0
        );
    }
}