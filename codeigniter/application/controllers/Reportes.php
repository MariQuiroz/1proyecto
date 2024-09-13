<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('reporte_model');
        $this->load->library('session');
        $this->load->helper('url');
    }

    private function _verificar_sesion() {
        if (!$this->session->userdata('login')) {
            redirect('usuarios/index/3', 'refresh');
        }
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('usuarios/panel', 'refresh');
        }
    }

    public function index() {
        $this->_verificar_sesion();
        $this->load->view('inc/header');
        $this->load->view('reportes/menu');
        $this->load->view('inc/footer');
    }

    public function prestamos_por_periodo() {
        $this->_verificar_sesion();
        $fecha_inicio = $this->input->post('fecha_inicio');
        $fecha_fin = $this->input->post('fecha_fin');
        $data['prestamos'] = $this->reporte_model->prestamos_por_periodo($fecha_inicio, $fecha_fin);
        $this->load->view('inc/header');
        $this->load->view('reportes/prestamos_por_periodo', $data);
        $this->load->view('inc/footer');
    }

    public function publicaciones_mas_solicitadas() {
        $this->_verificar_sesion();
        $data['publicaciones'] = $this->reporte_model->publicaciones_mas_solicitadas();
        $this->load->view('inc/header');
        $this->load->view('reportes/publicaciones_mas_solicitadas', $data);
        $this->load->view('inc/footer');
    }

    public function usuarios_mas_activos() {
        $this->_verificar_sesion();
        $data['usuarios'] = $this->reporte_model->usuarios_mas_activos();
        $this->load->view('inc/header');
        $this->load->view('reportes/usuarios_mas_activos', $data);
        $this->load->view('inc/footer');
    }

    public function generar_reporte() {
        $this->_verificar_sesion();
        $tipo = $this->input->post('tipo');
        $contenido = '';
        
        switch ($tipo) {
            case 'prestamos':
                $fecha_inicio = $this->input->post('fecha_inicio');
                $fecha_fin = $this->input->post('fecha_fin');
                $prestamos = $this->reporte_model->prestamos_por_periodo($fecha_inicio, $fecha_fin);
                $contenido = json_encode($prestamos);
                break;
            case 'publicaciones_populares':
                $publicaciones = $this->reporte_model->publicaciones_mas_solicitadas();
                $contenido = json_encode($publicaciones);
                break;
            case 'usuarios_activos':
                $usuarios = $this->reporte_model->usuarios_mas_activos();
                $contenido = json_encode($usuarios);
                break;
        }

        $data = array(
            'idUsuario' => $this->session->userdata('idUsuario'),
            'tipo' => $tipo,
            'contenido' => $contenido
        );

        $id_reporte = $this->reporte_model->guardar_reporte($data);
        
        if ($id_reporte) {
            $this->session->set_flashdata('success', 'Reporte generado con éxito.');
        } else {
            $this->session->set_flashdata('error', 'No se pudo generar el reporte.');
        }

        redirect('reportes/index', 'refresh');
    }

    public function ver_reporte($id) {
        $this->_verificar_sesion();
        $data['reporte'] = $this->reporte_model->obtener_reporte($id);
        
        if (!$data['reporte']) {
            $this->session->set_flashdata('error', 'Reporte no encontrado.');
            redirect('reportes/index', 'refresh');
        }

        $this->load->view('inc/header');
        $this->load->view('reportes/ver_reporte', $data);
        $this->load->view('inc/footer');
    }

    public function listar_reportes() {
        $this->_verificar_sesion();
        $data['reportes'] = $this->reporte_model->listar_reportes();
        $this->load->view('inc/header');
        $this->load->view('reportes/lista_reportes', $data);
        $this->load->view('inc/footer');
    }
}