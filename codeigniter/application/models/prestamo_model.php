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
            log_message('debug', "Iniciando préstamo para solicitud: {$idSolicitud}");

            // Obtener todos los detalles de la solicitud
            $this->db->select('
                sp.idSolicitud,
                sp.idUsuario,
                ds.idPublicacion,
                sp.estadoSolicitud,
                p.estado as estado_publicacion,
                p.titulo
            ');
            $this->db->from('SOLICITUD_PRESTAMO sp');
            $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
            $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
            $this->db->where('sp.idSolicitud', $idSolicitud);
            
            $detalles = $this->db->get()->result();
            log_message('debug', "Detalles encontrados: " . count($detalles));

            if (empty($detalles)) {
                log_message('error', "No se encontraron detalles para la solicitud {$idSolicitud}");
                $this->db->trans_rollback();
                return false;
            }

            // Verificar que todas las publicaciones estén disponibles
            foreach ($detalles as $detalle) {
                if ($detalle->estado_publicacion != ESTADO_PUBLICACION_DISPONIBLE) {
                    log_message('error', "Publicación {$detalle->idPublicacion} no disponible");
                    $this->db->trans_rollback();
                    return false;
                }
            }

            $fechaActual = date('Y-m-d H:i:s');
            $horaActual = date('H:i:s');
            
            // Crear un solo registro de préstamo para la solicitud
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
            $idPrestamo = $this->db->insert_id();
            log_message('debug', "Préstamo creado con ID: {$idPrestamo}");

            // Actualizar estado de cada publicación
            foreach ($detalles as $detalle) {
                $this->db->where('idPublicacion', $detalle->idPublicacion);
                $this->db->update('PUBLICACION', [
                    'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
                    'fechaActualizacion' => $fechaActual
                ]);
                log_message('debug', "Actualizado estado de publicación: {$detalle->idPublicacion}");
            }

            // Actualizar estado de la solicitud
            $this->db->where('idSolicitud', $idSolicitud);
            $this->db->update('SOLICITUD_PRESTAMO', [
                'estadoSolicitud' => ESTADO_SOLICITUD_FINALIZADA,
                'fechaActualizacion' => $fechaActual
            ]);
            log_message('debug', "Actualizado estado de solicitud: {$idSolicitud}");

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error al iniciar préstamo: ' . $e->getMessage());
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
   
public function obtener_prestamos_activos() {
    log_message('debug', "Obteniendo préstamos activos");

    $this->db->select('
        p.idPrestamo,
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
        // Usar el primer título como título principal
        $prestamo->titulo = $prestamo->titulos[0];
    }

    log_message('debug', "Total de préstamos activos encontrados: " . count($prestamos));
    return $prestamos;
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
        // Obtener datos del préstamo con joins necesarios
        $this->db->select('
            p.*,
            sp.idUsuario,
            ds.idPublicacion
        ');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->where('p.idPrestamo', $idPrestamo);
        
        $prestamo = $this->db->get()->row();
        
        if (!$prestamo) {
            log_message('error', "Préstamo no encontrado: {$idPrestamo}");
            return false;
        }
        
        // Actualizar préstamo
        $data_prestamo = [
            'estadoPrestamo' => ESTADO_PRESTAMO_FINALIZADO,
            'estadoDevolucion' => $estadoDevolucion,
            'idEncargadoDevolucion' => $idEncargado,
            'horaDevolucion' => date('H:i:s'),
            'fechaActualizacion' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('idPrestamo', $idPrestamo);
        $this->db->update('PRESTAMO', $data_prestamo);

        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            log_message('error', "Error al finalizar préstamo: {$idPrestamo}");
            return false;
        }
        
        return $prestamo;
        
    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Error en finalizar_prestamo: ' . $e->getMessage());
        return false;
    }
}

private function _obtener_texto_estado_devolucion($estado) {
    $estado = (int)$estado; // Asegurar que sea un entero
    
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

}