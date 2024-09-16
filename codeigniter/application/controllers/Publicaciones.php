<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Publicaciones extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('publicacion_model');
        $this->load->library('session');
        $this->load->helper('url');
    }

    private function _verificar_sesion() {
        if (!$this->session->userdata('login')) {
            redirect('usuarios/index/3', 'refresh');
        }
    }

    public function index() {
        $this->_verificar_sesion();
        $data['publicaciones'] = $this->publicacion_model->listar_publicaciones();
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('publicaciones/lista', $data);
        $this->load->view('inc/footer');
    }

    public function agregar() {
        $this->_verificar_sesion();
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('publicaciones/index', 'refresh');
        }
        $this->load->view('inc/header');
        $this->load->view('publicaciones/agregar');
        $this->load->view('inc/footer');
    }

    public function agregarbd() {
        $this->_verificar_sesion();
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('publicaciones/index', 'refresh');
        }
        $data = array(
            'titulo' => $this->input->post('titulo'),
            'editorial' => $this->input->post('editorial'),
            'diaPublicacion' => $this->input->post('diaPublicacion'),
            'mesPublicacion' => $this->input->post('mesPublicacion'),
            'añoPublicacion' => $this->input->post('añoPublicacion'),
            'tipo' => $this->input->post('tipo'),
            'descripcion' => $this->input->post('descripcion'),
            'idUsuario' => $this->session->userdata('idUsuario'),
            'usuarioSesion' => $this->session->userdata('idUsuario')
        );
        $this->publicacion_model->agregar_publicacion($data);
        redirect('publicaciones/index', 'refresh');
    }

    public function editar($idPublicacion) {
        $this->_verificar_sesion();
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('publicaciones/index', 'refresh');
        }
        $data['publicacion'] = $this->publicacion_model->obtener_publicacion($idPublicacion);
        $this->load->view('inc/header');
        $this->load->view('publicaciones/editar', $data);
        $this->load->view('inc/footer');
    }

    public function editarbd() {
        $this->_verificar_sesion();
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('publicaciones/index', 'refresh');
        }
        $idPublicacion = $this->input->post('idPublicacion');
        $data = array(
            'titulo' => $this->input->post('titulo'),
            'editorial' => $this->input->post('editorial'),
            'diaPublicacion' => $this->input->post('diaPublicacion'),
            'mesPublicacion' => $this->input->post('mesPublicacion'),
            'añoPublicacion' => $this->input->post('añoPublicacion'),
            'tipo' => $this->input->post('tipo'),
            'descripcion' => $this->input->post('descripcion'),
            'usuarioSesion' => $this->session->userdata('idUsuario')
        );
        $this->publicacion_model->actualizar_publicacion($idPublicacion, $data);
        redirect('publicaciones/index', 'refresh');
    }

    public function eliminar($idPublicacion) {
        $this->_verificar_sesion();
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('publicaciones/index', 'refresh');
        }
        $this->publicacion_model->eliminar_publicacion($idPublicacion);
        redirect('publicaciones/index', 'refresh');
    }

    public function buscar() {
        $this->_verificar_sesion();
        $termino = $this->input->get('termino');
        $data['publicaciones'] = $this->publicacion_model->buscar_publicaciones($termino);
        $this->load->view('inc/header');
        $this->load->view('publicaciones/resultados_busqueda', $data);
        $this->load->view('inc/footer');
    }
}