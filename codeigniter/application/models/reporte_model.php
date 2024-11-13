

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function obtener_reporte_prestamos($filtros) {
        $this->db->select([
            'p.idPrestamo',
            'p.fechaPrestamo',
            'p.horaInicio',
            'p.horaDevolucion',
            'p.estadoPrestamo',
            'u.nombres',
            'u.apellidoPaterno',
            'pub.titulo',
            'pub.idPublicacion',
            'enc.nombres as nombre_encargado',
            'enc.apellidoPaterno as apellido_encargado',
            'ds.observaciones',
            'CASE 
                WHEN p.estadoPrestamo = 1 AND p.horaDevolucion IS NULL 
                    AND DATEDIFF(NOW(), p.fechaPrestamo) <= 1 THEN "Activo"
                WHEN p.estadoPrestamo = 2 THEN "Devuelto"
                WHEN p.estadoPrestamo = 1 AND DATEDIFF(NOW(), p.fechaPrestamo) > 1 
                    THEN "Vencido"
            END as estado_prestamo'
        ]);

        $this->_aplicar_joins_prestamos();
        $this->_aplicar_filtros_prestamos($filtros);

        return $this->db->get()->result();
    }

    private function _aplicar_joins_prestamos() {
        $this->db->from('PRESTAMO p')
            ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud')
            ->join('USUARIO u', 'sp.idUsuario = u.idUsuario')
            ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud')
            ->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion')
            ->join('USUARIO enc', 'p.idEncargadoPrestamo = enc.idUsuario');
    }

    private function _aplicar_filtros_prestamos($filtros) {
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(p.fechaPrestamo) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(p.fechaPrestamo) <=', $filtros['fecha_fin']);
        }
        if (!empty($filtros['id_encargado'])) {
            $this->db->where('p.idEncargadoPrestamo', $filtros['id_encargado']);
        }
        if (!empty($filtros['estado'])) {
            $this->_aplicar_filtro_estado($filtros['estado']);
        }

        $this->db->order_by('p.fechaPrestamo', 'DESC');
    }

    public function obtener_estadisticas_prestamos($filtros) {
        $this->db->select('
            COUNT(CASE WHEN p.estadoPrestamo = 1 AND p.horaDevolucion IS NULL AND DATEDIFF(NOW(), p.fechaPrestamo) <= 1 THEN 1 END) as activos,
            COUNT(CASE WHEN p.estadoPrestamo = 2 THEN 1 END) as devueltos,
            COUNT(CASE WHEN p.estadoPrestamo = 1 AND p.horaDevolucion IS NULL AND DATEDIFF(NOW(), p.fechaPrestamo) > 1 THEN 1 END) as vencidos
        ');
        
        $this->db->from('PRESTAMO p');
        
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('p.fechaPrestamo >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('p.fechaPrestamo <=', $filtros['fecha_fin']);
        }

        return $this->db->get()->row();
    }

    public function obtener_estadisticas_mensuales() {
        $this->db->select([
            'MONTH(p.fechaPrestamo) as mes',
            'YEAR(p.fechaPrestamo) as año',
            'COUNT(CASE WHEN p.estadoPrestamo = 1 AND p.horaDevolucion IS NULL 
                AND DATEDIFF(NOW(), p.fechaPrestamo) <= 1 THEN 1 END) as activos',
            'COUNT(CASE WHEN p.estadoPrestamo = 2 THEN 1 END) as devueltos',
            'COUNT(CASE WHEN p.estadoPrestamo = 1 AND p.horaDevolucion IS NULL 
                AND DATEDIFF(NOW(), p.fechaPrestamo) > 1 THEN 1 END) as vencidos'
        ]);
        
        $this->db->from('PRESTAMO p');
        $this->db->where('p.fechaPrestamo >= DATE_SUB(NOW(), INTERVAL 6 MONTH)');
        $this->db->group_by('YEAR(p.fechaPrestamo), MONTH(p.fechaPrestamo)');
        $this->db->order_by('YEAR(p.fechaPrestamo), MONTH(p.fechaPrestamo)');
        
        return $this->db->get()->result();
    }
    
    public function obtener_reporte_publicaciones($filtros) {
        $this->db->select('
            p.idPublicacion,
            p.titulo,
            t.nombreTipo,
            e.nombreEditorial,
            COUNT(DISTINCT sp.idSolicitud) as total_solicitudes,
            COUNT(DISTINCT CASE WHEN pr.estadoPrestamo = 1 THEN pr.idPrestamo END) as prestamos_activos,
            COUNT(DISTINCT CASE WHEN pr.estadoPrestamo = 2 THEN pr.idPrestamo END) as prestamos_completados,
            DATE_FORMAT(p.fechaPublicacion, "%d/%m/%Y") as fecha_publicacion
        ');
        
        $this->db->from('PUBLICACION p');
        $this->db->join('TIPO t', 't.idTipo = p.idTipo');
        $this->db->join('EDITORIAL e', 'e.idEditorial = p.idEditorial');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idPublicacion = p.idPublicacion', 'left');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = ds.idSolicitud', 'left');
        $this->db->join('PRESTAMO pr', 'pr.idSolicitud = sp.idSolicitud', 'left');
        
        // Aplicar filtros
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('sp.fechaSolicitud >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('sp.fechaSolicitud <=', $filtros['fecha_fin']);
        }
        if (!empty($filtros['tipo'])) {
            $this->db->where('p.idTipo', $filtros['tipo']);
        }
        
        $this->db->group_by('p.idPublicacion, p.titulo, t.nombreTipo, e.nombreEditorial, p.fechaPublicacion');
        $this->db->order_by('total_solicitudes', 'DESC');
        
        return $this->db->get()->result();
    }
    
    public function obtener_estadisticas_publicaciones($filtros) {
        $this->db->select('
            COUNT(DISTINCT p.idPublicacion) as total_publicaciones,
            COUNT(DISTINCT sp.idSolicitud) as total_solicitudes,
            ROUND(COUNT(DISTINCT sp.idSolicitud) / COUNT(DISTINCT p.idPublicacion), 2) as promedio_solicitudes_por_publicacion,
            (
                SELECT titulo
                FROM PUBLICACION p2
                JOIN DETALLE_SOLICITUD ds2 ON ds2.idPublicacion = p2.idPublicacion
                JOIN SOLICITUD_PRESTAMO sp2 ON sp2.idSolicitud = ds2.idSolicitud
                GROUP BY p2.idPublicacion, p2.titulo
                ORDER BY COUNT(DISTINCT sp2.idSolicitud) DESC
                LIMIT 1
            ) as publicacion_mas_solicitada
        ');
        
        $this->db->from('PUBLICACION p');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idPublicacion = p.idPublicacion', 'left');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = ds.idSolicitud', 'left');
        
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('sp.fechaSolicitud >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('sp.fechaSolicitud <=', $filtros['fecha_fin']);
        }
        if (!empty($filtros['tipo'])) {
            $this->db->where('p.idTipo', $filtros['tipo']);
        }
        
        return $this->db->get()->row();
    }
    
    public function obtener_tendencias_mensuales($filtros) {
        $this->db->select('
            DATE_FORMAT(sp.fechaSolicitud, "%Y-%m") as mes,
            COUNT(DISTINCT sp.idSolicitud) as total_solicitudes
        ');
        
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
        $this->db->join('PUBLICACION p', 'p.idPublicacion = ds.idPublicacion');
        
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('sp.fechaSolicitud >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('sp.fechaSolicitud <=', $filtros['fecha_fin']);
        }
        if (!empty($filtros['tipo'])) {
            $this->db->where('p.idTipo', $filtros['tipo']);
        }
        
        $this->db->group_by('mes');
        $this->db->order_by('mes', 'ASC');
        
        return $this->db->get()->result();
    }
    public function obtener_reporte_usuarios($filtros) {
        $this->db->select('
            u.idUsuario,
            u.nombres,
            u.apellidoPaterno,
            u.profesion,
            COUNT(DISTINCT sp.idSolicitud) as total_solicitudes,
            COUNT(DISTINCT pr.idPrestamo) as total_prestamos,
            MAX(sp.fechaSolicitud) as ultima_actividad,
            SUM(CASE WHEN pr.estadoPrestamo = 1 THEN 1 ELSE 0 END) as prestamos_activos,
            SUM(CASE WHEN pr.estadoPrestamo = 2 THEN 1 ELSE 0 END) as prestamos_completados
        ');
        
        $this->db->from('USUARIO u');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idUsuario = u.idUsuario', 'left');
        $this->db->join('PRESTAMO pr', 'pr.idSolicitud = sp.idSolicitud', 'left');
        
        // Aplicar filtros
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('sp.fechaSolicitud >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('sp.fechaSolicitud <=', $filtros['fecha_fin']);
        }
        if (!empty($filtros['min_prestamos'])) {
            $this->db->having('total_prestamos >=', $filtros['min_prestamos']);
        }
        
        $this->db->where('u.rol', 'lector');
        $this->db->group_by('u.idUsuario, u.nombres, u.apellidoPaterno, u.profesion');
        $this->db->order_by('total_solicitudes', 'DESC');
        
        return $this->db->get()->result();
    }
    
    public function obtener_estadisticas_usuarios($filtros) {
        $this->db->select('
            COUNT(DISTINCT u.idUsuario) as total_usuarios,
            ROUND(AVG(prestamos_por_usuario.total_prestamos), 2) as promedio_prestamos,
            MAX(prestamos_por_usuario.total_prestamos) as max_prestamos
        ');
        
        $this->db->from('USUARIO u');
        $this->db->join('(
            SELECT 
                sp.idUsuario,
                COUNT(DISTINCT pr.idPrestamo) as total_prestamos
            FROM SOLICITUD_PRESTAMO sp
            LEFT JOIN PRESTAMO pr ON pr.idSolicitud = sp.idSolicitud
            GROUP BY sp.idUsuario
        ) prestamos_por_usuario', 'prestamos_por_usuario.idUsuario = u.idUsuario', 'left');
        
        $this->db->where('u.rol', 'lector');
        
        return $this->db->get()->row();
    }
    
    public function obtener_actividad_mensual_usuarios($filtros) {
        $this->db->select('
            DATE_FORMAT(sp.fechaSolicitud, "%Y-%m") as mes,
            COUNT(DISTINCT u.idUsuario) as usuarios_activos,
            COUNT(DISTINCT sp.idSolicitud) as total_solicitudes
        ');
        
        $this->db->from('USUARIO u');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idUsuario = u.idUsuario');
        
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('sp.fechaSolicitud >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('sp.fechaSolicitud <=', $filtros['fecha_fin']);
        }
        
        $this->db->where('u.rol', 'lector');
        $this->db->group_by('mes');
        $this->db->order_by('mes', 'ASC');
        
        return $this->db->get()->result();
    }
}