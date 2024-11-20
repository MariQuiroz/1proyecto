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

  /*  public function prestamos() {
        try {
            // Obtener rol del usuario
            $rol = $this->session->userdata('rol');
            
            // Obtener y validar filtros
            $filtros = $this->_obtener_filtros_prestamos();
            
            // Validar fechas
            if (!$this->_validar_fechas($filtros)) {
                throw new Exception('El rango de fechas no es válido');
            }

            // Obtener datos según el rol
            $datos_reporte = $this->_obtener_datos_reporte_prestamos($filtros, $rol);
            
            if (empty($datos_reporte['prestamos'])) {
                $this->session->set_flashdata('info', 'No se encontraron datos para los filtros seleccionados.');
            }

            // Agregar métricas adicionales según rol
            if ($rol === 'administrador') {
                $datos_reporte['metricas_avanzadas'] = $this->_obtener_metricas_avanzadas($filtros);
            }

            // Cargar la vista con los datos
            $this->_cargar_vista_reporte($datos_reporte);

        } catch (Exception $e) {
            log_message('error', 'Error en reporte de préstamos: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error al generar el reporte: ' . $e->getMessage());
            redirect('usuarios/panel');
        }
    }

    private function _obtener_filtros_prestamos() {
        return [
            'fecha_inicio' => $this->input->get('fecha_inicio'),
            'fecha_fin' => $this->input->get('fecha_fin'),
            'estado' => $this->input->get('estado'),
            'id_encargado' => $this->input->get('id_encargado'),
            'demora_devolucion' => $this->input->get('demora_devolucion'),
            'tipo_publicacion' => $this->input->get('tipo_publicacion')
        ];
    }

    private function _validar_fechas($filtros) {
        if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            if (strtotime($filtros['fecha_fin']) < strtotime($filtros['fecha_inicio'])) {
                return false;
            }
            // Validar que el rango no sea mayor a 1 año
            $diff = strtotime($filtros['fecha_fin']) - strtotime($filtros['fecha_inicio']);
            if ($diff > (365 * 24 * 60 * 60)) {
                return false;
            }
        }
        return true;
    }

    private function _obtener_datos_reporte_prestamos($filtros, $rol) {
        // Datos base del reporte
        $datos = [
            'prestamos' => $this->Reporte_model->obtener_reporte_prestamos($filtros, $rol),
            'estadisticas' => $this->Reporte_model->obtener_estadisticas_prestamos($filtros),
            'estadisticas_mensuales' => $this->Reporte_model->obtener_estadisticas_mensuales(),
            'filtros' => $filtros,
            'estados_prestamo' => $this->_obtener_estados_prestamo(),
            'encargados' => $this->Usuario_model->obtener_encargados_activos(),
            'tipos_publicacion' => $this->Tipo_model->obtener_tipos()
        ];

        // Datos específicos según rol
        if ($rol === 'administrador') {
            $datos['analisis_tendencias'] = $this->Reporte_model->obtener_analisis_tendencias($filtros);
            $datos['metricas_eficiencia'] = $this->Reporte_model->calcular_metricas_eficiencia($filtros);
            $datos['predicciones_demanda'] = $this->Reporte_model->calcular_predicciones_demanda();
        } else {
            $datos['prestamos_por_estado'] = $this->Reporte_model->obtener_prestamos_por_estado($filtros);
            $datos['tiempos_devolucion'] = $this->Reporte_model->calcular_tiempos_devolucion($filtros);
        }

        return $datos;
    }

    private function _obtener_metricas_avanzadas($filtros) {
        return [
            'tiempos_promedio_devolucion' => $this->Reporte_model->calcular_tiempos_promedio_devolucion($filtros),
            'tasa_renovacion' => $this->Reporte_model->calcular_tasa_renovacion($filtros),
            'publicaciones_mas_solicitadas' => $this->Reporte_model->obtener_publicaciones_mas_solicitadas($filtros),
            'usuarios_frecuentes' => $this->Reporte_model->obtener_usuarios_frecuentes($filtros),
            'dias_mayor_demanda' => $this->Reporte_model->analizar_dias_mayor_demanda($filtros)
        ];
    }

    public function exportar_prestamos() {
        try {
            $rol = $this->session->userdata('rol');
            $filtros = $this->_obtener_filtros_prestamos();
            $formato = $this->input->get('formato') ?? 'excel';
            
            // Obtener datos según rol
            $prestamos = $this->Reporte_model->obtener_reporte_prestamos($filtros, $rol);
            $estadisticas = $this->Reporte_model->obtener_estadisticas_prestamos($filtros);
            
            if (empty($prestamos)) {
                throw new Exception('No hay datos para exportar en el período seleccionado');
            }

            switch ($formato) {
                case 'pdf':
                    return $this->_exportar_pdf_prestamos($prestamos, $estadisticas);
                case 'excel':
                    return $this->_exportar_excel_prestamos($prestamos);
                default:
                    throw new Exception('Formato de exportación no válido');
            }

        } catch (Exception $e) {
            log_message('error', 'Error en exportación: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error al exportar: ' . $e->getMessage());
            redirect('reportes/prestamos');
        }
    }

    private function _exportar_pdf_prestamos($prestamos, $estadisticas) {
        // Cargar Dompdf
        require_once APPPATH . '../vendor/autoload.php';
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);

        // Construcción del HTML
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                }
                .header {
                    text-align: center;
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 20px;
                }
                .subheader {
                    text-align: center;
                    font-size: 14px;
                    margin-bottom: 15px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                table.info td, table.info th {
                    border: 1px solid #000;
                    padding: 8px;
                }
                table.info th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                    text-align: center;
                }
                .estado-activo { color: #28a745; }
                .estado-vencido { color: #dc3545; }
                .estado-devuelto { color: #17a2b8; }
                .footer {
                    position: fixed;
                    bottom: 0;
                    width: 100%;
                    font-size: 10px;
                    text-align: center;
                    border-top: 1px solid #000;
                    padding-top: 5px;
                }
            </style>
        </head>
        <body>
            <div class="header">HEMEROTECA UMSS</div>
            <div class="subheader">Reporte de Préstamos - ' . date('d/m/Y H:i:s') . '</div>

            <!-- Resumen Estadístico -->
            <table class="info" style="margin-bottom: 20px;">
                <tr>
                    <th colspan="2" style="background-color: #f8f9fa;">Resumen Estadístico</th>
                </tr>
                <tr>
                    <td style="width: 50%"><strong>Préstamos Activos:</strong></td>
                    <td>' . $estadisticas->activos . '</td>
                </tr>
                <tr>
                    <td><strong>Préstamos Devueltos:</strong></td>
                    <td>' . $estadisticas->devueltos . '</td>
                </tr>
                <tr>
                    <td><strong>Préstamos Vencidos:</strong></td>
                    <td>' . $estadisticas->vencidos . '</td>
                </tr>
            </table>

            <!-- Tabla de Préstamos -->
            <table class="info">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Publicación</th>
                        <th>Estado</th>
                        <th>Encargado</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($prestamos as $prestamo) {
            $estado_class = '';
            switch($prestamo->estado_prestamo) {
                case 'Activo': $estado_class = 'estado-activo'; break;
                case 'Vencido': $estado_class = 'estado-vencido'; break;
                case 'Devuelto': $estado_class = 'estado-devuelto'; break;
            }

            $html .= '
                <tr>
                    <td style="text-align: center;">' . $this->sanitize_for_pdf($prestamo->idPrestamo) . '</td>
                    <td>' . date('d/m/Y', strtotime($prestamo->fechaPrestamo)) . '</td>
                    <td>' . $this->sanitize_for_pdf($prestamo->nombres . ' ' . $prestamo->apellidoPaterno) . '</td>
                    <td>' . $this->sanitize_for_pdf($prestamo->titulo) . '</td>
                    <td class="' . $estado_class . '" style="text-align: center;">' . 
                        $this->sanitize_for_pdf($prestamo->estado_prestamo) . '</td>
                    <td>' . $this->sanitize_for_pdf($prestamo->nombre_encargado . ' ' . 
                        $prestamo->apellido_encargado) . '</td>
                </tr>';
        }

        $html .= '</tbody>
            </table>

            <div class="footer">
                <p>Este documento es generado automáticamente por el sistema.</p>
                <p>Fecha y hora de generación: ' . date('d/m/Y H:i:s') . '</p>
            </div>
        </body>
        </html>';

        // Configurar y generar PDF
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html);
        $dompdf->render();

        // Generar nombre único
        $pdfFileName = 'reporte_prestamos_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Descargar PDF
        $dompdf->stream($pdfFileName, array('Attachment' => true));
    }

    private function _exportar_excel_prestamos($prestamos) {
        // Implementar exportación a Excel
        $filename = 'Reporte_Prestamos_' . date('Y-m-d_H-i-s');
        $headers = [
            'ID',
            'Fecha',
            'Usuario',
            'Publicación',
            'Estado',
            'Encargado',
            'Tiempo Préstamo'
        ];

        $data = [];
        foreach ($prestamos as $prestamo) {
            $data[] = [
                $prestamo->idPrestamo,
                date('d/m/Y H:i', strtotime($prestamo->fechaPrestamo)),
                $prestamo->nombres . ' ' . $prestamo->apellidoPaterno,
                $prestamo->titulo,
                $prestamo->estado_prestamo,
                $prestamo->nombre_encargado . ' ' . $prestamo->apellido_encargado,
                $prestamo->tiempo_prestamo . ' minutos'
            ];
        }

        // Crear archivo Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Agregar encabezados
        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($col, 1, $header);
            $col++;
        }

        // Agregar datos
        $row = 2;
        foreach ($data as $rowData) {
            $col = 1;
            foreach ($rowData as $value) {
                $sheet->setCellValueByColumnAndRow($col, $row, $value);
                $col++;
            }
            $row++;
        }

        // Autoajustar columnas
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Configurar encabezados HTTP para la descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    private function sanitize_for_pdf($text) {
        if (empty($text)) {
            return '';
        }
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }


    private function _cargar_vista_reporte($data) {
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('reportes/prestamos', $data);
        $this->load->view('inc/footer');
    }

    private function exportar_pdf_prestamos() {
        try {
            $this->_verificar_acceso();
            $filtros = $this->_obtener_filtros();
            $formato = $this->input->get('formato') ?? 'excel';
            
            // Obtener datos necesarios
            $prestamos = $this->Reporte_model->obtener_reporte_prestamos($filtros);
            $estadisticas = $this->Reporte_model->obtener_estadisticas_prestamos($filtros);
            
            if (empty($prestamos)) {
                throw new Exception('No hay datos para exportar');
            }
    
            if ($formato === 'pdf') {
                // Cargar Dompdf
                require_once APPPATH . '../vendor/autoload.php';
                $options = new \Dompdf\Options();
                $options->set('isHtml5ParserEnabled', true);
                $options->set('isPhpEnabled', true);
                $options->set('isRemoteEnabled', true);
                
                $dompdf = new \Dompdf\Dompdf($options);
    
                // Construcción del HTML
                $html = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            font-size: 12px;
                        }
                        .header {
                            text-align: center;
                            font-size: 18px;
                            font-weight: bold;
                            margin-bottom: 20px;
                        }
                        .subheader {
                            text-align: center;
                            font-size: 14px;
                            margin-bottom: 15px;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 15px;
                        }
                        table.info td, table.info th {
                            border: 1px solid #000;
                            padding: 8px;
                        }
                        table.info th {
                            background-color: #f2f2f2;
                            font-weight: bold;
                            text-align: center;
                        }
                        .estado-activo { color: green; }
                        .estado-vencido { color: red; }
                        .estado-devuelto { color: blue; }
                        .footer {
                            position: absolute;
                            bottom: 0;
                            right: 0;
                            font-size: 8px;
                            text-align: right;
                        }
                    </style>
                </head>
                <body>
                    <div class="header">Reporte de Préstamos - Hemeroteca UMSS</div>
                    <div class="subheader">Fecha de generación: ' . date('d/m/Y H:i:s') . '</div>
    
                    <table class="info">
                        <tr>
                            <td width="30%"><strong>Préstamos Activos:</strong></td>
                            <td>' . $this->sanitize_for_pdf($estadisticas->activos) . '</td>
                        </tr>
                        <tr>
                            <td><strong>Préstamos Devueltos:</strong></td>
                            <td>' . $this->sanitize_for_pdf($estadisticas->devueltos) . '</td>
                        </tr>
                        <tr>
                            <td><strong>Préstamos Vencidos:</strong></td>
                            <td>' . $this->sanitize_for_pdf($estadisticas->vencidos) . '</td>
                        </tr>
                    </table>
    
                    <h4>Detalle de Préstamos:</h4>
                    <table class="info">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Publicación</th>
                                <th>Estado</th>
                                <th>Encargado</th>
                                <th>Devolución</th>
                            </tr>
                        </thead>
                        <tbody>';
    
                foreach ($prestamos as $prestamo) {
                    $estado_class = '';
                    switch($prestamo->estado_prestamo) {
                        case 'Activo': $estado_class = 'estado-activo'; break;
                        case 'Vencido': $estado_class = 'estado-vencido'; break;
                        case 'Devuelto': $estado_class = 'estado-devuelto'; break;
                    }
    
                    $html .= '
                        <tr>
                            <td style="text-align: center;">' . $this->sanitize_for_pdf($prestamo->idPrestamo) . '</td>
                            <td>' . date('d/m/Y', strtotime($prestamo->fechaPrestamo)) . '</td>
                            <td>' . $this->sanitize_for_pdf($prestamo->nombres . ' ' . $prestamo->apellidoPaterno) . '</td>
                            <td>' . $this->sanitize_for_pdf($prestamo->titulo) . '</td>
                            <td class="' . $estado_class . '">' . $this->sanitize_for_pdf($prestamo->estado_prestamo) . '</td>
                            <td>' . $this->sanitize_for_pdf($prestamo->nombre_encargado . ' ' . $prestamo->apellido_encargado) . '</td>
                            <td>' . ($prestamo->horaDevolucion ? date('d/m/Y H:i', strtotime($prestamo->horaDevolucion)) : 'Pendiente') . '</td>
                        </tr>';
                }
    
                $html .= '</tbody></table>
                    
                    <div class="footer">
                        <p>Documento generado el: ' . date('d/m/Y H:i:s') . '</p>
                    </div>
                </body>
                </html>';
    
                // Configurar y generar PDF
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->loadHtml($html);
                $dompdf->render();
    
                // Generar nombre único
                $pdfFileName = 'reporte_prestamos_' . date('Y-m-d_H-i-s') . '.pdf';
                
                // Descargar PDF
                $dompdf->stream($pdfFileName, array('Attachment' => true));
                
            } else {
                // Exportar a Excel
                $this->load->library('excel');
                $filename = 'Reporte_Prestamos_' . date('Y-m-d_H-i-s');
                $this->excel->export_to_excel($prestamos, $filename);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error en exportación de préstamos: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error al exportar el reporte: ' . $e->getMessage());
            redirect('reportes/prestamos');
        }
    }

    private function _obtener_filtros() {
        return [
            'fecha_inicio' => $this->input->get('fecha_inicio'),
            'fecha_fin' => $this->input->get('fecha_fin'),
            'estado' => $this->input->get('estado'),
            'id_encargado' => $this->input->get('id_encargado'),
            'id_publicacion' => $this->input->get('id_publicacion')
        ];
    }

    private function _validar_filtros($filtros) {
        $this->form_validation->set_rules('fecha_inicio', 'Fecha Inicio', 'callback__validar_fecha');
        $this->form_validation->set_rules('fecha_fin', 'Fecha Fin', 'callback__validar_fecha');
        
        if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            if (strtotime($filtros['fecha_fin']) < strtotime($filtros['fecha_inicio'])) {
                $this->form_validation->set_message('_validar_fecha', 'La fecha fin no puede ser menor a la fecha inicio');
                return FALSE;
            }
        }
        return TRUE;
    }

    private function _obtener_estados_prestamo() {
        return [
            'activo' => 'Activos',
            'devuelto' => 'Devueltos',
            'vencido' => 'Vencidos'
        ];
    }
    
    public function publicaciones() {
        // Cargar modelos necesarios
        $this->load->model('tipo_model');
        
        // Obtener filtros
        $filtros = array(
            'fecha_inicio' => $this->input->get('fecha_inicio'),
            'fecha_fin' => $this->input->get('fecha_fin'),
            'tipo' => $this->input->get('tipo')
        );
    
        // Preparar datos para la vista
        $data['publicaciones'] = $this->Reporte_model->obtener_reporte_publicaciones($filtros);
        $data['estadisticas'] = $this->Reporte_model->obtener_estadisticas_publicaciones($filtros);
        $data['tendencias'] = $this->Reporte_model->obtener_tendencias_mensuales($filtros);
        $data['tipos'] = $this->tipo_model->obtener_tipos();
        $data['filtros'] = $filtros;
    
        // Cargar la vista
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('reportes/publicaciones', $data);
        $this->load->view('inc/footer');
    }
    
    public function usuarios() {
        $filtros = array(
            'fecha_inicio' => $this->input->get('fecha_inicio'),
            'fecha_fin' => $this->input->get('fecha_fin'),
            'min_prestamos' => $this->input->get('min_prestamos')
        );
    
        $data['usuarios'] = $this->Reporte_model->obtener_reporte_usuarios($filtros);
        $data['estadisticas'] = $this->Reporte_model->obtener_estadisticas_usuarios($filtros);
        $data['actividad_mensual'] = $this->Reporte_model->obtener_actividad_mensual_usuarios($filtros);
        $data['filtros'] = $filtros;
    
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('reportes/usuarios', $data);
        $this->load->view('inc/footer');
    }


    
   
    /*public function exportar_prestamos() {
        $filtros = array(
            'fecha_inicio' => $this->input->get('fecha_inicio'),
            'fecha_fin' => $this->input->get('fecha_fin'),
            'estado' => $this->input->get('estado')
        );
    
        $prestamos = $this->Reporte_model->obtener_reporte_prestamos($filtros);
        
        // Preparar datos para exportación
        $export_data = array();
        foreach ($prestamos as $prestamo) {
            $export_data[] = array(
                'ID Préstamo' => $prestamo->idPrestamo,
                'Fecha' => date('d/m/Y', strtotime($prestamo->fechaPrestamo)),
                'Hora' => $prestamo->horaInicio,
                'Usuario' => $prestamo->nombres . ' ' . $prestamo->apellidoPaterno,
                'Publicación' => $prestamo->titulo,
                'Estado' => $prestamo->estado_prestamo,
                'Encargado' => $prestamo->nombre_encargado . ' ' . $prestamo->apellido_encargado
            );
        }
    
        $this->load->library('excel');
        $this->excel->export_to_excel($export_data, 'Reporte_Prestamos');
    }
    
    public function exportar_publicaciones() {
        $filtros = array(
            'fecha_inicio' => $this->input->get('fecha_inicio'),
            'fecha_fin' => $this->input->get('fecha_fin'),
            'tipo' => $this->input->get('tipo')
        );
    
        $publicaciones = $this->Reporte_model->obtener_reporte_publicaciones($filtros);
        
        $export_data = array();
        foreach ($publicaciones as $pub) {
            $export_data[] = array(
                'Título' => $pub->titulo,
                'Tipo' => $pub->nombreTipo,
                'Editorial' => $pub->nombreEditorial,
                'Total Solicitudes' => $pub->total_solicitudes,
                'Préstamos Activos' => $pub->prestamos_activos,
                'Préstamos Completados' => $pub->prestamos_completados,
                'Fecha Publicación' => $pub->fecha_publicacion
            );
        }
    
        $this->load->library('excel');
        $this->excel->export_to_excel($export_data, 'Reporte_Publicaciones');
    }
    
    public function exportar_usuarios() {
        $filtros = array(
            'fecha_inicio' => $this->input->get('fecha_inicio'),
            'fecha_fin' => $this->input->get('fecha_fin'),
            'min_prestamos' => $this->input->get('min_prestamos')
        );
    
        $usuarios = $this->Reporte_model->obtener_reporte_usuarios($filtros);
        
        $export_data = array();
        foreach ($usuarios as $usuario) {
            $export_data[] = array(
                'Usuario' => $usuario->nombres . ' ' . $usuario->apellidoPaterno,
                'Profesión' => $usuario->profesion,
                'Total Solicitudes' => $usuario->total_solicitudes,
                'Préstamos Activos' => $usuario->prestamos_activos,
                'Préstamos Completados' => $usuario->prestamos_completados,
                'Última Actividad' => date('d/m/Y', strtotime($usuario->ultima_actividad))
            );
        }
    
        $this->load->library('excel');
        $this->excel->export_to_excel($export_data, 'Reporte_Usuarios');
 
    }*/

    
    public function index() {
        $data['metricas'] = $this->Reporte_model->obtener_metricas_generales();
        
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('reportes/dashboard', $data);
        $this->load->view('inc/footer');
    }

    public function por_profesion() {
        $filtros = array(
            'fecha_inicio' => $this->input->get('fecha_inicio'),
            'fecha_fin' => $this->input->get('fecha_fin')
        );
        
        $data['estadisticas'] = $this->Reporte_model->obtener_estadisticas_profesion($filtros);
        $data['filtros'] = $filtros;
        
        if ($this->input->get('export') === 'pdf') {
            $this->_generar_pdf_profesiones($data);
        } else if ($this->input->get('export') === 'excel') {
            $this->_generar_excel_profesiones($data);
        } else {
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('reportes/por_profesion', $data);
            $this->load->view('inc/footer');
        }
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
    public function tipos_publicaciones() {
        $filtros = array(
            'fecha_inicio' => $this->input->get('fecha_inicio'),
            'fecha_fin' => $this->input->get('fecha_fin')
        );
        
        $data['estadisticas'] = $this->Reporte_model->obtener_estadisticas_tipos($filtros);
        $data['filtros'] = $filtros;
        if ($this->input->get('export') === 'pdf') {
            $this->_generar_pdf_devoluciones($data);
        } else if ($this->input->get('export') === 'excel') {
            $this->_generar_excel_devoluciones($data);
        } else {
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('reportes/tipos_publicaciones', $data);
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
        require_once APPPATH . 'third_party/fpdf/fpdf.php';
        
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Título
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(190, 10, 'Reporte por Profesiones', 0, 1, 'C');
        
        // Filtros aplicados
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(190, 10, 'Periodo: ' . $data['filtros']['fecha_inicio'] . ' - ' . $data['filtros']['fecha_fin'], 0, 1, 'L');
        
        // Cabeceras
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, 7, 'Profesión', 1);
        $pdf->Cell(35, 7, 'Total Lectores', 1);
        $pdf->Cell(35, 7, 'Solicitudes', 1);
        $pdf->Cell(35, 7, 'Préstamos', 1);
        $pdf->Cell(35, 7, 'Prom. Días', 1);
        $pdf->Ln();
        
        // Datos
        $pdf->SetFont('Arial', '', 10);
        foreach ($data['estadisticas'] as $est) {
            $pdf->Cell(50, 6, utf8_decode($est->profesion), 1);
            $pdf->Cell(35, 6, $est->total_lectores, 1, 0, 'R');
            $pdf->Cell(35, 6, $est->total_solicitudes, 1, 0, 'R');
            $pdf->Cell(35, 6, $est->total_prestamos, 1, 0, 'R');
            $pdf->Cell(35, 6, $est->promedio_dias_prestamo, 1, 0, 'R');
            $pdf->Ln();
        }
        
        $pdf->Output('reporte_profesiones.pdf', 'D');
    }

    private function _generar_excel_profesiones($data) {
        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        
        $excel = new PHPExcel();
        
        // Configuración básica
        $excel->getProperties()
              ->setCreator("Hemeroteca UMSS")
              ->setTitle("Reporte por Profesiones")
              ->setDescription("Estadísticas de uso por profesión de lectores");
        
        // Encabezados
        $excel->setActiveSheetIndex(0)
              ->setCellValue('A1', 'Profesión')
              ->setCellValue('B1', 'Total Lectores')
              ->setCellValue('C1', 'Total Solicitudes')
              ->setCellValue('D1', 'Total Préstamos')
              ->setCellValue('E1', 'Promedio Días Préstamo');
        
        // Datos
        $row = 2;
        foreach ($data['estadisticas'] as $est) {
            $excel->setActiveSheetIndex(0)
                  ->setCellValue('A'.$row, $est->profesion)
                  ->setCellValue('B'.$row, $est->total_lectores)
                  ->setCellValue('C'.$row, $est->total_solicitudes)
                  ->setCellValue('D'.$row, $est->total_prestamos)
                  ->setCellValue('E'.$row, $est->promedio_dias_prestamo);
            $row++;
        }
        
        // Autoajustar columnas
        foreach(range('A','E') as $col) {
            $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Descargar archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_profesiones.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');
    }

    private function _generar_pdf_solicitudes($data) {
        // Similar a _generar_pdf_profesiones pero adaptado para solicitudes
        require_once APPPATH . 'third_party/fpdf/fpdf.php';
        
        $pdf = new FPDF();
        $pdf->AddPage();
        
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(190, 10, 'Reporte de Estado de Solicitudes', 0, 1, 'C');
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(190, 10, 'Periodo: ' . $data['filtros']['fecha_inicio'] . ' - ' . $data['filtros']['fecha_fin'], 0, 1, 'L');
        
        // Cabeceras
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(25, 7, 'Mes/Año', 1);
        $pdf->Cell(30, 7, 'Total', 1);
        $pdf->Cell(30, 7, 'Aprobadas', 1);
        $pdf->Cell(30, 7, 'Rechazadas', 1);
        $pdf->Cell(30, 7, 'Pendientes', 1);
        $pdf->Cell(45, 7, '% Aprobación', 1);
        $pdf->Ln();
        
        foreach ($data['estadisticas'] as $est) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(25, 6, $est->mes . '/' . $est->anio, 1);
            $pdf->Cell(30, 6, $est->total_solicitudes, 1, 0, 'R');
            $pdf->Cell(30, 6, $est->aprobadas, 1, 0, 'R');
            $pdf->Cell(30, 6, $est->rechazadas, 1, 0, 'R');
            $pdf->Cell(30, 6, $est->pendientes, 1, 0, 'R');
            $pdf->Cell(45, 6, $est->porcentaje_aprobacion . '%', 1, 0, 'R');
            $pdf->Ln();
        }
        
        $pdf->Output('reporte_solicitudes.pdf', 'D');
    }
}
