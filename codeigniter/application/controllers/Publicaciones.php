<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Publicaciones extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('publicacion_model');
        $this->load->model('tipo_model');
        $this->load->model('editorial_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');
    }

    private function _verificar_sesion() {
        if (!$this->session->userdata('login')) {
            redirect('usuarios/index/3', 'refresh');
        }
    }

    private function _verificar_permisos_admin_encargado() {
        $this->_verificar_sesion();
        $rol = $this->session->userdata('rol');
        if ($rol != 'administrador' && $rol != 'encargado') {
            $this->session->set_flashdata('error', 'No tienes permisos para realizar esta acción.');
            redirect('publicaciones/index', 'refresh');
        }
    }

    private function _es_lector() {
        return $this->session->userdata('rol') == 'lector';
    }

    public function index() {
        $this->_verificar_sesion();
        $data['publicaciones'] = $this->publicacion_model->listar_publicaciones();
        $data['es_lector'] = $this->_es_lector();
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('publicaciones/lista', $data);
        $this->load->view('inc/footer');
    }

    public function agregar() {
        $this->_verificar_permisos_admin_encargado();
        $data['tipos'] = $this->tipo_model->obtener_tipos();
        $data['editoriales'] = $this->editorial_model->obtener_editoriales();
        $data['es_lector'] = false; // Agregamos esta línea
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('publicaciones/agregar', $data);
        $this->load->view('inc/footer');
    }
    public function agregarbd() {
        $this->_verificar_permisos_admin_encargado();
        $this->form_validation->set_rules('titulo', 'Título', 'required');
        $this->form_validation->set_rules('idEditorial', 'Editorial', 'required|numeric');
        $this->form_validation->set_rules('idTipo', 'Tipo', 'required|numeric');
        $this->form_validation->set_rules('fechaPublicacion', 'Fecha de Publicación', 'required');
        $this->form_validation->set_rules('numeroPaginas', 'Número de Páginas', 'numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->agregar();
        } else {
            $data = array(
                'idUsuario' => $this->session->userdata('idUsuario'),
                'idTipo' => $this->input->post('idTipo'),
                'idEditorial' => $this->input->post('idEditorial'),
                'titulo' => $this->input->post('titulo'),
                'fechaPublicacion' => $this->input->post('fechaPublicacion'),
                'numeroPaginas' => $this->input->post('numeroPaginas'),
                'descripcion' => $this->input->post('descripcion'),
                'ubicacionFisica' => $this->input->post('ubicacionFisica'),
                'estado' => ESTADO_PUBLICACION_DISPONIBLE,
                'fechaCreacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $this->session->userdata('idUsuario')
            );
            
            if ($this->publicacion_model->agregar_publicacion($data)) {
                $this->session->set_flashdata('mensaje', 'Publicación agregada correctamente.');
            } else {
                $this->session->set_flashdata('error', 'Error al agregar la publicación.');
            }
            redirect('publicaciones/index', 'refresh');
        }
    }

 

    public function editar($idPublicacion) {
        $this->_verificar_permisos_admin_encargado();
        $data['publicacion'] = $this->publicacion_model->obtener_publicacion($idPublicacion);
        $data['tipos'] = $this->tipo_model->obtener_tipos();
        $data['editoriales'] = $this->editorial_model->obtener_editoriales();
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('publicaciones/editar', $data);
        $this->load->view('inc/footer');
    }

    public function editarbd() {
        $this->_verificar_permisos_admin_encargado();
        $idPublicacion = $this->input->post('idPublicacion');
        $this->form_validation->set_rules('titulo', 'Título', 'required');
        $this->form_validation->set_rules('idEditorial', 'Editorial', 'required|numeric');
        $this->form_validation->set_rules('idTipo', 'Tipo', 'required|numeric');
        $this->form_validation->set_rules('fechaPublicacion', 'Fecha de Publicación', 'required');
        $this->form_validation->set_rules('numeroPaginas', 'Número de Páginas', 'numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->editar($idPublicacion);
        } else {
            $data = array(
                'idTipo' => $this->input->post('idTipo'),
                'idEditorial' => $this->input->post('idEditorial'),
                'titulo' => $this->input->post('titulo'),
                'fechaPublicacion' => $this->input->post('fechaPublicacion'),
                'numeroPaginas' => $this->input->post('numeroPaginas'),
                'descripcion' => $this->input->post('descripcion'),
                'ubicacionFisica' => $this->input->post('ubicacionFisica'),
                'fechaActualizacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $this->session->userdata('idUsuario')
            );
            
            if ($this->publicacion_model->actualizar_publicacion($idPublicacion, $data)) {
                $this->session->set_flashdata('mensaje', 'Publicación actualizada correctamente.');
            } else {
                $this->session->set_flashdata('error', 'Error al actualizar la publicación.');
            }
            redirect('publicaciones/index', 'refresh');
        }
    }

    public function eliminar($idPublicacion) {
        $this->_verificar_permisos_admin_encargado();
        $data = array(
            'estado' => 0,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $this->session->userdata('idUsuario')
        );
        if ($this->publicacion_model->eliminar_publicacion($idPublicacion, $data)) {
            $this->session->set_flashdata('mensaje', 'Publicación eliminada correctamente.');
        } else {
            $this->session->set_flashdata('error', 'No se pudo eliminar la publicación.');
        }
        redirect('publicaciones/index', 'refresh');
    }

    public function buscar() {
        $this->_verificar_sesion();
        $termino = $this->input->get('termino');
        $data['publicaciones'] = $this->publicacion_model->buscar_publicaciones($termino);
        $data['es_lector'] = $this->_es_lector();
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('publicaciones/resultados_busqueda', $data);
        $this->load->view('inc/footer');
    }

    public function cambiar_estado($idPublicacion, $nuevoEstado) {
        $this->_verificar_permisos_admin_encargado();
        $data = array(
            'estado' => $nuevoEstado,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $this->session->userdata('idUsuario')
        );
        if ($this->publicacion_model->cambiar_estado_publicacion($idPublicacion, $data)) {
            $this->session->set_flashdata('mensaje', 'Estado de la publicación actualizado correctamente.');
        } else {
            $this->session->set_flashdata('error', 'Error al actualizar el estado de la publicación.');
        }
        redirect('publicaciones/index', 'refresh');
    }

    public function ver($idPublicacion) {
        $this->_verificar_sesion();
        $data['publicacion'] = $this->publicacion_model->obtener_publicacion_detallada($idPublicacion);
        $data['es_lector'] = $this->_es_lector();
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('publicaciones/ver', $data);
        $this->load->view('inc/footer');
    }
}