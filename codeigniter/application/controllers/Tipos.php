<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tipos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('tipo_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');
    }

    private function _verificar_permisos() {
        if (!$this->session->userdata('login')) {
            redirect('usuarios/index/3', 'refresh');
        }
        $rol = $this->session->userdata('rol');
        if ($rol != 'administrador' && $rol != 'encargado') {
            $this->session->set_flashdata('error', 'No tienes permisos para realizar esta acción.');
            redirect('publicaciones/index', 'refresh');
        }
    }

    public function index() {
        $this->_verificar_permisos();
        $data['tipos'] = $this->tipo_model->obtener_tipos();
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('tipos/lista', $data);
        $this->load->view('inc/footer');
    }

    public function agregar() {
        $this->_verificar_permisos();
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('tipos/agregar');
        $this->load->view('inc/footer');
    }

    public function agregarbd() {
        $this->_verificar_permisos();
        $this->form_validation->set_rules('nombreTipo', 'Nombre del Tipo', 'required|is_unique[TIPO.nombreTipo]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('tipos/agregar');
            $this->load->view('inc/footer');
        } else {
            $data = array(
                'nombreTipo' => $this->input->post('nombreTipo'),
                'estado' => 1,
                'fechaCreacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $this->session->userdata('idUsuario')
            );

            if ($this->tipo_model->agregar_tipo($data)) {
                $this->session->set_flashdata('mensaje', 'Tipo agregado correctamente.');
            } else {
                $this->session->set_flashdata('error', 'Error al agregar el tipo.');
            }
            redirect('tipos/index');
        }
    }

    public function editar($idTipo) {
        $this->_verificar_permisos();
        $data['tipo'] = $this->tipo_model->obtener_tipo($idTipo);
        
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('tipos/editar', $data);
        $this->load->view('inc/footer');
    }

    public function editarbd() {
        $this->_verificar_permisos();
        $idTipo = $this->input->post('idTipo');
        $this->form_validation->set_rules('nombreTipo', 'Nombre del Tipo', 'required|is_unique[TIPO.nombreTipo.idTipo.' . $idTipo . ']');

        if ($this->form_validation->run() == FALSE) {
            $data['tipo'] = $this->tipo_model->obtener_tipo($idTipo);
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('tipos/editar', $data);
            $this->load->view('inc/footer');
        } else {
            $data = array(
                'nombreTipo' => $this->input->post('nombreTipo'),
                'fechaActualizacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $this->session->userdata('idUsuario')
            );

            if ($this->tipo_model->actualizar_tipo($idTipo, $data)) {
                $this->session->set_flashdata('mensaje', 'Tipo actualizado correctamente.');
            } else {
                $this->session->set_flashdata('error', 'Error al actualizar el tipo.');
            }
            redirect('tipos/index');
        }
    }

    public function eliminar($idTipo) {
        $this->_verificar_permisos();
        $data = array(
            'estado' => 0,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $this->session->userdata('idUsuario')
        );
        if ($this->tipo_model->eliminar_tipo($idTipo, $data)) {
            $this->session->set_flashdata('mensaje', 'Tipo eliminado correctamente.');
        } else {
            $this->session->set_flashdata('error', 'Error al eliminar el tipo.');
        }
        redirect('tipos/index');
    }
}