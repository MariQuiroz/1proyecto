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
        
        // Configurar reglas de validación personalizadas
        $this->form_validation->set_rules(
            'nombreTipo',
            'Nombre del Tipo',
            'trim|required|min_length[2]|max_length[100]|callback_validar_nombre_tipo',
            array(
                'required'      => 'El nombre del tipo es obligatorio.',
                'min_length'    => 'El nombre debe tener al menos 2 caracteres.',
                'max_length'    => 'El nombre no puede exceder los 100 caracteres.',
                'validar_nombre_tipo' => 'El nombre contiene caracteres no permitidos.'
            )
        );
    
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('tipos/agregar');
            $this->load->view('inc/footer');
        } else {
            try {
                // Obtener y procesar el nombre del tipo
                $nombreTipo = $this->input->post('nombreTipo');
                
                // Eliminar espacios múltiples y al inicio/final
                $nombreTipo = preg_replace('/\s+/', ' ', trim($nombreTipo));
                
                // Verificar existencia
                if ($this->tipo_model->verificar_existencia($nombreTipo)) {
                    $this->session->set_flashdata('error', 'Este tipo ya existe en el sistema.');
                    redirect('tipos/agregar');
                    return;
                }
                
                // Convertir a mayúsculas preservando acentos
                $nombreTipo = mb_strtoupper($nombreTipo, 'UTF-8');
    
                $data = array(
                    'nombreTipo'       => $nombreTipo,
                    'estado'           => 1,
                    'fechaCreacion'    => date('Y-m-d H:i:s'),
                    'idUsuarioCreador' => $this->session->userdata('idUsuario')
                );
    
                if ($this->tipo_model->agregar_tipo($data)) {
                    $this->session->set_flashdata('mensaje', 
                        "Tipo '{$nombreTipo}' agregado correctamente.");
                    redirect('tipos/index');
                } else {
                    throw new Exception('Error al guardar en la base de datos.');
                }
    
            } catch (Exception $e) {
                log_message('error', 'Error al agregar tipo: ' . $e->getMessage());
                $this->session->set_flashdata('error', 'Error al procesar la solicitud.');
                redirect('tipos/agregar');
            }
        }
    }
    
    // Método de validación personalizado
    public function validar_nombre_tipo($nombre) {
        // Permitir letras (con acentos), números y espacios
        if (!preg_match('/^[A-ZÁÉÍÓÚÜÑa-záéíóúüñ0-9\s]+$/', $nombre)) {
            $this->form_validation->set_message('validar_nombre_tipo', 
                'El nombre solo puede contener letras, números y espacios.');
            return FALSE;
        }
        return TRUE;
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