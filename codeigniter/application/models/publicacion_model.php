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
            p.fechaPublicacion,
            p.portada,
            p.ubicacionFisica,
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
        PUBLICACION.idPublicacion,
        PUBLICACION.titulo,
        PUBLICACION.fechaPublicacion,
        PUBLICACION.numeroPaginas,
        PUBLICACION.portada,
        PUBLICACION.descripcion,
        PUBLICACION.ubicacionFisica,
        PUBLICACION.estado,
        PUBLICACION.fechaCreacion,
        PUBLICACION.fechaActualizacion,
        TIPO.nombreTipo,
        EDITORIAL.nombreEditorial
    ');
    $this->db->from('PUBLICACION');
    $this->db->join('TIPO', 'TIPO.idTipo = PUBLICACION.idTipo');
    $this->db->join('EDITORIAL', 'EDITORIAL.idEditorial = PUBLICACION.idEditorial');
    // Añadir este filtro
    $this->db->where('PUBLICACION.estado !=', ESTADO_PUBLICACION_ELIMINADO);
    $this->db->order_by('PUBLICACION.fechaCreacion', 'DESC');
    return $this->db->get()->result();
}

private function mapear_estado($estado) {
    $mapeo_estados = [
        ESTADO_PUBLICACION_DISPONIBLE => 'Disponible',
        ESTADO_PUBLICACION_EN_CONSULTA => 'En préstamo',
        ESTADO_PUBLICACION_EN_MANTENIMIENTO => 'En mantenimiento',
        ESTADO_PUBLICACION_ELIMINADO => 'Eliminado'  // Añadir este
    ];

    return isset($mapeo_estados[$estado]) ? $mapeo_estados[$estado] : 'Estado desconocido';
}
    public function obtener_estado_personalizado($idPublicacion, $idUsuario) {
        $this->db->select('
            p.estado,
            sp.idUsuario as usuario_solicitud,
            pr.estadoPrestamo,
            ds.idSolicitud
        ');
        $this->db->from('PUBLICACION p');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idPublicacion = p.idPublicacion', 'left');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = ds.idSolicitud AND sp.estado = 1', 'left');
        $this->db->join('PRESTAMO pr', 'pr.idSolicitud = sp.idSolicitud AND pr.estadoPrestamo = 1', 'left');
        $this->db->where('p.idPublicacion', $idPublicacion);
        $resultado = $this->db->get()->row();
    
        if ($resultado) {
            if ($resultado->estado == ESTADO_PUBLICACION_EN_CONSULTA) {
                if ($resultado->usuario_solicitud == $idUsuario && $resultado->estadoPrestamo == ESTADO_PRESTAMO_ACTIVO) {
                    return 'En préstamo por ti';
                }
                return 'En consulta';
            }
            return $this->mapear_estado($resultado->estado);
        }
        return 'Estado desconocido';
    }
    public function obtener_publicacion_detallada($idPublicacion) {
        $this->db->select('
            p.idPublicacion,
            p.titulo,
            p.ubicacionFisica,
            p.estado,
            e.nombreEditorial,
            t.nombreTipo
        ');
        $this->db->from('PUBLICACION p');
        $this->db->join('EDITORIAL e', 'e.idEditorial = p.idEditorial');
        $this->db->join('TIPO t', 't.idTipo = p.idTipo');
        $this->db->where('p.idPublicacion', $idPublicacion);
        
        return $this->db->get()->row();
    }
    
    public function obtener_publicaciones_seleccionadas($publicaciones_ids) {
        if (empty($publicaciones_ids)) {
            return array();
        }
    
        $this->db->select('
            p.idPublicacion,
            p.titulo,
            p.ubicacionFisica,
            p.estado,
            e.nombreEditorial,
            t.nombreTipo
        ');
        $this->db->from('PUBLICACION p');
        $this->db->join('EDITORIAL e', 'e.idEditorial = p.idEditorial');
        $this->db->join('TIPO t', 't.idTipo = p.idTipo');
        $this->db->where_in('p.idPublicacion', $publicaciones_ids);
        
        return $this->db->get()->result();
    }
}