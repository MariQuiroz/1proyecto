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
        // Verificar permisos
        $this->_verificar_permisos();
    
        // Definir las reglas de validación
        $this->form_validation->set_rules(
            'nombreTipo',
            'Nombre del Tipo',
            'required|is_unique[TIPO.nombreTipo]|alpha_numeric_spaces|min_length[3]|max_length[50]',
            [
                'required' => 'El campo %s es obligatorio.',
                'is_unique' => 'El %s ya existe. Por favor, elija otro.',
                'alpha_numeric_spaces' => 'El %s solo puede contener letras, números y espacios.',
                'min_length' => 'El %s debe tener al menos 3 caracteres.',
                'max_length' => 'El %s no puede exceder los 50 caracteres.'
            ]
        );
    
        // Validar el formulario
        if ($this->form_validation->run() == FALSE) {
            // Cargar vistas con errores
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('tipos/agregar');
            $this->load->view('inc/footer');
        } else {
            // Convertir el nombre del tipo a mayúsculas
            $nombreTipo = strtoupper($this->input->post('nombreTipo'));
    
            // Preparar datos para guardar
            $data = array(
                'nombreTipo' => $nombreTipo,
                'estado' => 1,
                'fechaCreacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $this->session->userdata('idUsuario')
            );
    
            // Intentar guardar en la base de datos
            if ($this->tipo_model->agregar_tipo($data)) {
                $this->session->set_flashdata('mensaje', 'Tipo agregado correctamente.');
            } else {
                $this->session->set_flashdata('error', 'Error al agregar el tipo.');
            }
    
            // Redirigir al índice de tipos
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
    
        // Obtener el nombre actual para validación de unicidad
        $tipo_actual = $this->tipo_model->obtener_tipo($idTipo);
    
        // Definir las reglas de validación
        $this->form_validation->set_rules(
            'nombreTipo',
            'Nombre del Tipo',
            [
                'required',
                [
                    'nombre_unico',
                    function ($nombreTipo) use ($tipo_actual, $idTipo) {
                        // Verificar si el nuevo nombre ya existe en otro registro
                        $existe = $this->tipo_model->es_nombre_tipo_unico($nombreTipo, $idTipo);
                        return !$existe; // Retorna false si existe
                    }
                ]
            ],
            [
                'required' => 'El campo %s es obligatorio.',
                'nombre_unico' => 'El %s ya existe. Por favor, elija otro.'
            ]
        );
    
        if ($this->form_validation->run() == FALSE) {
            $data['tipo'] = $tipo_actual; // Mantener datos actuales para mostrar en la vista
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('tipos/editar', $data);
            $this->load->view('inc/footer');
        } else {
            // Preparar datos para actualizar
            $data = array(
                'nombreTipo' => strtoupper($this->input->post('nombreTipo')), // Convertir a mayúsculas
                'fechaActualizacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $this->session->userdata('idUsuario')
            );
    
            // Intentar actualizar en la base de datos
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