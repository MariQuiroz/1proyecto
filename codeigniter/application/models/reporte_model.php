

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
/*
    public function obtener_reporte_prestamos($filtros, $rol) {
        // Campos base para todos los roles
        $campos_seleccion = [
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
            't.nombreTipo',
            'e.nombreEditorial',
            'TIMESTAMPDIFF(MINUTE, p.fechaPrestamo, COALESCE(p.horaDevolucion, NOW())) as tiempo_prestamo',
            'CASE 
                WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_ACTIVO . ' 
                    AND DATEDIFF(NOW(), p.fechaPrestamo) <= 1 THEN "Activo"
                WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' THEN "Devuelto"
                WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_ACTIVO . ' 
                    AND DATEDIFF(NOW(), p.fechaPrestamo) > 1 THEN "Vencido"
                ELSE "Desconocido"
            END as estado_prestamo'
        ];
    
        // Campos adicionales para administradores
        if ($rol === 'administrador') {
            $campos_adicionales = [
                'TIMESTAMPDIFF(HOUR, p.fechaPrestamo, p.horaDevolucion) as horas_prestamo',
                '(SELECT COUNT(*) FROM PRESTAMO p2 
                  JOIN SOLICITUD_PRESTAMO sp2 ON p2.idSolicitud = sp2.idSolicitud 
                  WHERE sp2.idUsuario = u.idUsuario) as total_prestamos_usuario',
                '(SELECT AVG(TIMESTAMPDIFF(HOUR, p3.fechaPrestamo, p3.horaDevolucion)) 
                  FROM PRESTAMO p3 
                  JOIN SOLICITUD_PRESTAMO sp3 ON p3.idSolicitud = sp3.idSolicitud 
                  WHERE sp3.idUsuario = u.idUsuario 
                  AND p3.horaDevolucion IS NOT NULL) as promedio_horas_prestamo'
            ];
            $campos_seleccion = array_merge($campos_seleccion, $campos_adicionales);
        }
    
        $this->db->select($campos_seleccion);
        
        // JOINS necesarios
        $this->db->from('PRESTAMO p')
            ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud')
            ->join('USUARIO u', 'sp.idUsuario = u.idUsuario')
            ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud')
            ->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion')
            ->join('EDITORIAL e', 'pub.idEditorial = e.idEditorial')
            ->join('TIPO t', 'pub.idTipo = t.idTipo')
            ->join('USUARIO enc', 'p.idEncargadoPrestamo = enc.idUsuario');
    
        // Aplicar filtros
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(p.fechaPrestamo) >=', $filtros['fecha_inicio']);
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(p.fechaPrestamo) <=', $filtros['fecha_fin']);
        }
        
        if (!empty($filtros['estado'])) {
            switch($filtros['estado']) {
                case 'activo':
                    $this->db->where('p.estadoPrestamo', ESTADO_PRESTAMO_ACTIVO);
                    $this->db->where('DATEDIFF(NOW(), p.fechaPrestamo) <=', 1);
                    break;
                case 'devuelto':
                    $this->db->where('p.estadoPrestamo', ESTADO_PRESTAMO_FINALIZADO);
                    break;
                case 'vencido':
                    $this->db->where('p.estadoPrestamo', ESTADO_PRESTAMO_ACTIVO);
                    $this->db->where('DATEDIFF(NOW(), p.fechaPrestamo) >', 1);
                    break;
            }
        }
    
        if (!empty($filtros['id_encargado'])) {
            $this->db->where('p.idEncargadoPrestamo', $filtros['id_encargado']);
        }
    
        // Si es encargado, solo mostrar sus préstamos
        if ($rol === 'encargado') {
            $this->db->where('p.idEncargadoPrestamo', $this->session->userdata('idUsuario'));
        }
    
        $this->db->order_by('p.fechaPrestamo', 'DESC');
    
        return $this->db->get()->result();
    }
    
    private function _aplicar_joins_prestamos() {
        $this->db->from('PRESTAMO p')
            ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud')
            ->join('USUARIO u', 'sp.idUsuario = u.idUsuario')
            ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud')
            ->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion')
            ->join('EDITORIAL e', 'pub.idEditorial = e.idEditorial')
            ->join('TIPO t', 'pub.idTipo = t.idTipo')
            ->join('USUARIO enc', 'p.idEncargadoPrestamo = enc.idUsuario');
    }

    public function calcular_tiempos_promedio_devolucion($filtros) {
        $this->db->select([
            't.idTipo',
            't.nombreTipo',
            'COUNT(p.idPrestamo) as total_prestamos',
            'ROUND(AVG(CASE 
                WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' 
                THEN TIMESTAMPDIFF(HOUR, p.fechaPrestamo, p.horaDevolucion) 
                END), 2) as promedio_horas_devolucion',
            'SUM(CASE 
                WHEN TIMESTAMPDIFF(DAY, p.fechaPrestamo, p.horaDevolucion) > 1 
                THEN 1 
                ELSE 0 
                END) as devoluciones_tardias'
        ]);
    
        $this->db->from('PRESTAMO p')
            ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud')
            ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud')
            ->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion')
            ->join('TIPO t', 'pub.idTipo = t.idTipo');
    
        // Aplicar filtros si existen
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(p.fechaPrestamo) >=', $filtros['fecha_inicio']);
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(p.fechaPrestamo) <=', $filtros['fecha_fin']);
        }
    
        if (!empty($filtros['id_encargado'])) {
            $this->db->where('p.idEncargadoPrestamo', $filtros['id_encargado']);
        }
    
        $this->db->group_by(['t.idTipo', 't.nombreTipo'])
            ->having('total_prestamos > 0')
            ->order_by('total_prestamos', 'DESC');
    
        $resultados = $this->db->get()->result();
    
        // Calcular métricas adicionales para cada tipo
        foreach ($resultados as &$resultado) {
            $resultado->tasa_demora = ($resultado->total_prestamos > 0) 
                ? round(($resultado->devoluciones_tardias / $resultado->total_prestamos) * 100, 2)
                : 0;
                
            $resultado->eficiencia = $this->_calcular_eficiencia_tipo(
                $resultado->total_prestamos,
                $resultado->devoluciones_tardias,
                $resultado->promedio_horas_devolucion
            );
        }
    
        return $resultados;
    }

    private function _calcular_eficiencia_tipo($total_prestamos, $devoluciones_tardias, $promedio_horas) {
        if ($total_prestamos == 0) return 0;
    
        // Factor de tiempo (normalizado a 24 horas)
        $factor_tiempo = min(1, 24 / max(1, $promedio_horas));
        
        // Factor de devoluciones a tiempo
        $devoluciones_tiempo = $total_prestamos - $devoluciones_tardias;
        $factor_devoluciones = $devoluciones_tiempo / $total_prestamos;
    
        // Cálculo ponderado de eficiencia
        $eficiencia = ($factor_tiempo * 0.4) + ($factor_devoluciones * 0.6);
        
        return round($eficiencia * 100, 2);
    }
    
    public function obtener_estadisticas_devoluciones($filtros) {
        $this->db->select([
            'COUNT(p.idPrestamo) as total_prestamos',
            'SUM(CASE 
                WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' 
                THEN 1 ELSE 0 END) as prestamos_devueltos',
            'ROUND(AVG(CASE 
                WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' 
                THEN TIMESTAMPDIFF(HOUR, p.fechaPrestamo, p.horaDevolucion) 
                END), 2) as promedio_horas_total',
            'COUNT(DISTINCT sp.idUsuario) as usuarios_unicos'
        ]);
    
        $this->db->from('PRESTAMO p')
            ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
    
        // Aplicar filtros
        $this->_aplicar_filtros_fecha($filtros);
    
        $estadisticas = $this->db->get()->row();
    
        // Calcular métricas adicionales
        if ($estadisticas) {
            $estadisticas->tasa_devolucion = $estadisticas->total_prestamos > 0 
                ? round(($estadisticas->prestamos_devueltos / $estadisticas->total_prestamos) * 100, 2) 
                : 0;
                
            $estadisticas->prestamos_por_usuario = $estadisticas->usuarios_unicos > 0 
                ? round($estadisticas->total_prestamos / $estadisticas->usuarios_unicos, 2) 
                : 0;
        }
    
        return $estadisticas;
    }
    
    private function _aplicar_filtros_fecha($filtros) {
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(p.fechaPrestamo) >=', $filtros['fecha_inicio']);
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(p.fechaPrestamo) <=', $filtros['fecha_fin']);
        }
    
        if (!empty($filtros['id_encargado'])) {
            $this->db->where('p.idEncargadoPrestamo', $filtros['id_encargado']);
        }
    }


    public function calcular_predicciones_demanda() {
        // Obtener datos históricos de los últimos 6 meses
        $fecha_inicio = date('Y-m-d', strtotime('-6 months'));
        
        $this->db->select([
            'DATE_FORMAT(p.fechaPrestamo, "%Y-%m") as mes',
            'COUNT(p.idPrestamo) as total_prestamos',
            't.nombreTipo',
            'COUNT(DISTINCT sp.idUsuario) as usuarios_unicos'
        ]);

        $this->_aplicar_joins_prestamos();
        $this->db->where('p.fechaPrestamo >=', $fecha_inicio);
        
        $this->db->group_by('mes, t.idTipo');
        $this->db->order_by('mes ASC');

        $datos_historicos = $this->db->get()->result();

        // Aplicar algoritmo simple de predicción
        return $this->_calcular_prediccion($datos_historicos);
    }

    private function _calcular_prediccion($datos_historicos) {
        $predicciones = [];
        $tipos = [];
        
        // Agrupar datos por tipo
        foreach ($datos_historicos as $dato) {
            if (!isset($tipos[$dato->nombreTipo])) {
                $tipos[$dato->nombreTipo] = [];
            }
            $tipos[$dato->nombreTipo][] = $dato->total_prestamos;
        }

        // Calcular tendencia por tipo
        foreach ($tipos as $tipo => $valores) {
            $n = count($valores);
            if ($n >= 3) {
                $tendencia = ($valores[$n-1] - $valores[$n-3]) / 3;
                $prediccion_siguiente_mes = $valores[$n-1] + $tendencia;
                
                $predicciones[$tipo] = [
                    'prediccion' => round($prediccion_siguiente_mes),
                    'tendencia' => round($tendencia, 2),
                    'confianza' => $this->_calcular_nivel_confianza($valores),
                    'historico' => $valores
                ];
            }
        }

        return $predicciones;
    }

    private function _calcular_nivel_confianza($valores) {
        // Cálculo simple de la variabilidad de los datos
        $promedio = array_sum($valores) / count($valores);
        $varianza = array_reduce($valores, function($carry, $item) use ($promedio) {
            return $carry + pow($item - $promedio, 2);
        }, 0) / count($valores);
        
        $desviacion_estandar = sqrt($varianza);
        $coeficiente_variacion = ($promedio != 0) ? ($desviacion_estandar / $promedio) : 1;
        
        // Convertir a porcentaje de confianza (inversamente proporcional al coeficiente de variación)
        $confianza = (1 - min($coeficiente_variacion, 1)) * 100;
        return round($confianza, 1);
    }

    public function obtener_metricas_eficiencia($filtros) {
        $this->db->select([
            'enc.idUsuario as id_encargado',
            'enc.nombres as nombre_encargado',
            'enc.apellidoPaterno as apellido_encargado',
            'COUNT(p.idPrestamo) as total_prestamos',
            'AVG(CASE 
                WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' 
                THEN TIMESTAMPDIFF(MINUTE, p.fechaPrestamo, p.horaDevolucion) 
                END) as tiempo_promedio_proceso',
            'COUNT(DISTINCT sp.idUsuario) as usuarios_atendidos',
            'SUM(CASE WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' 
                 AND TIMESTAMPDIFF(DAY, p.fechaPrestamo, p.horaDevolucion) <= 1 
                 THEN 1 ELSE 0 END) as devoluciones_tiempo',
            'COUNT(CASE WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_ACTIVO . ' 
                   AND DATEDIFF(NOW(), p.fechaPrestamo) > 1 
                   THEN 1 END) as prestamos_vencidos'
        ]);

        $this->db->from('PRESTAMO p')
            ->join('USUARIO enc', 'p.idEncargadoPrestamo = enc.idUsuario')
            ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');

        $this->_aplicar_filtros_prestamos($filtros);
        $this->db->group_by('enc.idUsuario');
        
        $resultados = $this->db->get()->result();

        // Calcular métricas adicionales
        foreach ($resultados as &$resultado) {
            $resultado->eficiencia = $this->_calcular_indice_eficiencia(
                $resultado->devoluciones_tiempo,
                $resultado->total_prestamos,
                $resultado->prestamos_vencidos
            );
        }

        return $resultados;
    }

    public function obtener_estadisticas_prestamos($filtros) {
        $this->db->select([
            'COUNT(CASE WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_ACTIVO . ' 
                   AND DATEDIFF(NOW(), p.fechaPrestamo) <= 1 THEN 1 END) as activos',
            'COUNT(CASE WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' 
                   THEN 1 END) as devueltos',
            'COUNT(CASE WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_ACTIVO . ' 
                   AND DATEDIFF(NOW(), p.fechaPrestamo) > 1 THEN 1 END) as vencidos',
            'AVG(CASE WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' 
                 THEN TIMESTAMPDIFF(HOUR, p.fechaPrestamo, p.horaDevolucion) 
                 END) as promedio_horas_prestamo',
            'COUNT(DISTINCT sp.idUsuario) as usuarios_unicos',
            'COUNT(DISTINCT pub.idTipo) as tipos_publicaciones'
        ]);
        
        $this->_aplicar_joins_prestamos();
        $this->_aplicar_filtros_prestamos($filtros);

        $estadisticas = $this->db->get()->row();

        // Calcular métricas adicionales
        $estadisticas->tasa_devolucion = $this->_calcular_tasa_devolucion($estadisticas);
        $estadisticas->tasa_vencimiento = $this->_calcular_tasa_vencimiento($estadisticas);
        
        return $estadisticas;
    }

    private function _calcular_tasa_devolucion($estadisticas) {
        $total_prestamos = $estadisticas->activos + $estadisticas->devueltos + $estadisticas->vencidos;
        return $total_prestamos > 0 ? round(($estadisticas->devueltos / $total_prestamos) * 100, 1) : 0;
    }

    private function _calcular_tasa_vencimiento($estadisticas) {
        $total_prestamos = $estadisticas->activos + $estadisticas->devueltos + $estadisticas->vencidos;
        return $total_prestamos > 0 ? round(($estadisticas->vencidos / $total_prestamos) * 100, 1) : 0;
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
            $this->_aplicar_filtro_por_estado($filtros['estado']);
        }

        $this->db->order_by('p.fechaPrestamo', 'DESC');
    }

    private function _aplicar_filtro_por_estado($estado) {
        if (empty($estado)) {
            return;  // Si no hay estado, no aplicar filtro
        }
    
        switch($estado) {
            case 'activo':
                $this->db->where('p.estadoPrestamo', ESTADO_PRESTAMO_ACTIVO);
                $this->db->where('p.horaDevolucion IS NULL');
                $this->db->where('DATEDIFF(NOW(), p.fechaPrestamo) <= 1');
                break;
            
            case 'devuelto':
                $this->db->where('p.estadoPrestamo', ESTADO_PRESTAMO_FINALIZADO);
                break;
                
            case 'vencido':
                $this->db->where('p.estadoPrestamo', ESTADO_PRESTAMO_ACTIVO);
                $this->db->where('p.horaDevolucion IS NULL');
                $this->db->where('DATEDIFF(NOW(), p.fechaPrestamo) > 1');
                break;
                
            default:
                log_message('debug', 'Estado de préstamo no reconocido: ' . $estado);
                break;
        }
    }
    public function obtener_prestamos_por_estado($filtros) {
        $this->db->select([
            'p.estadoPrestamo',
            'COUNT(p.idPrestamo) as total',
            'COUNT(DISTINCT sp.idUsuario) as usuarios_unicos',
            'AVG(TIMESTAMPDIFF(HOUR, p.fechaPrestamo, 
                CASE WHEN p.horaDevolucion IS NOT NULL 
                    THEN p.horaDevolucion 
                    ELSE NOW() 
                END)) as promedio_horas',
            'COUNT(CASE WHEN p.horaDevolucion IS NOT NULL THEN 1 END) as devueltos',
            'COUNT(CASE WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_ACTIVO . ' 
                   AND DATEDIFF(NOW(), p.fechaPrestamo) > 1 THEN 1 END) as vencidos'
        ]);
    
        $this->db->from('PRESTAMO p')
            ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud')
            ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud')
            ->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
    
        // Aplicar filtros
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(p.fechaPrestamo) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(p.fechaPrestamo) <=', $filtros['fecha_fin']);
        }
        if (!empty($filtros['estado'])) {
            switch($filtros['estado']) {
                case 'activo':
                    $this->db->where('p.estadoPrestamo', ESTADO_PRESTAMO_ACTIVO)
                        ->where('p.horaDevolucion IS NULL')
                        ->where('DATEDIFF(NOW(), p.fechaPrestamo) <= 1');
                    break;
                case 'devuelto':
                    $this->db->where('p.estadoPrestamo', ESTADO_PRESTAMO_FINALIZADO);
                    break;
                case 'vencido':
                    $this->db->where('p.estadoPrestamo', ESTADO_PRESTAMO_ACTIVO)
                        ->where('p.horaDevolucion IS NULL')
                        ->where('DATEDIFF(NOW(), p.fechaPrestamo) > 1');
                    break;
            }
        }
        if (!empty($filtros['id_encargado'])) {
            $this->db->where('p.idEncargadoPrestamo', $filtros['id_encargado']);
        }
        if (!empty($filtros['tipo_publicacion'])) {
            $this->db->where('pub.idTipo', $filtros['tipo_publicacion']);
        }
    
        $this->db->group_by('p.estadoPrestamo');
    
        $resultados = $this->db->get()->result();
    
        // Procesar y agregar métricas adicionales
        foreach ($resultados as &$resultado) {
            $resultado->tasa_devolucion = $resultado->total > 0 ? 
                ($resultado->devueltos / $resultado->total) * 100 : 0;
            $resultado->tasa_vencimiento = $resultado->total > 0 ? 
                ($resultado->vencidos / $resultado->total) * 100 : 0;
            $resultado->eficiencia = $this->_calcular_eficiencia_estado($resultado);
        }
    
        return $resultados;
    }
    
    private function _calcular_eficiencia_estado($estado_data) {
        // Calcular índice de eficiencia basado en múltiples factores
        $pesos = [
            'tasa_devolucion' => 0.4,
            'tiempo_promedio' => 0.3,
            'tasa_vencimiento' => 0.3
        ];
    
        // Normalizar tiempo promedio (considerando 24 horas como óptimo)
        $tiempo_normalizado = min(1, 24 / max(1, $estado_data->promedio_horas));
        
        // Calcular score ponderado
        $score = ($estado_data->tasa_devolucion/100 * $pesos['tasa_devolucion']) +
                 ($tiempo_normalizado * $pesos['tiempo_promedio']) +
                 ((100 - $estado_data->tasa_vencimiento)/100 * $pesos['tasa_vencimiento']);
    
        return round($score * 100, 1);
    }
    public function obtener_estadisticas_mensuales() {
        $this->db->select([
            'MONTH(p.fechaPrestamo) as mes',
            'YEAR(p.fechaPrestamo) as año',
            'COUNT(CASE 
                WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_ACTIVO . ' 
                AND p.horaDevolucion IS NULL 
                AND DATEDIFF(NOW(), p.fechaPrestamo) <= 1 THEN 1 
            END) as activos',
            'COUNT(CASE 
                WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' THEN 1 
            END) as devueltos',
            'COUNT(CASE 
                WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_ACTIVO . ' 
                AND p.horaDevolucion IS NULL 
                AND DATEDIFF(NOW(), p.fechaPrestamo) > 1 THEN 1 
            END) as vencidos'
        ]);
        
        $this->db->from('PRESTAMO p');
        $this->db->where('p.fechaPrestamo >= DATE_SUB(NOW(), INTERVAL 6 MONTH)');
        $this->db->group_by(['YEAR(p.fechaPrestamo)', 'MONTH(p.fechaPrestamo)']);
        $this->db->order_by('YEAR(p.fechaPrestamo)', 'MONTH(p.fechaPrestamo)');
        
        return $this->db->get()->result();
    }
    // Método faltante que causaba el error
    public function calcular_tiempos_devolucion($filtros) {
        $this->db->select([
            'p.idPrestamo',
            'TIMESTAMPDIFF(HOUR, p.fechaPrestamo, COALESCE(p.horaDevolucion, NOW())) as horas_prestamo',
            'pub.titulo',
            't.nombreTipo',
            'u.nombres',
            'u.apellidoPaterno',
            'CASE 
                WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' THEN "Devuelto"
                WHEN TIMESTAMPDIFF(DAY, p.fechaPrestamo, NOW()) > 1 THEN "Vencido"
                ELSE "En préstamo"
            END as estado'
        ]);

        $this->db->from('PRESTAMO p')
            ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud')
            ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud')
            ->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion')
            ->join('TIPO t', 'pub.idTipo = t.idTipo')
            ->join('USUARIO u', 'sp.idUsuario = u.idUsuario');

        $this->_aplicar_filtros_prestamos($filtros);

        return $this->db->get()->result();
    }

    // Método mejorado para obtener datos agregados de tiempos de devolución
    public function obtener_estadisticas_tiempos_devolucion($filtros) {
        $this->db->select([
            't.nombreTipo',
            'COUNT(p.idPrestamo) as total_prestamos',
            'ROUND(AVG(TIMESTAMPDIFF(HOUR, p.fechaPrestamo, 
                CASE WHEN p.horaDevolucion IS NOT NULL 
                    THEN p.horaDevolucion 
                    ELSE NOW() 
                END)), 2) as promedio_horas',
            'COUNT(CASE WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' THEN 1 END) as devueltos',
            'COUNT(CASE WHEN TIMESTAMPDIFF(DAY, p.fechaPrestamo, NOW()) > 1 
                   AND p.estadoPrestamo = ' . ESTADO_PRESTAMO_ACTIVO . ' THEN 1 END) as vencidos'
        ]);

        $this->db->from('PRESTAMO p')
            ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud')
            ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud')
            ->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion')
            ->join('TIPO t', 'pub.idTipo = t.idTipo');

        $this->_aplicar_filtros_prestamos($filtros);

        $this->db->group_by('t.nombreTipo');
        
        $resultados = $this->db->get()->result();
        
        // Agregar cálculos adicionales
        foreach ($resultados as &$resultado) {
            $resultado->tasa_devolucion = $this->_calcular_tasa_devolucion_tipo(
                $resultado->devueltos, 
                $resultado->total_prestamos
            );
            $resultado->tasa_cumplimiento = $this->_calcular_tasa_cumplimiento(
                $resultado->total_prestamos,
                $resultado->vencidos
            );
        }

        return $resultados;
    }

    private function _calcular_tasa_devolucion_tipo($devueltos, $total) {
        return $total > 0 ? round(($devueltos / $total) * 100, 2) : 0;
    }

    private function _calcular_tasa_cumplimiento($total, $vencidos) {
        $prestamos_tiempo = $total - $vencidos;
        return $total > 0 ? round(($prestamos_tiempo / $total) * 100, 2) : 0;
    }

    // Método para obtener alertas de devolución
    public function obtener_alertas_devolucion() {
        $this->db->select([
            'p.idPrestamo',
            'u.nombres',
            'u.apellidoPaterno',
            'pub.titulo',
            'p.fechaPrestamo',
            'TIMESTAMPDIFF(HOUR, p.fechaPrestamo, NOW()) as horas_transcurridas',
            'enc.nombres as nombre_encargado',
            'enc.apellidoPaterno as apellido_encargado'
        ]);

        $this->db->from('PRESTAMO p')
            ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud')
            ->join('USUARIO u', 'sp.idUsuario = u.idUsuario')
            ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud')
            ->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion')
            ->join('USUARIO enc', 'p.idEncargadoPrestamo = enc.idUsuario')
            ->where('p.estadoPrestamo', ESTADO_PRESTAMO_ACTIVO)
            ->where('p.horaDevolucion IS NULL')
            ->where('TIMESTAMPDIFF(HOUR, p.fechaPrestamo, NOW()) >', 20)
            ->order_by('horas_transcurridas', 'DESC');

        return $this->db->get()->result();
    }

    // Método para logging de operaciones
    private function _log_operacion($operacion, $detalles) {
        $data = [
            'operacion' => $operacion,
            'detalles' => json_encode($detalles),
            'fecha' => date('Y-m-d H:i:s'),
            'idUsuario' => $this->session->userdata('idUsuario')
        ];
        
        $this->db->insert('LOG_OPERACIONES', $data);
    }

    
    public function calcular_metricas_eficiencia($filtros) {
        $this->db->select([
            'enc.nombres as nombre_encargado',
            'enc.apellidoPaterno as apellido_encargado',
            'COUNT(p.idPrestamo) as total_prestamos',
            'AVG(TIMESTAMPDIFF(MINUTE, p.fechaPrestamo, 
                CASE WHEN p.horaDevolucion IS NOT NULL THEN p.horaDevolucion ELSE NOW() END)) 
                as tiempo_promedio_proceso',
            'COUNT(DISTINCT sp.idUsuario) as usuarios_atendidos',
            'COUNT(CASE WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' 
                AND TIMESTAMPDIFF(DAY, p.fechaPrestamo, p.horaDevolucion) <= 1 THEN 1 END) 
                as devoluciones_tiempo',
            'COUNT(CASE WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_ACTIVO . ' 
                AND DATEDIFF(NOW(), p.fechaPrestamo) > 1 THEN 1 END) as prestamos_vencidos'
        ]);
        
        $this->db->from('PRESTAMO p')
            ->join('USUARIO enc', 'p.idEncargadoPrestamo = enc.idUsuario')
            ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');
        
        $this->_aplicar_filtros_fecha($filtros);
        
        $this->db->group_by('enc.idUsuario')
            ->order_by('total_prestamos', 'DESC');
        
        $resultados = $this->db->get()->result();
        
        // Calcular métricas adicionales
        foreach ($resultados as &$resultado) {
            $resultado->eficiencia = $this->_calcular_indice_eficiencia(
                $resultado->devoluciones_tiempo,
                $resultado->total_prestamos,
                $resultado->prestamos_vencidos
            );
        }
        
        return $resultados;
    }
    
    private function _calcular_indice_eficiencia($devoluciones_tiempo, $total_prestamos, $prestamos_vencidos) {
        if ($total_prestamos == 0) return 0;
        
        $tasa_devoluciones = $devoluciones_tiempo / $total_prestamos;
        $tasa_vencimientos = $prestamos_vencidos / $total_prestamos;
        
        // Fórmula ponderada para el índice de eficiencia
        $indice = ($tasa_devoluciones * 0.7) + ((1 - $tasa_vencimientos) * 0.3);
        return round($indice * 100, 1);
    }
    









public function calcular_tasa_renovacion($filtros) {
    $this->db->select([
        'COUNT(DISTINCT p.idSolicitud) as total_solicitudes',
        'COUNT(DISTINCT CASE 
            WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' 
            AND EXISTS (
                SELECT 1 
                FROM SOLICITUD_PRESTAMO sp2 
                JOIN PRESTAMO p2 ON p2.idSolicitud = sp2.idSolicitud 
                WHERE sp2.idUsuario = sp.idUsuario 
                AND sp2.fechaSolicitud > sp.fechaSolicitud
            )
            THEN p.idPrestamo 
            END) as prestamos_renovados'
    ]);

    $this->db->from('PRESTAMO p')
        ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');

    $this->_aplicar_filtros_fecha($filtros);

    $resultado = $this->db->get()->row();

    if ($resultado && $resultado->total_solicitudes > 0) {
        $tasa_renovacion = ($resultado->prestamos_renovados / $resultado->total_solicitudes) * 100;
        return round($tasa_renovacion, 2);
    }

    return 0;
}

public function obtener_publicaciones_mas_solicitadas($filtros, $limite = 5) {
    $this->db->select([
        'pub.idPublicacion',
        'pub.titulo',
        't.nombreTipo',
        'e.nombreEditorial',
        'COUNT(DISTINCT p.idPrestamo) as total_prestamos',
        'COUNT(DISTINCT sp.idUsuario) as usuarios_unicos',
        'ROUND(AVG(TIMESTAMPDIFF(HOUR, p.fechaPrestamo, 
            CASE WHEN p.horaDevolucion IS NOT NULL 
                THEN p.horaDevolucion 
                ELSE NOW() 
            END)), 2) as promedio_horas_prestamo'
    ]);

    $this->db->from('PRESTAMO p')
        ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud')
        ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud')
        ->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion')
        ->join('TIPO t', 'pub.idTipo = t.idTipo')
        ->join('EDITORIAL e', 'pub.idEditorial = e.idEditorial');

    $this->_aplicar_filtros_fecha($filtros);

    $this->db->group_by(['pub.idPublicacion', 'pub.titulo', 't.nombreTipo', 'e.nombreEditorial'])
        ->order_by('total_prestamos', 'DESC')
        ->limit($limite);

    $publicaciones = $this->db->get()->result();

    foreach ($publicaciones as &$publicacion) {
        $publicacion->tasa_uso = $this->_calcular_tasa_uso($publicacion->idPublicacion);
    }

    return $publicaciones;
}

public function obtener_usuarios_frecuentes($filtros, $limite = 5) {
    $this->db->select([
        'u.idUsuario',
        'u.nombres',
        'u.apellidoPaterno',
        'u.profesion',
        'COUNT(DISTINCT p.idPrestamo) as total_prestamos',
        'COUNT(DISTINCT ds.idPublicacion) as publicaciones_diferentes',
        'ROUND(AVG(CASE 
            WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' 
            THEN TIMESTAMPDIFF(HOUR, p.fechaPrestamo, p.horaDevolucion)
            END), 2) as promedio_horas_prestamo',
        'COUNT(DISTINCT CASE 
            WHEN p.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' 
            AND TIMESTAMPDIFF(DAY, p.fechaPrestamo, p.horaDevolucion) <= 1 
            THEN p.idPrestamo 
            END) as devoluciones_tiempo'
    ]);

    $this->db->from('PRESTAMO p')
        ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud')
        ->join('USUARIO u', 'sp.idUsuario = u.idUsuario')
        ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud');

    $this->_aplicar_filtros_fecha($filtros);

    $this->db->group_by(['u.idUsuario', 'u.nombres', 'u.apellidoPaterno', 'u.profesion'])
        ->having('total_prestamos > 0')
        ->order_by('total_prestamos', 'DESC')
        ->limit($limite);

    $usuarios = $this->db->get()->result();

    foreach ($usuarios as &$usuario) {
        $usuario->indice_puntualidad = $this->_calcular_indice_puntualidad(
            $usuario->devoluciones_tiempo,
            $usuario->total_prestamos
        );
    }

    return $usuarios;
}

private function _calcular_tasa_uso($idPublicacion) {
    $this->db->select([
        'COUNT(DISTINCT p.idPrestamo) as total_prestamos',
        'DATEDIFF(MAX(p.fechaPrestamo), MIN(p.fechaPrestamo)) as dias_uso'
    ]);

    $this->db->from('PRESTAMO p')
        ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud')
        ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud')
        ->where('ds.idPublicacion', $idPublicacion);

    $resultado = $this->db->get()->row();

    if ($resultado && $resultado->dias_uso > 0) {
        return round($resultado->total_prestamos / ($resultado->dias_uso / 30), 2); // Préstamos por mes
    }

    return 0;
}

private function _calcular_indice_puntualidad($devoluciones_tiempo, $total_prestamos) {
    if ($total_prestamos == 0) return 0;

    $tasa_puntualidad = $devoluciones_tiempo / $total_prestamos;
    return round($tasa_puntualidad * 100, 2);
}

public function analizar_dias_mayor_demanda($filtros) {
    $this->db->select([
        'DAYOFWEEK(p.fechaPrestamo) as dia_numero',
        'DAYNAME(p.fechaPrestamo) as dia_nombre',
        'COUNT(p.idPrestamo) as total_prestamos',
        'COUNT(DISTINCT sp.idUsuario) as usuarios_unicos',
        'ROUND(AVG(TIMESTAMPDIFF(HOUR, p.fechaPrestamo, 
            CASE WHEN p.horaDevolucion IS NOT NULL 
                THEN p.horaDevolucion 
                ELSE NOW() 
            END)), 2) as promedio_horas_prestamo'
    ]);

    $this->db->from('PRESTAMO p')
        ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud');

    $this->_aplicar_filtros_fecha($filtros);

    $this->db->group_by(['dia_numero', 'dia_nombre'])
        ->order_by('dia_numero', 'ASC');

    $resultados = $this->db->get()->result();

    // Traducir días al español
    $dias_espanol = [
        'Sunday' => 'Domingo',
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado'
    ];

    foreach ($resultados as &$resultado) {
        $resultado->dia_nombre = $dias_espanol[$resultado->dia_nombre];
    }

    return $resultados;
}










private function _obtener_tendencias_por_dia($filtros) {
    $this->db->select([
        'DAYNAME(p.fechaPrestamo) as dia_semana',
        'DAYOFWEEK(p.fechaPrestamo) as numero_dia',
        'COUNT(p.idPrestamo) as total_prestamos',
        'COUNT(DISTINCT sp.idUsuario) as usuarios_unicos',
        'AVG(TIMESTAMPDIFF(HOUR, p.fechaPrestamo, 
            CASE WHEN p.horaDevolucion IS NOT NULL THEN p.horaDevolucion ELSE NOW() END)) 
            as promedio_horas_prestamo'
    ]);
    
    $this->db->from('PRESTAMO p')
        ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud')
        ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud')
        ->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
    
    $this->_aplicar_filtros_fecha($filtros);
    
    $this->db->group_by(['DAYNAME(p.fechaPrestamo)', 'DAYOFWEEK(p.fechaPrestamo)'])
        ->order_by('numero_dia', 'ASC');
    
    $resultados = $this->db->get()->result();

    // Ordenar los días de la semana correctamente
    $dias_ordenados = [
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    ];

    foreach ($resultados as $resultado) {
        $resultado->dia_semana = $dias_ordenados[$resultado->dia_semana] ?? $resultado->dia_semana;
    }

    return $resultados;
}

private function _obtener_tendencias_por_mes($filtros) {
    $this->db->select([
        'YEAR(p.fechaPrestamo) as anio',
        'MONTH(p.fechaPrestamo) as mes',
        'DATE_FORMAT(p.fechaPrestamo, "%Y-%m") as fecha_agrupada',
        'COUNT(p.idPrestamo) as total_prestamos',
        'COUNT(DISTINCT sp.idUsuario) as usuarios_unicos',
        'AVG(TIMESTAMPDIFF(HOUR, p.fechaPrestamo, 
            CASE WHEN p.horaDevolucion IS NOT NULL THEN p.horaDevolucion ELSE NOW() END)) 
            as promedio_horas_prestamo'
    ]);
    
    $this->db->from('PRESTAMO p')
        ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud')
        ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud')
        ->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion');
    
    $this->_aplicar_filtros_fecha($filtros);
    
    $this->db->group_by(['anio', 'mes', 'fecha_agrupada'])
        ->order_by('fecha_agrupada', 'ASC');
    
    $resultados = $this->db->get()->result();

    // Agregar nombre del mes en español
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];

    foreach ($resultados as $resultado) {
        $resultado->nombre_mes = $meses[(int)$resultado->mes];
        $resultado->etiqueta = $resultado->nombre_mes . ' ' . $resultado->anio;
    }

    return $resultados;
}

public function obtener_analisis_tendencias($filtros) {
    // Obtener tendencias por día
    $tendencias_diarias = $this->_obtener_tendencias_por_dia($filtros);
    
    // Obtener tendencias por mes
    $tendencias_mensuales = $this->_obtener_tendencias_por_mes($filtros);
    
    // Obtener tendencias por tipo de publicación
    $tendencias_por_tipo = $this->_obtener_tendencias_por_tipo($filtros);
    
    // Calcular totales y promedios generales
    $resumen = $this->_calcular_resumen_tendencias($tendencias_mensuales);
    
    return [
        'diarias' => $tendencias_diarias,
        'mensuales' => $tendencias_mensuales,
        'por_tipo' => $tendencias_por_tipo,
        'resumen' => $resumen
    ];
}

private function _calcular_resumen_tendencias($tendencias_mensuales) {
    $total_prestamos = 0;
    $total_usuarios = 0;
    $horas_totales = 0;
    $count = 0;

    foreach ($tendencias_mensuales as $tendencia) {
        $total_prestamos += $tendencia->total_prestamos;
        $total_usuarios += $tendencia->usuarios_unicos;
        if ($tendencia->promedio_horas_prestamo) {
            $horas_totales += $tendencia->promedio_horas_prestamo;
            $count++;
        }
    }

    return [
        'total_prestamos' => $total_prestamos,
        'promedio_mensual' => count($tendencias_mensuales) > 0 ? 
            round($total_prestamos / count($tendencias_mensuales), 2) : 0,
        'total_usuarios_unicos' => $total_usuarios,
        'promedio_horas' => $count > 0 ? round($horas_totales / $count, 2) : 0
    ];
}

private function _obtener_tendencias_por_tipo($filtros) {
    $this->db->select([
        't.nombreTipo',
        'COUNT(p.idPrestamo) as total_prestamos',
        'COUNT(DISTINCT sp.idUsuario) as usuarios_unicos',
        'AVG(TIMESTAMPDIFF(HOUR, p.fechaPrestamo, 
            CASE WHEN p.horaDevolucion IS NOT NULL THEN p.horaDevolucion ELSE NOW() END)) 
            as promedio_horas_prestamo'
    ]);
    
    $this->db->from('PRESTAMO p')
        ->join('SOLICITUD_PRESTAMO sp', 'p.idSolicitud = sp.idSolicitud')
        ->join('DETALLE_SOLICITUD ds', 'sp.idSolicitud = ds.idSolicitud')
        ->join('PUBLICACION pub', 'ds.idPublicacion = pub.idPublicacion')
        ->join('TIPO t', 'pub.idTipo = t.idTipo');
    
    $this->_aplicar_filtros_fecha($filtros);
    
    $this->db->group_by(['t.idTipo', 't.nombreTipo'])
        ->order_by('total_prestamos', 'DESC');
    
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
*/

/**
     * Obtiene estadísticas de préstamos por profesión de lectores
     */
    public function obtener_estadisticas_profesion($filtros = array()) {
        $this->db->select('
            u.profesion,
            COUNT(DISTINCT u.idUsuario) as total_lectores,
            COUNT(DISTINCT sp.idSolicitud) as total_solicitudes,
            COUNT(DISTINCT p.idPrestamo) as total_prestamos,
            ROUND(AVG(DATEDIFF(
                p.fechaActualizacion, 
                p.fechaCreacion
            )), 1) as promedio_dias_prestamo
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

    /**
     * Obtiene estadísticas de estado de solicitudes
     */
    public function obtener_estadisticas_solicitudes($filtros = array()) {
        $this->db->select('
            MONTH(sp.fechaSolicitud) as mes,
            YEAR(sp.fechaSolicitud) as anio,
            COUNT(sp.idSolicitud) as total_solicitudes,
            SUM(CASE WHEN sp.estadoSolicitud = ' . ESTADO_SOLICITUD_APROBADA . ' THEN 1 ELSE 0 END) as aprobadas,
            SUM(CASE WHEN sp.estadoSolicitud = ' . ESTADO_SOLICITUD_RECHAZADA . ' THEN 1 ELSE 0 END) as rechazadas,
            SUM(CASE WHEN sp.estadoSolicitud = ' . ESTADO_SOLICITUD_PENDIENTE . ' THEN 1 ELSE 0 END) as pendientes,
            SUM(CASE WHEN sp.estadoSolicitud = ' . ESTADO_SOLICITUD_FINALIZADA . ' THEN 1 ELSE 0 END) as finalizadas,
            ROUND((SUM(CASE WHEN sp.estadoSolicitud = ' . ESTADO_SOLICITUD_APROBADA . ' THEN 1 ELSE 0 END) / 
                COUNT(sp.idSolicitud)) * 100, 2) as porcentaje_aprobacion
        ');
        $this->db->from('SOLICITUD_PRESTAMO sp');
        
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

    /**
     * Obtiene estadísticas de tipos de publicaciones más solicitadas
     */
    public function obtener_estadisticas_tipos($filtros = array()) {
        $this->db->select('
            t.nombreTipo,
            COUNT(DISTINCT p.idPublicacion) as total_publicaciones,
            COUNT(DISTINCT sp.idSolicitud) as total_solicitudes,
            COUNT(DISTINCT pr.idPrestamo) as total_prestamos,
            ROUND(AVG(CASE WHEN pr.estadoPrestamo = ' . ESTADO_PRESTAMO_FINALIZADO . ' 
                THEN DATEDIFF(pr.fechaActualización, pr.fechaPrestamo)
                ELSE NULL END), 1) as promedio_dias_prestamo
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

    /**
     * Obtiene estadísticas de comportamiento de devoluciones
     */
    public function obtener_estadisticas_devoluciones($filtros = array()) {
        $this->db->select('
            MONTH(pr.fechaActualizacion) as mes,
            YEAR(pr.fechaActualizacion) as anio,
            COUNT(pr.idPrestamo) as total_devoluciones,
            SUM(CASE WHEN pr.estadoDevolucion = ' . ESTADO_DEVOLUCION_BUENO . ' THEN 1 ELSE 0 END) as estado_bueno,
            SUM(CASE WHEN pr.estadoDevolucion = ' . ESTADO_DEVOLUCION_DAÑADO . ' THEN 1 ELSE 0 END) as estado_dañado,
            SUM(CASE WHEN pr.estadoDevolucion = ' . ESTADO_DEVOLUCION_PERDIDO . ' THEN 1 ELSE 0 END) as estado_perdido,
            ROUND(AVG(DATEDIFF(pr.fechaActualizacion, pr.fechaPrestamo)), 1) as promedio_dias_prestamo,
            COUNT(CASE WHEN DATEDIFF(pr.fechaActualizacion, pr.fechaPrestamo) > 1 THEN 1 END) as devoluciones_tardias
        ');
        $this->db->from('PRESTAMO pr');
        
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(pr.fechaActualizacion) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(pr.fechaActualizacion) <=', $filtros['fecha_fin']);
        }
        
        $this->db->where('pr.estadoPrestamo', ESTADO_PRESTAMO_FINALIZADO);
        $this->db->group_by('YEAR(pr.fechaActualizacion), MONTH(pr.fechaActualizacion)');
        $this->db->order_by('anio, mes');
        
        return $this->db->get()->result();
    }

    /**
     * Obtiene métricas generales del sistema
     */
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
            COUNT(*)) * 100, 2) as tasa_aprobacion
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
            ROUND(AVG(DATEDIFF(fechaActualizacion, fechaPrestamo)), 1) as promedio_dias_prestamo
        ');
        $this->db->from('PRESTAMO');
        $this->db->where('estadoPrestamo', ESTADO_PRESTAMO_FINALIZADO);
        if (!empty($filtros['fecha_inicio'])) {
            $this->db->where('DATE(fechaPrestamo) >=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $this->db->where('DATE(fechaPrestamo) <=', $filtros['fecha_fin']);
        }
        $promedio_dias = $this->db->get()->row()->promedio_dias_prestamo;

        return array(
            'usuarios_activos' => $usuarios_activos,
            'tasa_aprobacion' => $tasa_aprobacion,
            'promedio_dias_prestamo' => $promedio_dias
        );
    }
    
}