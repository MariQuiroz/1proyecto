<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function obtener_estadisticas_profesion($filtros) {
        $this->db->select('
            COALESCE(u.profesion, "No especificado") as profesion,
            COUNT(DISTINCT p.idPrestamo) as total_prestamos,
            COUNT(DISTINCT u.idUsuario) as total_lectores,
            AVG(TIMESTAMPDIFF(DAY, p.fechaPrestamo, COALESCE(p.fechaDevolucion, NOW()))) as promedio_dias_prestamo
        ');
        
        $this->db->from('USUARIO u');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idUsuario = u.idUsuario');
        $this->db->join('PRESTAMO p', 'p.idSolicitud = sp.idSolicitud');
        
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(p.fechaPrestamo) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(p.fechaPrestamo) <=', $filtros['fecha_fin']);
        }
        if (!empty($filtros['profesion'])) {
            $this->db->where('u.profesion', $filtros['profesion']);
        }
        
        $this->db->where('u.rol', 'lector');
        $this->db->group_by('u.profesion');
        
        $resultado = $this->db->get()->result();
        
        // Si no hay resultados, devolver un array con valores por defecto
        if (empty($resultado)) {
            return [
                (object)[
                    'profesion' => 'Sin datos',
                    'total_prestamos' => 0,
                    'total_lectores' => 0,
                    'promedio_dias_prestamo' => 0
                ]
            ];
        }
        
        return $resultado;
    }

    public function obtener_detalle_prestamos_profesion($filtros) {
        $this->db->select('
            u.profesion,
            u.nombres,
            u.apellidoPaterno,
            pub.titulo as titulo_publicacion,
            t.nombreTipo as tipo_publicacion,
            e.nombreEditorial,
            pub.fechaPublicacion,
            p.fechaPrestamo,
            p.fechaDevolucion,
            CASE 
                WHEN p.estadoDevolucion = 1 THEN "Bueno"
                WHEN p.estadoDevolucion = 2 THEN "Dañado"
                WHEN p.estadoDevolucion = 3 THEN "Perdido"
                ELSE "No devuelto"
            END as estado_devolucion,
            TIMESTAMPDIFF(DAY, p.fechaPrestamo, COALESCE(p.fechaDevolucion, NOW())) as dias_prestamo
        ', false);
    
        $this->db->from('USUARIO u');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idUsuario = u.idUsuario');
        $this->db->join('PRESTAMO p', 'p.idSolicitud = sp.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
        $this->db->join('PUBLICACION pub', 'pub.idPublicacion = ds.idPublicacion');
        $this->db->join('TIPO t', 't.idTipo = pub.idTipo');
        $this->db->join('EDITORIAL e', 'e.idEditorial = pub.idEditorial');
    
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(p.fechaPrestamo) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(p.fechaPrestamo) <=', $filtros['fecha_fin']);
        }
        if (!empty($filtros['profesion'])) {
            $this->db->where('u.profesion', $filtros['profesion']);
        }
    
        $this->db->where('u.rol', 'lector');
        $this->db->order_by('p.fechaPrestamo', 'DESC');
    
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

    public function obtener_detalles_publicaciones_por_tipo($filtros = array()) {
        $this->db->select('
            p.idPublicacion,
            p.titulo,
            t.nombreTipo,
            e.nombreEditorial,
            p.fechaPublicacion,
            COUNT(DISTINCT ds.idSolicitud) as total_solicitudes,
            COUNT(DISTINCT CASE WHEN pr.estadoPrestamo = 1 THEN pr.idPrestamo END) as prestamos_activos,
            COUNT(DISTINCT CASE WHEN pr.estadoPrestamo = 2 THEN pr.idPrestamo END) as prestamos_completados,
            ROUND(AVG(
                CASE 
                    WHEN pr.fechaDevolucion IS NOT NULL 
                    THEN TIMESTAMPDIFF(HOUR, pr.fechaPrestamo, pr.fechaDevolucion)/24.0
                    ELSE NULL 
                END
            ), 1) as promedio_dias_prestamo
        ');
        
        $this->db->from('PUBLICACION p');
        $this->db->join('TIPO t', 'p.idTipo = t.idTipo');
        $this->db->join('EDITORIAL e', 'p.idEditorial = e.idEditorial');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idPublicacion = p.idPublicacion', 'left');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = ds.idSolicitud', 'left');
        $this->db->join('PRESTAMO pr', 'pr.idSolicitud = sp.idSolicitud', 'left');
        
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(sp.fechaSolicitud) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(sp.fechaSolicitud) <=', $filtros['fecha_fin']);
        }
        if (!empty($filtros['tipo'])) {
            $this->db->where('t.idTipo', $filtros['tipo']);
        }
        
        $this->db->group_by('p.idPublicacion, p.titulo, t.nombreTipo, e.nombreEditorial, p.fechaPublicacion');
        $this->db->order_by('total_solicitudes', 'DESC');
        
        return $this->db->get()->result();
    }
}