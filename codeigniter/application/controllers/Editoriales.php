<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Editoriales extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('editorial_model');
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
        $data['editoriales'] = $this->editorial_model->obtener_editoriales();
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('editoriales/lista', $data);
        $this->load->view('inc/footer');
    }

    public function agregar() {
        $this->_verificar_permisos();
        $this->form_validation->set_rules('nombreEditorial', 'Nombre de la Editorial', 'required|is_unique[EDITORIAL.nombreEditorial]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('editoriales/agregar');
            $this->load->view('inc/footer');
        } else {
            $data = array(
                'nombreEditorial' => $this->input->post('nombreEditorial'),
                'estado' => 1,
                'fechaCreacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $this->session->userdata('idUsuario')
            );

            if ($this->editorial_model->agregar_editorial($data)) {
                $this->session->set_flashdata('mensaje', 'Editorial agregada correctamente.');
            } else {
                $this->session->set_flashdata('error', 'Error al agregar la editorial.');
            }
            redirect('editoriales/index');
        }
    }

    public function editar($idEditorial) {
        $this->_verificar_permisos();
        $data['editorial'] = $this->editorial_model->obtener_editorial($idEditorial);

        $this->form_validation->set_rules('nombreEditorial', 'Nombre de la Editorial', 'required|is_unique[EDITORIAL.nombreEditorial.idEditorial.' . $idEditorial . ']');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('editoriales/editar', $data);
            $this->load->view('inc/footer');
        } else {
            $data = array(
                'nombreEditorial' => $this->input->post('nombreEditorial'),
                'fechaActualizacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $this->session->userdata('idUsuario')
            );

            if ($this->editorial_model->actualizar_editorial($idEditorial, $data)) {
                $this->session->set_flashdata('mensaje', 'Editorial actualizada correctamente.');
            } else {
                $this->session->set_flashdata('error', 'Error al actualizar la editorial.');
            }
            redirect('editoriales/index');
        }
    }

    public function eliminar($idEditorial) {
        $this->_verificar_permisos();
        $data = array(
            'estado' => 0,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $this->session->userdata('idUsuario')
        );
        if ($this->editorial_model->eliminar_editorial($idEditorial, $data)) {
            $this->session->set_flashdata('mensaje', 'Editorial eliminada correctamente.');
        } else {
            $this->session->set_flashdata('error', 'Error al eliminar la editorial.');
        }
        redirect('editoriales/index');
    }
}