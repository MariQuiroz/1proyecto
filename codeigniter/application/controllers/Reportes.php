<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(['Reporte_model', 'Tipo_model', 'Usuario_model']);
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form', 'download']);
        $this->_verificar_acceso();
    }

    private function _verificar_acceso() {
        if (!$this->session->userdata('login')) {
            redirect('usuarios/login');
        }

        $roles_permitidos = ['administrador', 'encargado'];
        if (!in_array($this->session->userdata('rol'), $roles_permitidos)) {
            $this->session->set_flashdata('error', 'No tienes permisos para acceder a esta sección.');
            redirect('usuarios/panel');
        }
    }
    public function index() {
        $data['metricas'] = $this->Reporte_model->obtener_metricas_generales();
        
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('reportes/dashboard', $data);
        $this->load->view('inc/footer');
    }

    public function por_profesion() {
        try {
            // Obtener y validar filtros
            $filtros = $this->_obtener_y_validar_filtros();
            
            // Obtener datos con los filtros aplicados
            $data = $this->_obtener_datos_reporte($filtros);
            
            // Manejar exportaciones si se solicitan
            if ($this->input->get('export')) {
                $this->_manejar_exportacion($this->input->get('export'), $data);
                return;
            }
            
            // Cargar vista normal
            $this->_cargar_vista_reporte($data);
            
        } catch (Exception $e) {
            log_message('error', 'Error en reporte por profesión: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error al generar el reporte: ' . $e->getMessage());
            redirect('usuarios/panel');
        }
    }

    private function _obtener_y_validar_filtros() {
        $filtros = array(
            'fecha_inicio' => $this->input->get('fecha_inicio') ?: date('Y-m-d', strtotime('-1 month')),
            'fecha_fin' => $this->input->get('fecha_fin') ?: date('Y-m-d'),
            'profesion' => $this->input->get('profesion')
        );

        // Validar fechas
        if (strtotime($filtros['fecha_fin']) < strtotime($filtros['fecha_inicio'])) {
            throw new Exception('La fecha final no puede ser menor a la fecha inicial');
        }

        // Validar profesión si está presente
        if (!empty($filtros['profesion'])) {
            $profesiones_validas = ['ESTUDIANTE UMSS', 'DOCENTE', 'INVESTIGADOR', 'PUBLICO GENERAL'];
            if (!in_array(strtoupper($filtros['profesion']), $profesiones_validas)) {
                $filtros['profesion'] = ''; // Resetear si no es válida
            }
        }

        return $filtros;
    }

    private function _obtener_datos_reporte($filtros) {
        // Obtener estadísticas y detalles
        $estadisticas = $this->Reporte_model->obtener_estadisticas_profesion($filtros);
        $detalles = $this->Reporte_model->obtener_detalle_prestamos_profesion($filtros);
        
        // Calcular totales
        $totales = array(
            'prestamos' => array_sum(array_column($estadisticas, 'total_prestamos')),
            'lectores' => array_sum(array_column($estadisticas, 'total_lectores')),
            'promedio_dias' => $this->_calcular_promedio_dias($estadisticas)
        );

        return array(
            'estadisticas' => $estadisticas,
            'detalles' => $detalles,
            'filtros' => $filtros,
            'profesion_seleccionada' => $filtros['profesion'],
            'totales' => $totales
        );
    }

    private function _calcular_promedio_dias($estadisticas) {
        $total_prestamos = array_sum(array_column($estadisticas, 'total_prestamos'));
        if ($total_prestamos == 0) return 0;

        $suma_ponderada = array_sum(array_map(function($item) {
            return $item->promedio_dias_prestamo * $item->total_prestamos;
        }, $estadisticas));

        return number_format($suma_ponderada / $total_prestamos, 1);
    }

    private function _manejar_exportacion($tipo_exportacion, $data) {
        switch ($tipo_exportacion) {
            case 'pdf':
                $this->load->library('pdf');
                $this->_generar_pdf_profesiones($data);
                break;
            
            case 'excel':
                $this->_generar_excel_profesiones($data);
                break;
                
            default:
                throw new Exception('Tipo de exportación no válido');
        }
    }

    private function _cargar_vista_reporte($data) {
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('reportes/por_profesion', $data);
        $this->load->view('inc/footer');
    }

    public function solicitudes() {
        $filtros = array(
            'fecha_inicio' => $this->input->get('fecha_inicio'),
            'fecha_fin' => $this->input->get('fecha_fin')
        );
        
        $data['estadisticas'] = $this->Reporte_model->obtener_estadisticas_solicitudes($filtros);
        $data['filtros'] = $filtros;
        
        if ($this->input->get('export') === 'pdf') {
            $this->_generar_pdf_solicitudes($data);
        } else if ($this->input->get('export') === 'excel') {
            $this->_generar_excel_solicitudes($data);
        } else {
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('reportes/solicitudes', $data);
            $this->load->view('inc/footer');
        }
    }

    public function devoluciones() {
        $filtros = array(
            'fecha_inicio' => $this->input->get('fecha_inicio'),
            'fecha_fin' => $this->input->get('fecha_fin')
        );
        
        $data['estadisticas'] = $this->Reporte_model->obtener_estadisticas_devoluciones($filtros);
        $data['filtros'] = $filtros;
        
        if ($this->input->get('export') === 'pdf') {
            $this->_generar_pdf_devoluciones($data);
        } else if ($this->input->get('export') === 'excel') {
            $this->_generar_excel_devoluciones($data);
        } else {
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('reportes/devoluciones', $data);
            $this->load->view('inc/footer');
        }
    }

    private function _generar_pdf_profesiones($data) {
        try {
            require_once APPPATH . '../vendor/autoload.php';
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf = new \Dompdf\Dompdf($options);
    
            // Preparar datos para el PDF
            $total_prestamos = array_sum(array_column($data['estadisticas'], 'total_prestamos'));
            $total_lectores = array_sum(array_column($data['estadisticas'], 'total_lectores'));
    
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: Arial, sans-serif; }
                    .header { text-align: center; margin-bottom: 20px; }
                    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f5f5f5; }
                    .badge { 
                        padding: 3px 8px;
                        border-radius: 3px;
                        font-size: 12px;
                        color: white;
                    }
                    .badge-success { background-color: #28a745; }
                    .badge-warning { background-color: #ffc107; }
                    .badge-danger { background-color: #dc3545; }
                    .summary { margin: 20px 0; padding: 10px; background-color: #f8f9fa; }
                    .footer { 
                        position: fixed;
                        bottom: 0;
                        width: 100%;
                        text-align: center;
                        font-size: 10px;
                    }
                </style>
            </head>
            <body>
                <div class="header">
        
                 <img src="' . base_url('uploads/logo_umss.jpg') . '" alt="Logo UMSS" style="width: 70px;">
                    <h1>HEMEROTECA UMSS</h1>
                    <h2>Reporte de Préstamos por Profesión</h2>
                    <h3>Período: ' . date('d/m/Y', strtotime($data['filtros']['fecha_inicio'])) . 
                        ' al ' . date('d/m/Y', strtotime($data['filtros']['fecha_fin'])) . '</h3>
                </div>
    
                <div class="summary">
                    <h4>Resumen General</h4>
                    <p>Total de Préstamos: ' . $total_prestamos . '</p>
                    <p>Total de Lectores: ' . $total_lectores . '</p>
                    <p>Promedio de Días por Préstamo: ' . $data['totales']['promedio_dias'] . '</p>
                </div>
    
                <h4>Estadísticas por Profesión</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Profesión</th>
                            <th>Total Préstamos</th>
                            <th>Lectores</th>
                            <th>Prom. Días</th>
                        </tr>
                    </thead>
                    <tbody>';
    
            foreach ($data['estadisticas'] as $est) {
                $html .= '
                    <tr>
                        <td>' . htmlspecialchars($est->profesion) . '</td>
                        <td>' . $est->total_prestamos . '</td>
                        <td>' . $est->total_lectores . '</td>
                        <td>' . number_format($est->promedio_dias_prestamo, 1) . '</td>
                    </tr>';
            }
    
            $html .= '
                    </tbody>
                </table>
    
                <h4>Detalle de Préstamos</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Lector</th>
                            <th>Profesión</th>
                            <th>Publicación</th>
                            <th>Fecha Publicación</th>
                            <th>Fecha Préstamo</th>
                            <th>Estado</th>
                            <th>Días</th>
                        </tr>
                    </thead>
                    <tbody>';
    
            foreach ($data['detalles'] as $detalle) {
                $html .= '
                    <tr>
                        <td>' . htmlspecialchars($detalle->nombres . ' ' . $detalle->apellidoPaterno) . '</td>
                        <td>' . htmlspecialchars($detalle->profesion) . '</td>
                        <td>' . htmlspecialchars($detalle->titulo_publicacion) . '</td>
                        <td>' . date('d/m/Y', strtotime($detalle->fechaPublicacion)) . '</td>
                        <td>' . date('d/m/Y', strtotime($detalle->fechaPrestamo)) . '</td>
                        <td>' . $detalle->estado_devolucion . '</td>
                        <td>' . $detalle->dias_prestamo . '</td>
                    </tr>';
            }
    
            $html .= '
                    </tbody>
                </table>
    
                <div class="footer">
                    <p>Reporte generado el ' . date('d/m/Y H:i:s') . '</p>
                </div>
            </body>
            </html>';
    
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
    
            // Generar nombre del archivo
            $filename = 'reporte_prestamos_profesion_' . date('YmdHis') . '.pdf';
            
            // Enviar al navegador
            $dompdf->stream($filename, array('Attachment' => false));
        } catch (Exception $e) {
            log_message('error', 'Error generando PDF de profesiones: ' . $e->getMessage());
            // Guardar el error detallado en los logs para debugging
            log_message('debug', 'Detalles del error: ' . $e->getTraceAsString());
            
            // Notificar al usuario
            $this->session->set_flashdata('error', 'Error al generar el PDF. Por favor intente nuevamente.');
            redirect('reportes/por_profesion');
        }
    }
    
    // Función auxiliar para formatear datos para el PDF
    private function _formatear_datos_pdf($estadisticas) {
        $datos_formateados = array();
        foreach ($estadisticas as $est) {
            $datos_formateados[] = array(
                'profesion' => $this->_formatear_profesion($est->profesion),
                'total_prestamos' => $est->total_prestamos,
                'total_lectores' => $est->total_lectores,
                'promedio_dias' => number_format($est->promedio_dias_prestamo, 1)
            );
        }
        return $datos_formateados;
    }
    
    // Función para formatear nombres de profesiones
    private function _formatear_profesion($profesion) {
        $profesiones = array(
            'ESTUDIANTE UMSS' => 'Estudiante Umss',
            'DOCENTE' => 'Docente',
            'INVESTIGADOR' => 'Investigador',
            'PUBLICO GENERAL' => 'Administrativo',
            
        );
        
        return isset($profesiones[$profesion]) ? $profesiones[$profesion] : $profesion;
    }
    
    // Función para generar el gráfico en el PDF
    private function _generar_grafico_pdf($estadisticas) {
        // Crear nuevo objeto Image
        $img = new Image();
        
        // Configurar datos del gráfico
        $data = array(
            'labels' => array_column($estadisticas, 'profesion'),
            'datasets' => array(
                array(
                    'data' => array_column($estadisticas, 'total_prestamos'),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                )
            )
        );
        
        // Configurar opciones del gráfico
        $options = array(
            'responsive' => true,
            'scales' => array(
                'y' => array(
                    'beginAtZero' => true
                )
            )
        );
        
        // Generar gráfico y retornar como base64
        return $img->generateChart('bar', $data, $options);
    }
    
    // Función para validar los datos antes de generar el PDF
    private function _validar_datos_pdf($data) {
        if (empty($data['estadisticas'])) {
            throw new Exception('No hay datos para generar el reporte');
        }
        
        foreach ($data['estadisticas'] as $est) {
            if (!isset($est->profesion) || !isset($est->total_prestamos)) {
                throw new Exception('Datos incompletos en las estadísticas');
            }
        }
        
        return true;
    }
    
    // Función para generar CSS personalizado para el PDF
    private function _get_pdf_styles() {
        return '
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    font-size: 12px;
                    line-height: 1.4;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                    padding: 20px 0;
                    border-bottom: 2px solid #003366;
                }
                .header h1 {
                    color: #003366;
                    margin: 0;
                    padding: 0;
                    font-size: 24px;
                }
                .header h3 {
                    color: #666;
                    margin: 10px 0 0 0;
                    font-size: 16px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 15px 0;
                }
                th {
                    background-color: #003366;
                    color: white;
                    padding: 10px;
                    font-size: 12px;
                    text-align: left;
                }
                td {
                    padding: 8px;
                    border-bottom: 1px solid #ddd;
                    font-size: 11px;
                }
                tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                .summary {
                    background-color: #f5f5f5;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 5px;
                }
                .summary h4 {
                    color: #003366;
                    margin: 0 0 10px 0;
                }
                .summary p {
                    margin: 5px 0;
                    color: #333;
                }
                .chart-container {
                    margin: 20px 0;
                    text-align: center;
                }
                .footer {
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    padding: 10px;
                    text-align: center;
                    font-size: 10px;
                    color: #666;
                    border-top: 1px solid #ddd;
                }
                .badge {
                    padding: 3px 8px;
                    border-radius: 3px;
                    font-size: 10px;
                    color: white;
                    display: inline-block;
                }
                .badge-success { background-color: #28a745; }
                .badge-warning { background-color: #ffc107; color: #000; }
                .badge-danger { background-color: #dc3545; }
                .text-right { text-align: right; }
                .page-break { page-break-before: always; }
                @page {
                    margin: 40px;
                    footer: html_footer;
                }
            </style>
        ';
    }
    
    // Función para renderizar encabezado HTML del PDF
    private function _render_pdf_header($filtros) {
        return '
            <div class="header">
                <img src="' . base_url('assets/img/logo-umss.png') . '" alt="Logo UMSS" style="width: 150px;">
                <h1>HEMEROTECA UMSS</h1>
                <h3>Reporte de Préstamos por Profesión</h3>
                <p>Período: ' . date('d/m/Y', strtotime($filtros['fecha_inicio'])) . 
                   ' al ' . date('d/m/Y', strtotime($filtros['fecha_fin'])) . '</p>
                ' . ($filtros['profesion'] ? '<p>Filtrado por profesión: ' . $this->_formatear_profesion($filtros['profesion']) . '</p>' : '') . '
            </div>
        ';
    }
    
    // Función para renderizar pie de página HTML del PDF
    private function _render_pdf_footer() {
        return '
            <div class="footer">
                <p>Reporte generado el ' . date('d/m/Y H:i:s') . ' - Página {PAGENO}/{nbpg}</p>
                <p>Sistema de Gestión de Hemeroteca UMSS</p>
            </div>
        ';
    }
    // Función auxiliar para encontrar elementos en array
    private function array_find($array, $callback) {
        foreach ($array as $item) {
            if ($callback($item)) {
                return $item;
            }
        }
        return null;
    }

    private function _generar_excel_profesiones($data) {
        // Preparar datos para la exportación
        $export_data = array();
        
        // Datos estadísticos
        foreach ($data['estadisticas'] as $est) {
            $export_data[] = array(
                'Profesión' => $this->_formatear_profesion($est->profesion),
                'Total Préstamos' => $est->total_prestamos,
                'Total Lectores' => $est->total_lectores,
                'Promedio Días' => number_format($est->promedio_dias_prestamo, 1)
            );
        }
        
        // Agregar línea en blanco como separador
        $export_data[] = array(
            'Profesión' => '',
            'Total Préstamos' => '',
            'Total Lectores' => '',
            'Promedio Días' => ''
        );
        
        // Agregar totales
        $export_data[] = array(
            'Profesión' => 'TOTALES',
            'Total Préstamos' => $data['totales']['prestamos'],
            'Total Lectores' => $data['totales']['lectores'],
            'Promedio Días' => number_format($data['totales']['promedio_dias'], 1)
        );
        
        // Agregar línea en blanco como separador
        $export_data[] = array(
            'Profesión' => '',
            'Total Préstamos' => '',
            'Total Lectores' => '',
            'Promedio Días' => ''
        );
        
        // Agregar detalles de préstamos
        $export_data[] = array(
            'Lector' => 'LECTOR',
            'Profesión' => 'PROFESIÓN',
            'Publicación' => 'PUBLICACIÓN',
            'Fecha Publicación' => 'FECHA PUBLICACIÓN',
            'Fecha Préstamo' => 'FECHA PRÉSTAMO',
            'Fecha Devolución' => 'FECHA DEVOLUCIÓN',
            'Estado' => 'ESTADO',
            'Días' => 'DÍAS'
        );
        
        foreach ($data['detalles'] as $detalle) {
            $export_data[] = array(
                'Lector' => $detalle->nombres . ' ' . $detalle->apellidoPaterno,
                'Profesión' => $this->_formatear_profesion($detalle->profesion),
                'Publicación' => $detalle->titulo_publicacion,
                'Fecha Publicación' => date('d/m/Y', strtotime($detalle->fechaPublicacion)),
                'Fecha Préstamo' => date('d/m/Y', strtotime($detalle->fechaPrestamo)),
                'Fecha Devolución' => $detalle->fechaDevolucion ? date('d/m/Y', strtotime($detalle->fechaDevolucion)) : 'En préstamo',
                'Estado' => $detalle->estado_devolucion,
                'Días' => $detalle->dias_prestamo
            );
        }
    
        // Cargar la librería y exportar
        $this->load->library('excel');
        $this->excel->export_to_excel($export_data, 'Reporte_Prestamos_Por_Profesion_' . date('Y-m-d'));
    }

    private function _generar_pdf_solicitudes($data) {
        try {
            require_once APPPATH . '../vendor/autoload.php';
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf = new \Dompdf\Dompdf($options);
    
            // Calcular totales
            $total_solicitudes = 0;
            $total_aprobadas = 0;
            $total_rechazadas = 0;
            $total_pendientes = 0;
    
            foreach ($data['estadisticas'] as $est) {
                $total_solicitudes += $est->total_solicitudes;
                $total_aprobadas += $est->aprobadas;
                $total_rechazadas += $est->rechazadas;
                $total_pendientes += $est->pendientes;
            }
    
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Reporte Detallado de Solicitudes - Hemeroteca UMSS</title>
                <style>
                    /* Estilos anteriores... */
                </style>
            </head>
            <body>
                <div class="header">
                    <img src="' . base_url('assets/img/logo-umss.png') . '" alt="Logo UMSS">
                    <h1>HEMEROTECA UMSS</h1>
                    <h2>REPORTE DETALLADO DE SOLICITUDES</h2>
                </div>
    
                <div class="filtros">
                    <strong>Periodo:</strong> ' . 
                    ($data['filtros']['fecha_inicio'] ? date('d/m/Y', strtotime($data['filtros']['fecha_inicio'])) : 'Inicio') . 
                    ' - ' . 
                    ($data['filtros']['fecha_fin'] ? date('d/m/Y', strtotime($data['filtros']['fecha_fin'])) : 'Fin') . '
                </div>
    
                <div class="resumen">
                    <h3>Resumen General</h3>
                    <table>
                        <tr>
                            <th>Total Solicitudes</th>
                            <th>Aprobadas</th>
                            <th>Rechazadas</th>
                            <th>Pendientes</th>
                            <th>% Aprobación</th>
                        </tr>
                        <tr>
                            <td style="text-align: center;">' . $total_solicitudes . '</td>
                            <td style="text-align: center;">' . $total_aprobadas . '</td>
                            <td style="text-align: center;">' . $total_rechazadas . '</td>
                            <td style="text-align: center;">' . $total_pendientes . '</td>
                            <td style="text-align: center;">' . 
                                number_format(($total_aprobadas / ($total_solicitudes ?: 1)) * 100, 1) . '%</td>
                        </tr>
                    </table>
                </div>
    
                <div class="detalle">
                    <h3>Detalle Mensual</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Total</th>
                                <th>Aprobadas</th>
                                <th>Rechazadas</th>
                                <th>Pendientes</th>
                                <th>% Aprobación</th>
                                <th>Tendencia</th>
                            </tr>
                        </thead>
                        <tbody>';
    
            $meses = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
    
            foreach ($data['estadisticas'] as $index => $est) {
                $tendencia = '';
                if ($index > 0) {
                    $anterior = $data['estadisticas'][$index - 1];
                    $dif = $est->total_solicitudes - $anterior->total_solicitudes;
                    $tendencia = $dif > 0 ? '↑' : ($dif < 0 ? '↓' : '→');
                }
    
                $html .= '
                    <tr>
                        <td>' . $meses[$est->mes] . ' ' . $est->anio . '</td>
                        <td style="text-align: right;">' . $est->total_solicitudes . '</td>
                        <td style="text-align: right;">' . $est->aprobadas . '</td>
                        <td style="text-align: right;">' . $est->rechazadas . '</td>
                        <td style="text-align: right;">' . $est->pendientes . '</td>
                        <td style="text-align: right;">' . number_format($est->porcentaje_aprobacion, 1) . '%</td>
                        <td style="text-align: center;">' . $tendencia . '</td>
                    </tr>';
            }
    
            $html .= '
                        <tr class="total">
                            <td><strong>TOTALES</strong></td>
                            <td style="text-align: right;"><strong>' . $total_solicitudes . '</strong></td>
                            <td style="text-align: right;"><strong>' . $total_aprobadas . '</strong></td>
                            <td style="text-align: right;"><strong>' . $total_rechazadas . '</strong></td>
                            <td style="text-align: right;"><strong>' . $total_pendientes . '</strong></td>
                            <td colspan="2"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
    
                <div class="footer">
                    <p>Fecha de generación: ' . date('d/m/Y H:i:s') . '</p>
                    <p>Reporte generado por el Sistema de Hemeroteca UMSS</p>
                </div>
            </body>
            </html>';
    
            $dompdf->loadHtml($html);
            $dompdf->setPaper('letter', 'portrait');
            $dompdf->render();
            $dompdf->stream('reporte_detallado_solicitudes.pdf', array('Attachment' => true));
    
        } catch (Exception $e) {
            log_message('error', 'Error al generar PDF de solicitudes: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error al generar el PDF');
            redirect('reportes/solicitudes');
        }
    }

    // En Reportes.php
public function tipos_publicaciones() {
    try {
        $filtros = $this->_obtener_filtros_tipos();
        $data = $this->_obtener_datos_reporte_tipos($filtros);
        
        switch($this->input->get('export')) {
            case 'pdf':
                $this->_generar_pdf_tipos($data);
                break;
            case 'excel':
                $this->_generar_excel_tipos($data);
                break;
            default:
                $this->_cargar_vista_tipos($data);
        }
    } catch (Exception $e) {
        log_message('error', 'Error en reporte tipos: ' . $e->getMessage());
        $this->session->set_flashdata('error', $e->getMessage());
        redirect('usuarios/panel');
    }
}

private function _obtener_filtros_tipos() {
    return [
        'fecha_inicio' => $this->input->get('fecha_inicio') ?: date('Y-m-d', strtotime('-1 month')),
        'fecha_fin' => $this->input->get('fecha_fin') ?: date('Y-m-d'),
        'tipo' => $this->input->get('tipo')
    ];
}

private function _obtener_datos_reporte_tipos($filtros) {
    return [
        'tipos' => $this->Tipo_model->obtener_tipos(),
        'estadisticas' => $this->Reporte_model->obtener_estadisticas_tipos($filtros),
        'detalles' => $this->Reporte_model->obtener_detalle_tipos($filtros),
        'filtros' => $filtros,
        'totales' => $this->_calcular_totales_tipos($filtros)
    ];
}

private function _generar_pdf_tipos($data) {
    require_once APPPATH . '../vendor/autoload.php';
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $dompdf = new \Dompdf\Dompdf($options);

    $html = $this->load->view('reportes/tipos_pdf_template', $data, true);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    $filename = 'reporte_tipos_' . date('YmdHis') . '.pdf';
    $dompdf->stream($filename, ['Attachment' => false]);
}

private function _generar_excel_tipos($data) {
    $export_data = [];
    
    // Datos estadísticos
    foreach ($data['estadisticas'] as $est) {
        $export_data[] = [
            'Tipo' => $est->nombreTipo,
            'Total Publicaciones' => $est->total_publicaciones,
            'Prestamos Activos' => $est->prestamos_activos,
            'Total Préstamos' => $est->total_prestamos
        ];
    }
    
    // Agregar detalles
    $export_data[] = [''];  // Separador
    foreach ($data['detalles'] as $det) {
        $export_data[] = [
            'Título' => $det->titulo,
            'Tipo' => $det->nombreTipo,
            'Editorial' => $det->nombreEditorial,
            'Fecha Publicación' => date('d/m/Y', strtotime($det->fechaPublicacion)),
            'Total Solicitudes' => $det->total_solicitudes
        ];
    }

    $this->load->library('excel');
    $this->excel->export_to_excel($export_data, 'Reporte_Tipos_' . date('Y-m-d'));
}
}
