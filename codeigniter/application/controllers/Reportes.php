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

    public function prestamos() {
        try {
            // Obtener y validar filtros
            $estado = $this->input->get('estado');
            
            $filtros = [
                'fecha_inicio' => $this->input->get('fecha_inicio'),
                'fecha_fin' => $this->input->get('fecha_fin'),
                'estado' => $estado ? strtolower($estado) : '',  // Verificar que no sea null
                'id_encargado' => $this->input->get('id_encargado')
            ];

            // Validar fechas
            if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
                if (strtotime($filtros['fecha_fin']) < strtotime($filtros['fecha_inicio'])) {
                    throw new Exception('La fecha fin no puede ser menor a la fecha inicio.');
                }
            }

            // Obtener datos para la vista
            $data = [
                'prestamos' => $this->Reporte_model->obtener_reporte_prestamos($filtros),
                'estadisticas' => $this->Reporte_model->obtener_estadisticas_prestamos($filtros),
                'estadisticas_mensuales' => $this->Reporte_model->obtener_estadisticas_mensuales(),
                'filtros' => $filtros,
                'estados_prestamo' => [
                    'activo' => 'Activos',
                    'devuelto' => 'Devueltos',
                    'vencido' => 'Vencidos'
                ],
                'encargados' => $this->Usuario_model->obtener_encargados_activos()
            ];

            // Cargar la vista
            $this->_cargar_vista_reporte($data);

        } catch (Exception $e) {
            log_message('error', 'Error en reporte de préstamos: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error al generar el reporte: ' . $e->getMessage());
            redirect('usuarios/panel');
        }
    }

    private function _cargar_vista_reporte($data) {
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('reportes/prestamos', $data);
        $this->load->view('inc/footer');
    }

    public function exportar_prestamos() {
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
    
    private function sanitize_for_pdf($text) {
        if (empty($text)) {
            return '';
        }
        // Convertir caracteres especiales a entidades HTML
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        // Convertir entidades HTML a sus equivalentes Unicode
        return html_entity_decode($text, ENT_QUOTES, 'UTF-8');
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
    }*/
    
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
    }
}