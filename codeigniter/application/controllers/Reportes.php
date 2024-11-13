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
            $filtros = $this->_obtener_filtros();
            $this->_validar_filtros($filtros);
    
            $data = [
                'prestamos' => $this->Reporte_model->obtener_reporte_prestamos($filtros),
                'estadisticas' => $this->Reporte_model->obtener_estadisticas_prestamos($filtros),
                'estadisticas_mensuales' => $this->Reporte_model->obtener_estadisticas_mensuales(),
                'filtros' => $filtros,
                'estados_prestamo' => $this->_obtener_estados_prestamo(),
                'encargados' => $this->Usuario_model->obtener_encargados_activos()
            ];
    
            $this->_cargar_vista_reporte($data);
    
        } catch (Exception $e) {
            log_message('error', 'Error en reporte de préstamos: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error al generar el reporte: ' . $e->getMessage());
            redirect('usuarios/panel');
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

    private function _cargar_vista_reporte($data) {
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('reportes/prestamos', $data);
        $this->load->view('inc/footer');
    }

    public function exportar_prestamos() {
        try {
            $filtros = $this->_obtener_filtros();
            $prestamos = $this->Reporte_model->obtener_reporte_prestamos($filtros);
            
            $this->load->library('excel');
            $excel_data = $this->_generar_excel_prestamos($prestamos);
            
            $filename = 'reporte_prestamos_' . date('Y-m-d_H-i-s') . '.xlsx';
            force_download($filename, $excel_data);

        } catch (Exception $e) {
            log_message('error', 'Error al exportar reporte: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Error al exportar el reporte.');
            redirect('reportes/prestamos');
        }
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