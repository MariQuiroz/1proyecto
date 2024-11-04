<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Reporte_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->_verificar_acceso();
        $this->load->model('tipo_model');
    }

    private function _verificar_acceso() {
        if (!$this->session->userdata('login')) {
            redirect('usuarios/login');
        }
        
        $rol = $this->session->userdata('rol');
        if ($rol != 'administrador' && $rol != 'encargado') {
            $this->session->set_flashdata('error', 'No tienes permisos para acceder a esta sección.');
            redirect('usuarios/panel');
        }
    }

    public function prestamos() {
        $filtros = array(
            'fecha_inicio' => $this->input->get('fecha_inicio'),
            'fecha_fin' => $this->input->get('fecha_fin'),
            'estado' => $this->input->get('estado')
        );

        $data['prestamos'] = $this->Reporte_model->obtener_reporte_prestamos($filtros);
        $data['estadisticas'] = $this->Reporte_model->obtener_estadisticas_prestamos($filtros);
        $data['filtros'] = $filtros;

        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('reportes/prestamos', $data);
        $this->load->view('inc/footer');
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
    
   
    public function exportar_prestamos() {
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
    }
}