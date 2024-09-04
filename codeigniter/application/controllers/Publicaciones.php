<?php
defined('BASEPATH') OR exit('No se permite acceso directo al script');

class Publicaciones extends CI_Controller {
    
   

    private function _verificar_sesion() {
        if (!$this->session->userdata('login')) {
            redirect('usuarios/login');
        }
    }

    public function index() {
        $data['publicaciones'] = $this->publicacion_model->listar_publicaciones();
        $this->load->view('inc/header');
        $this->load->view('publicaciones/lista', $data);
        $this->load->view('inc/footer');
    }

    public function ver($id) {
        $data['publicacion'] = $this->publicacion_model->obtener_publicacion($id);
        $this->load->view('inc/header');
        $this->load->view('publicaciones/ver', $data);
        $this->load->view('inc/footer');
    }

    public function agregar() {
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('publicaciones');
        }

        $this->load->view('inc/header');
        $this->load->view('publicaciones/formulario');
        $this->load->view('inc/footer');
    }

    public function agregarbd() {
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('publicaciones');
        }

        $data = array(
            'idUsuario' => $this->session->userdata('idUsuario'),
            'titulo' => $this->input->post('titulo'),
            'editorial' => $this->input->post('editorial'),
            'diaPublicacion' => $this->input->post('diaPublicacion'),
            'mesPublicacion' => $this->input->post('mesPublicacion'),
            'añoPublicacion' => $this->input->post('añoPublicacion'),
            'tipo' => $this->input->post('tipo'),
            'descripcion' => $this->input->post('descripcion'),
            'usuarioSesion' => $this->session->userdata('idUsuario')
        );

        $this->publicacion_model->agregar_publicacion($data);
        redirect('publicaciones');
    }

    public function modificar($id) {
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('publicaciones');
        }

        $data['publicacion'] = $this->publicacion_model->obtener_publicacion($id);
        $this->load->view('inc/header');
        $this->load->view('publicaciones/formulario_modificar', $data);
        $this->load->view('inc/footer');
    }

    public function modificarbd() {
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('publicaciones');
        }

        $id = $this->input->post('idPublicacion');
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

        $this->publicacion_model->modificar_publicacion($id, $data);
        redirect('publicaciones');
    }

    public function eliminar($id) {
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('publicaciones');
        }

        $this->publicacion_model->eliminar_publicacion($id);
        redirect('publicaciones');
    }

    public function deshabilitarbd() {
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('publicaciones');
        }

        $id = $this->input->post('idPublicacion');
        $this->publicacion_model->deshabilitar_publicacion($id);
        redirect('publicaciones');
    }

    public function habilitarbd() {
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('publicaciones');
        }

        $id = $this->input->post('idPublicacion');
        $this->publicacion_model->habilitar_publicacion($id);
        redirect('publicaciones');
    }
}
