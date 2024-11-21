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
        try {
            require_once APPPATH . '../vendor/autoload.php';
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf = new \Dompdf\Dompdf($options);
    
            // Calcular totales
            $total_lectores = 0;
            $total_solicitudes = 0;
            $total_prestamos = 0;
            foreach ($data['estadisticas'] as $est) {
                $total_lectores += $est->total_lectores;
                $total_solicitudes += $est->total_solicitudes;
                $total_prestamos += $est->total_prestamos;
            }
    
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Reporte Detallado por Profesiones - Hemeroteca UMSS</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .header img { max-width: 150px; margin-bottom: 10px; }
                    .titulo { font-size: 20px; font-weight: bold; margin-bottom: 20px; text-align: center; color: #003366; }
                    .filtros { margin-bottom: 20px; font-size: 12px; }
                    .resumen { margin: 20px 0; padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd; }
                    .resumen h3 { color: #003366; margin-bottom: 10px; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #003366; color: white; }
                    tr.total { font-weight: bold; background-color: #f5f5f5; }
                    .detalle h3 { color: #003366; border-bottom: 1px solid #003366; padding-bottom: 5px; }
                    .footer { position: fixed; bottom: 0; font-size: 10px; text-align: center; width: 100%; }
                    .grafico { margin: 20px 0; text-align: center; }
                </style>
            </head>
            <body>
                <div class="header">
                    <img src="' . base_url('assets/img/logo-umss.png') . '" alt="Logo UMSS">
                    <h1>HEMEROTECA UMSS</h1>
                    <h2>REPORTE DETALLADO POR PROFESIONES</h2>
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
                            <th>Total Lectores Activos</th>
                            <th>Total Solicitudes</th>
                            <th>Total Préstamos</th>
                            <th>Promedio Días/Préstamo</th>
                        </tr>
                        <tr>
                            <td style="text-align: center;">' . $total_lectores . '</td>
                            <td style="text-align: center;">' . $total_solicitudes . '</td>
                            <td style="text-align: center;">' . $total_prestamos . '</td>
                            <td style="text-align: center;">' . number_format(array_sum(array_column($data['estadisticas'], 'promedio_dias_prestamo')) / count($data['estadisticas']), 1) . '</td>
                        </tr>
                    </table>
                </div>
    
                <div class="detalle">
                    <h3>Detalle por Profesión</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Profesión</th>
                                <th>Total Lectores</th>
                                <th>Solicitudes</th>
                                <th>Préstamos</th>
                                <th>Prom. Días</th>
                                <th>% del Total</th>
                            </tr>
                        </thead>
                        <tbody>';
    
            $profesiones_nombres = [
                'ESTUDIANTE' => 'Estudiante',
                'DOCENTE' => 'Docente',
                'INVESTIGADOR' => 'Investigador',
                'ADMINISTRATIVO' => 'Administrativo',
                'OTRO' => 'Otro'
            ];
    
            foreach ($profesiones_nombres as $key => $nombre) {
                $est = array_find($data['estadisticas'], function($e) use ($key) {
                    return $e->profesion === $key;
                });
    
                if ($est) {
                    $porcentaje = ($est->total_lectores / $total_lectores) * 100;
                    $html .= '
                        <tr>
                            <td>' . $nombre . '</td>
                            <td style="text-align: right;">' . $est->total_lectores . '</td>
                            <td style="text-align: right;">' . $est->total_solicitudes . '</td>
                            <td style="text-align: right;">' . $est->total_prestamos . '</td>
                            <td style="text-align: right;">' . number_format($est->promedio_dias_prestamo, 1) . '</td>
                            <td style="text-align: right;">' . number_format($porcentaje, 1) . '%</td>
                        </tr>';
                }
            }
    
            $html .= '
                        <tr class="total">
                            <td><strong>TOTALES</strong></td>
                            <td style="text-align: right;"><strong>' . $total_lectores . '</strong></td>
                            <td style="text-align: right;"><strong>' . $total_solicitudes . '</strong></td>
                            <td style="text-align: right;"><strong>' . $total_prestamos . '</strong></td>
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
            $dompdf->stream('reporte_detallado_profesiones.pdf', array('Attachment' => true));
    
        } catch (Exception $e) {
            log_message('error', 'Error al generar PDF de profesiones: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error al generar el PDF');
            redirect('reportes/por_profesion');
        }
    }
    
    // Función auxiliar para encontrar elementos en array
    function array_find($array, $callback) {
        foreach ($array as $item) {
            if ($callback($item)) {
                return $item;
            }
        }
        return null;
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
}
