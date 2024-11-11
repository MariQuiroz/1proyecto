<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prestamo_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

// En Prestamo_model.php
public function iniciar_prestamo($idSolicitud, $idEncargado) {
    $this->db->trans_start();
    
    try {
        $fechaActual = date('Y-m-d H:i:s');
        
        // Obtener detalles de la solicitud
        $this->db->select('
            ds.idPublicacion, 
            ds.idSolicitud,
            sp.idUsuario,
            p.titulo,
            p.estado'
        );
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
        $this->db->where('sp.idSolicitud', $idSolicitud);
        
        $publicaciones = $this->db->get()->result();

        if (empty($publicaciones)) {
            log_message('error', "No se encontraron publicaciones para la solicitud {$idSolicitud}");
            return false;
        }

        foreach ($publicaciones as $pub) {
            // Crear registro de préstamo
            $data_prestamo = array(
                'idSolicitud' => $idSolicitud,
                'idEncargadoPrestamo' => $idEncargado,
                'fechaPrestamo' => $fechaActual,
                'horaInicio' => date('H:i:s'),
                'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
                'estado' => 1,
                'fechaCreacion' => $fechaActual,
                'idUsuarioCreador' => $idEncargado
            );
            
            $this->db->insert('PRESTAMO', $data_prestamo);
            $idPrestamo = $this->db->insert_id();

            // Actualizar estado de la publicación a EN_CONSULTA
            $this->db->where('idPublicacion', $pub->idPublicacion);
            $this->db->update('PUBLICACION', array(
                'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
                'fechaActualizacion' => $fechaActual,
                'idUsuarioCreador' => $idEncargado
            ));

            log_message('info', "Préstamo {$idPrestamo} iniciado para publicación {$pub->idPublicacion}");
        }

        $this->db->trans_complete();
        return $this->db->trans_status();

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Error en iniciar_prestamo: ' . $e->getMessage());
        return false;
    }
}

public function obtener_prestamos_activos() {
    log_message('debug', "Obteniendo préstamos activos");

    $this->db->select('
        p.idPrestamo,
        p.idSolicitud,        
        p.fechaPrestamo,
        p.horaInicio,
        p.estadoPrestamo,
        u.nombres,
        u.apellidoPaterno,
        e.nombreEditorial,
        GROUP_CONCAT(DISTINCT pub.titulo) as titulos,
        MIN(ds.observaciones) as observaciones
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
        p.idSolicitud,        
        p.fechaPrestamo, 
        p.horaInicio, 
        p.estadoPrestamo,
        u.nombres, 
        u.apellidoPaterno, 
        e.nombreEditorial
    ');
    $this->db->order_by('p.fechaPrestamo', 'DESC');

    $prestamos = $this->db->get()->result();
    
    // Procesar los resultados
    foreach ($prestamos as &$prestamo) {
        $prestamo->titulos = explode(',', $prestamo->titulos);
        $prestamo->titulo = $prestamo->titulos[0];
    }

    log_message('debug', "Total de préstamos activos encontrados: " . count($prestamos));
    return $prestamos;
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
        $this->db->select('
            P.idPrestamo,
            P.fechaPrestamo,
            P.horaInicio,
            P.horaDevolucion,
            P.estadoPrestamo,
            P.estadoDevolucion,
            PUB.titulo,
            PUB.idPublicacion,
            E.nombreEditorial,
            T.nombreTipo
        ');
        $this->db->from('PRESTAMO P');
        $this->db->join('SOLICITUD_PRESTAMO SP', 'P.idSolicitud = SP.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD DS', 'SP.idSolicitud = DS.idSolicitud');
        $this->db->join('PUBLICACION PUB', 'DS.idPublicacion = PUB.idPublicacion');
        $this->db->join('EDITORIAL E', 'PUB.idEditorial = E.idEditorial');
        $this->db->join('TIPO T', 'PUB.idTipo = T.idTipo');
        $this->db->where('SP.idUsuario', $idUsuario);
        $this->db->where('P.estado', 1); // Solo préstamos activos en el sistema
        $this->db->order_by('P.fechaPrestamo', 'DESC');
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        
        return array();
    }
    
    public function obtener_prestamo_detallado($idPrestamo) {
        $this->db->select([
            'p.idPrestamo',
            'p.estadoPrestamo',
            'p.fechaPrestamo',
            'p.horaInicio',
            'p.estadoDevolucion',
            'sp.idUsuario',
            'ds.idPublicacion',
            'pub.titulo',
            'u.email',
            'u.nombres',
            'u.apellidoPaterno'
        ]);
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
        $this->db->join('USUARIO u', 'sp.idUsuario = u.idUsuario');
        $this->db->where('p.idPrestamo', $idPrestamo);
        
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
   

public function obtener_prestamo($idPrestamo) {
    log_message('debug', "Obteniendo préstamo ID: {$idPrestamo}");

    $this->db->select('
        p.idPrestamo,
        p.idSolicitud,
        p.fechaPrestamo,
        p.horaInicio,
        p.horaDevolucion,
        p.estadoPrestamo,
        sp.idUsuario,
        GROUP_CONCAT(DISTINCT pub.titulo) as titulos,
        MIN(pub.idPublicacion) as idPublicacion,
        MIN(ds.observaciones) as observaciones
    ');
    $this->db->from('PRESTAMO p');
    $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
    $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
    $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
    $this->db->where('p.idPrestamo', $idPrestamo);
    $this->db->group_by('
        p.idPrestamo,
        p.idSolicitud,
        p.fechaPrestamo,
        p.horaInicio,
        p.horaDevolucion,
        p.estadoPrestamo,
        sp.idUsuario
    ');

    $prestamo = $this->db->get()->row();
    
    if ($prestamo) {
        $prestamo->titulos = explode(',', $prestamo->titulos);
        $prestamo->titulo = $prestamo->titulos[0];
        
        // Obtener detalles adicionales de las publicaciones
        $publicaciones = $this->obtener_publicaciones_prestamo($idPrestamo);
        $prestamo->publicaciones = $publicaciones;
        
        log_message('debug', "Préstamo encontrado con {$prestamo->titulo}");
    } else {
        log_message('debug', "No se encontró el préstamo {$idPrestamo}");
    }

    return $prestamo;
}

private function obtener_publicaciones_prestamo($idPrestamo) {
    $this->db->select('
        pub.idPublicacion,
        pub.titulo,
        pub.fechaPublicacion,
        e.nombreEditorial
    ');
    $this->db->from('PRESTAMO p');
    $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
    $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
    $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
    $this->db->join('EDITORIAL e', 'pub.idEditorial = e.idEditorial');
    $this->db->where('p.idPrestamo', $idPrestamo);
    
    return $this->db->get()->result();
}

public function obtener_datos_ficha_devolucion($idPrestamo) {
    // Obtener datos básicos del préstamo y usuario
    $this->db->select('
        p.idPrestamo,
        p.fechaPrestamo,
        p.horaInicio,
        p.horaDevolucion,
        p.estadoDevolucion,
        u.nombres as nombreLector,
        u.apellidoPaterno as apellidoLector,
        u.carnet,
        u.email,
        u.profesion,
        enc.nombres as nombreEncargado,
        enc.apellidoPaterno as apellidoEncargado,
        pub.titulo,
        pub.ubicacionFisica,
        t.nombreTipo,
        e.nombreEditorial,
        ds.observaciones
    ');
    $this->db->from('PRESTAMO p');
    $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
    $this->db->join('USUARIO u', 'sp.idUsuario = u.idUsuario');
    $this->db->join('USUARIO enc', 'p.idEncargadoPrestamo = enc.idUsuario');
    $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
    $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
    $this->db->join('EDITORIAL e', 'pub.idEditorial = e.idEditorial');
    $this->db->join('TIPO t', 'pub.idTipo = t.idTipo');
    $this->db->where('p.idPrestamo', $idPrestamo);
    
    $resultado = $this->db->get();
    
    if ($resultado->num_rows() == 0) {
        log_message('error', "No se encontraron datos para el préstamo ID: {$idPrestamo}");
        return false;
    }
    
    $datos = $resultado->row_array();
    
    // Asegurar que todos los campos requeridos existan
    $campos_requeridos = [
        'carnet' => 'No disponible',
        'titulo' => 'Sin título',
        'nombreEditorial' => 'Editorial no especificada',
        'nombreTipo' => 'Tipo no especificado',
        'ubicacionFisica' => 'No especificada',
        'estadoDevolucion' => ESTADO_DEVOLUCION_BUENO
    ];
    
    foreach ($campos_requeridos as $campo => $valor_default) {
        if (!isset($datos[$campo]) || empty($datos[$campo])) {
            $datos[$campo] = $valor_default;
            log_message('debug', "Campo {$campo} no encontrado, usando valor por defecto");
        }
    }
    
    return $datos;
}

public function finalizar_prestamo($idPrestamo, $idEncargado, $estadoDevolucion) {
    $this->db->trans_start();
    
    try {
        // Obtener datos completos del préstamo
        $this->db->select('
            p.idPrestamo,
            p.idSolicitud,
            p.estadoPrestamo,
            sp.idUsuario,
            ds.idPublicacion,
            pub.estado as estadoPublicacion,
            pub.titulo
        ');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
        $this->db->where('p.idPrestamo', $idPrestamo);
        
        $prestamo = $this->db->get()->row();

        if (!$prestamo) {
            throw new Exception("Préstamo no encontrado: {$idPrestamo}");
        }

        // Actualizar el préstamo
        $data_prestamo = [
            'estadoPrestamo' => ESTADO_PRESTAMO_FINALIZADO,
            'estadoDevolucion' => $estadoDevolucion,
            'idEncargadoDevolucion' => $idEncargado,
            'horaDevolucion' => date('H:i:s'),
            'fechaActualizacion' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('idPrestamo', $idPrestamo);
        if (!$this->db->update('PRESTAMO', $data_prestamo)) {
            throw new Exception('Error al actualizar el préstamo');
        }

        // Actualizar la publicación a DISPONIBLE
        $data_publicacion = [
            'estado' => ESTADO_PUBLICACION_DISPONIBLE,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idEncargado
        ];
        
        $this->db->where('idPublicacion', $prestamo->idPublicacion);
        if (!$this->db->update('PUBLICACION', $data_publicacion)) {
            throw new Exception('Error al actualizar el estado de la publicación');
        }

        // Actualizar la solicitud
        $data_solicitud = [
            'estadoSolicitud' => ESTADO_SOLICITUD_FINALIZADA,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idEncargado
        ];
        
        $this->db->where('idSolicitud', $prestamo->idSolicitud);
        if (!$this->db->update('SOLICITUD_PRESTAMO', $data_solicitud)) {
            throw new Exception('Error al actualizar la solicitud');
        }

        // Registrar en el log del sistema
        log_message('info', sprintf(
            "Préstamo %d finalizado. Publicación %d ('%s') marcada como disponible. Estado de devolución: %s",
            $idPrestamo,
            $prestamo->idPublicacion,
            $prestamo->titulo,
            $estadoDevolucion
        ));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            throw new Exception("Error en la transacción al finalizar el préstamo {$idPrestamo}");
        }

        return $prestamo;

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Error en finalizar_prestamo: ' . $e->getMessage());
        return false;
    }
}

private function _obtener_texto_estado_devolucion($estado) {
    $estado = (int)$estado;
    
    switch ($estado) {
        case ESTADO_DEVOLUCION_DAÑADO:
            return 'Dañado';
        case ESTADO_DEVOLUCION_PERDIDO:
            return 'Perdido';
        case ESTADO_DEVOLUCION_BUENO:
        default:
            return 'Bueno';
    }
}

public function obtener_prestamos_devueltos() {
    $this->db->select('
        p.idPrestamo,
        p.fechaPrestamo,
        p.horaInicio,
        p.horaDevolucion,
        p.estadoDevolucion,
        p.fechaCreacion,
        p.idEncargadoPrestamo,
        p.idEncargadoDevolucion,
        sp.idUsuario,
        u.nombres as nombre_lector,
        u.apellidoPaterno as apellido_lector,
        u.carnet,
        pub.titulo,
        pub.ubicacionFisica,
        edit.nombreEditorial,
        ep.nombres as nombre_encargado_prestamo,
        ep.apellidoPaterno as apellido_encargado_prestamo,
        edev.nombres as nombre_encargado_devolucion,
        edev.apellidoPaterno as apellido_encargado_devolucion
    ');
    
    $this->db->from('PRESTAMO p');
    $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
    $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
    $this->db->join('USUARIO u', 'sp.idUsuario = u.idUsuario');
    $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
    $this->db->join('EDITORIAL edit', 'pub.idEditorial = edit.idEditorial');
    $this->db->join('USUARIO ep', 'p.idEncargadoPrestamo = ep.idUsuario', 'left');
    $this->db->join('USUARIO edev', 'p.idEncargadoDevolucion = edev.idUsuario', 'left');
    
    $this->db->where('p.estadoPrestamo', ESTADO_PRESTAMO_FINALIZADO);
    $this->db->where('p.estado', 1);
    $this->db->order_by('p.fechaCreacion', 'DESC');
    
    return $this->db->get()->result();
}

}