<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prestamo_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

// En Prestamo_model.php
/*public function iniciar_prestamo($idSolicitud, $idEncargado) {
    $this->db->trans_start();
    
    try {
        
        // Establecer la zona horaria correcta para Bolivia
        date_default_timezone_set('America/La_Paz');
        
        // Crear objeto DateTime para manejar las fechas correctamente
        $fechaHoraActual = new DateTime();
        
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
                'fechaPrestamo' => $fechaHoraActual->format('Y-m-d'),
                'horaInicio' => $fechaHoraActual->format('H:i:s'),
                'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
                'estado' => 1,
                'fechaCreacion' => $fechaHoraActual->format('Y-m-d H:i:s'),
                'idUsuarioCreador' => $idEncargado
            );
            
            $this->db->insert('PRESTAMO', $data_prestamo);
            $idPrestamo = $this->db->insert_id();

            // Actualizar estado de la publicación a EN_CONSULTA
            $this->db->where('idPublicacion', $pub->idPublicacion);
            $this->db->update('PUBLICACION', array(
                'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
                'fechaActualizacion' => $fechaHoraActual->format('Y-m-d H:i:s'),
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
}*/
public function iniciar_prestamo($idSolicitud, $idEncargado) {
    $this->db->trans_start();
    
    try {
        // Establecer zona horaria
        date_default_timezone_set('America/La_Paz');
        $fechaActual = new DateTime();

        // Obtener detalles de la solicitud
        $this->db->select('
            ds.idPublicacion, 
            ds.idSolicitud,
            sp.idUsuario,
            p.titulo,
            p.estado
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        $this->db->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');
        $this->db->join('PUBLICACION p', 'ds.idPublicacion = p.idPublicacion');
        $this->db->where('sp.idSolicitud', $idSolicitud);
        
        $publicaciones = $this->db->get()->result();

        if (empty($publicaciones)) {
            throw new Exception("No se encontraron publicaciones para la solicitud {$idSolicitud}");
        }

        foreach ($publicaciones as $pub) {
            // Crear registro de préstamo con todos los campos requeridos
            $data_prestamo = array(
                'idSolicitud' => $idSolicitud,
                'idEncargadoPrestamo' => $idEncargado,
                'fechaPrestamo' => $fechaActual->format('Y-m-d H:i:s'), // DATETIME
                'fechaDevolucion' => $fechaActual->format('Y-m-d H:i:s'), // DATETIME inicial
                'horaInicio' => $fechaActual->format('H:i:s'),
                'estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
                'estadoDevolucion' => ESTADO_DEVOLUCION_BUENO, // Valor por defecto
                'estado' => 1,
                'fechaCreacion' => $fechaActual->format('Y-m-d H:i:s'),
                'idUsuarioCreador' => $idEncargado
            );
            
            $this->db->insert('PRESTAMO', $data_prestamo);
            $idPrestamo = $this->db->insert_id();

            // Actualizar estado de la publicación
            $this->db->where('idPublicacion', $pub->idPublicacion);
            $this->db->update('PUBLICACION', array(
                'estado' => ESTADO_PUBLICACION_EN_CONSULTA,
                'fechaActualizacion' => $fechaActual->format('Y-m-d H:i:s'),
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
        if (!$idUsuario) {
            log_message('error', 'Se intentó obtener préstamos activos sin proporcionar un ID de usuario válido');
            return array();
        }

        // Seleccionar solo campos requeridos y asegurar que no sean null
        $this->db->select('
            p.idPrestamo,
            p.fechaPrestamo,
            p.horaInicio,
            p.estadoPrestamo,
            p.fechaCreacion,
            pub.titulo,
            pub.idPublicacion,
            e.nombreEditorial,
            t.nombreTipo,
            CONCAT(
                DATE_FORMAT(p.fechaPrestamo, "%d/%m/%Y"),
                " ",
                TIME_FORMAT(p.horaInicio, "%H:%i")
            ) as fecha_hora_prestamo,
            COALESCE(ds.observaciones, "") as observaciones
        ');
        $this->db->from('PRESTAMO p');
        $this->db->join('SOLICITUD_PRESTAMO sp', 'sp.idSolicitud = p.idSolicitud');
        $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
        $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
        $this->db->join('EDITORIAL e', 'pub.idEditorial = e.idEditorial');
        $this->db->join('TIPO t', 'pub.idTipo = t.idTipo');
        
        // Asegurar que todos los campos requeridos no sean null
        $this->db->where('p.idPrestamo IS NOT NULL');
        $this->db->where('p.fechaPrestamo IS NOT NULL');
        $this->db->where('p.horaInicio IS NOT NULL');
        $this->db->where('pub.titulo IS NOT NULL');
        $this->db->where('e.nombreEditorial IS NOT NULL');
        $this->db->where('t.nombreTipo IS NOT NULL');
        
        // Condiciones de préstamo activo
        $this->db->where([
            'sp.idUsuario' => $idUsuario,
            'p.estadoPrestamo' => ESTADO_PRESTAMO_ACTIVO,
            'p.estado' => 1
        ]);
        
        $this->db->order_by('p.fechaPrestamo', 'DESC');
        $this->db->order_by('p.horaInicio', 'DESC');
        
        $resultado = $this->db->get();
        
        if (!$resultado) {
            log_message('error', 'Error en la consulta de préstamos activos: ' . $this->db->error()['message']);
            return array();
        }

        // Validar cada registro antes de devolverlo
        $prestamos_validados = array();
        foreach ($resultado->result() as $prestamo) {
            if ($this->_validar_prestamo($prestamo)) {
                $prestamos_validados[] = $prestamo;
            } else {
                log_message('warning', 'Préstamo ID:' . $prestamo->idPrestamo . ' descartado por datos incompletos');
            }
        }

        return $prestamos_validados;
    }

    private function _validar_prestamo($prestamo) {
        $campos_requeridos = [
            'idPrestamo',
            'fechaPrestamo',
            'horaInicio',
            'estadoPrestamo',
            'fechaCreacion',
            'titulo',
            'idPublicacion',
            'nombreEditorial',
            'nombreTipo'
        ];

        foreach ($campos_requeridos as $campo) {
            if (!isset($prestamo->$campo) || $prestamo->$campo === '' || $prestamo->$campo === null) {
                log_message('error', "Campo requerido '$campo' faltante o null en préstamo ID: " . 
                    (isset($prestamo->idPrestamo) ? $prestamo->idPrestamo : 'desconocido'));
                return false;
            }
        }

        return true;
    }

    /**
     * Añadir un nuevo préstamo con validación estricta
     */
    public function agregar_prestamo($datos_prestamo) {
        // Validar campos requeridos
        $campos_requeridos = [
            'idSolicitud',
            'fechaPrestamo',
            'horaInicio',
            'estadoPrestamo',
            'estado',
            'idUsuarioCreador'
        ];

        foreach ($campos_requeridos as $campo) {
            if (!isset($datos_prestamo[$campo]) || empty($datos_prestamo[$campo])) {
                log_message('error', "Campo requerido '$campo' faltante al crear préstamo");
                return false;
            }
        }

        // Asegurar que la fecha de creación esté presente
        $datos_prestamo['fechaCreacion'] = date('Y-m-d H:i:s');

        try {
            $this->db->trans_start();
            $this->db->insert('PRESTAMO', $datos_prestamo);
            $id_prestamo = $this->db->insert_id();
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Error al insertar préstamo: ' . $this->db->error()['message']);
                return false;
            }

            return $id_prestamo;

        } catch (Exception $e) {
            log_message('error', 'Exception al crear préstamo: ' . $e->getMessage());
            return false;
        }
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

/*public function finalizar_prestamo($idPrestamo, $idEncargado, $estadoDevolucion) {
    $this->db->trans_start();
    
    try {
        // Establecer la zona horaria correcta para Bolivia
        date_default_timezone_set('America/La_Paz');
        
        // Crear objeto DateTime para manejar las fechas correctamente
        $fechaHoraActual = new DateTime();
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
            'fechaDevolucion' => $fechaHoraActual->format('Y-m-d H:i:s'),
            'fechaActualizacion' => $fechaHoraActual->format('Y-m-d H:i:s')
        ];
        
        $this->db->where('idPrestamo', $idPrestamo);
        if (!$this->db->update('PRESTAMO', $data_prestamo)) {
            throw new Exception('Error al actualizar el préstamo');
        }

        // Actualizar la publicación a DISPONIBLE
        $data_publicacion = [
            'estado' => ESTADO_PUBLICACION_DISPONIBLE,
            'fechaActualizacion' => $fechaHoraActual->format('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idEncargado
        ];
        
        $this->db->where('idPublicacion', $prestamo->idPublicacion);
        if (!$this->db->update('PUBLICACION', $data_publicacion)) {
            throw new Exception('Error al actualizar el estado de la publicación');
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
*/
public function finalizar_prestamo($idPrestamo, $idEncargado, $estadoDevolucion) {
    $this->db->trans_start();
    
    try {
        date_default_timezone_set('America/La_Paz');
        $fechaActual = new DateTime();

        // Obtener datos del préstamo
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
            'fechaDevolucion' => $fechaActual->format('Y-m-d H:i:s'), // DATETIME
            'horaDevolucion' => $fechaActual->format('H:i:s'),        // TIME
            'fechaActualizacion' => $fechaActual->format('Y-m-d H:i:s')
        ];
        
        $this->db->where('idPrestamo', $idPrestamo);
        if (!$this->db->update('PRESTAMO', $data_prestamo)) {
            throw new Exception('Error al actualizar el préstamo');
        }

        // Actualizar estado de la publicación
        $data_publicacion = [
            'estado' => ESTADO_PUBLICACION_DISPONIBLE,
            'fechaActualizacion' => $fechaActual->format('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idEncargado
        ];
        
        $this->db->where('idPublicacion', $prestamo->idPublicacion);
        if (!$this->db->update('PUBLICACION', $data_publicacion)) {
            throw new Exception('Error al actualizar el estado de la publicación');
        }

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
        p.fechaDevolucion,
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

public function obtener_prestamo_por_publicacion($idPublicacion) {
    $this->db->select('
        p.idPrestamo,
        p.fechaPrestamo,
        p.horaInicio,
        p.horaDevolucion,
        p.estadoPrestamo,
        sp.idSolicitud,
        sp.idUsuario,
        pub.idPublicacion,
        pub.titulo,
        u.nombres,
        u.apellidoPaterno,
        enc.nombres as nombre_encargado,
        enc.apellidoPaterno as apellido_encargado
    ');
    
    $this->db->from('PRESTAMO p');
    $this->db->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
    $this->db->join('DETALLE_SOLICITUD ds', 'ds.idSolicitud = sp.idSolicitud');
    $this->db->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
    $this->db->join('USUARIO u', 'sp.idUsuario = u.idUsuario');
    $this->db->join('USUARIO enc', 'p.idEncargadoPrestamo = enc.idUsuario', 'left');
    $this->db->where('pub.idPublicacion', $idPublicacion);
    $this->db->where('p.estado', 1);
    $this->db->order_by('p.fechaPrestamo', 'DESC');
    $this->db->limit(1);

    return $this->db->get()->row();
}
}