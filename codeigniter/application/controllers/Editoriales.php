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
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('editoriales/agregar');
        $this->load->view('inc/footer');
    }

    public function agregarbd() {
        $this->_verificar_permisos();
        
        // Configurar reglas de validación personalizadas
        $this->form_validation->set_rules(
            'nombreEditorial',
            'Nombre de la Editorial',
            'trim|required|min_length[2]|max_length[100]|callback_validar_nombre_editorial',
            array(
                'required'      => 'El nombre de la editorial es obligatorio.',
                'min_length'    => 'El nombre debe tener al menos 2 caracteres.',
                'max_length'    => 'El nombre no puede exceder los 100 caracteres.',
                'validar_nombre_editorial' => 'El nombre contiene caracteres no permitidos.'
            )
        );
    
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('editoriales/agregar');
            $this->load->view('inc/footer');
        } else {
            try {
                // Obtener y procesar el nombre de la editorial
                $nombreEditorial = $this->input->post('nombreEditorial');
                
                // Eliminar espacios múltiples y al inicio/final
                $nombreEditorial = preg_replace('/\s+/', ' ', trim($nombreEditorial));
                
                // Verificar existencia (considerando variaciones de espacios)
                if ($this->editorial_model->verificar_existencia($nombreEditorial)) {
                    $this->session->set_flashdata('error', 'Esta editorial ya existe en el sistema.');
                    redirect('editoriales/agregar');
                    return;
                }
                
                // Convertir a mayúsculas preservando acentos
                $nombreEditorial = mb_strtoupper($nombreEditorial, 'UTF-8');
    
                $data = array(
                    'nombreEditorial'   => $nombreEditorial,
                    'estado'            => 1,
                    'fechaCreacion'     => date('Y-m-d H:i:s'),
                    'idUsuarioCreador'  => $this->session->userdata('idUsuario')
                );
    
                if ($this->editorial_model->agregar_editorial($data)) {
                    $this->session->set_flashdata('mensaje', 
                        "Editorial '{$nombreEditorial}' agregada correctamente.");
                    redirect('editoriales/index');
                } else {
                    throw new Exception('Error al guardar en la base de datos.');
                }
    
            } catch (Exception $e) {
                log_message('error', 'Error al agregar editorial: ' . $e->getMessage());
                $this->session->set_flashdata('error', 'Error al procesar la solicitud.');
                redirect('editoriales/agregar');
            }
        }
    }
    
    // Agregar método de validación personalizado
    public function validar_nombre_editorial($nombre) {
        // Permitir letras (con acentos), números y espacios
        if (!preg_match('/^[A-ZÁÉÍÓÚÜÑa-záéíóúüñ0-9\s]+$/', $nombre)) {
            $this->form_validation->set_message('validar_nombre_editorial', 
                'El nombre solo puede contener letras, números y espacios.');
            return FALSE;
        }
        return TRUE;
    }

    public function editar($idEditorial) {
        $this->_verificar_permisos();
        $data['editorial'] = $this->editorial_model->obtener_editorial($idEditorial);
        
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('editoriales/editar', $data);
        $this->load->view('inc/footer');
    }

    public function editarbd() {
        $this->_verificar_permisos();
        $idEditorial = $this->input->post('idEditorial');
        
        // Configurar reglas de validación que permitan espacios
        $this->form_validation->set_rules(
            'nombreEditorial', 
            'Nombre de la Editorial',
            'required|trim|callback_validar_nombre_editorial|is_unique[EDITORIAL.nombreEditorial.idEditorial.' . $idEditorial . ']',
            array(
                'required' => 'El nombre de la editorial es requerido.',
                'is_unique' => 'Este nombre de editorial ya existe.'
            )
        );
    
        if ($this->form_validation->run() == FALSE) {
            $data['editorial'] = $this->editorial_model->obtener_editorial($idEditorial);
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
            redirect('editoriales');
        }
    }
    
   
    public function eliminar($idEditorial) {
        $this->_verificar_permisos();
        
        $data = array(
            'estado' => $this->input->post('estado'),
            'fechaActualizacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $this->session->userdata('idUsuario')
        );
    
        if ($this->editorial_model->eliminar_editorial($idEditorial, $data)) {
            $mensaje = $data['estado'] == 0 ? 'Editorial eliminada correctamente.' : 'Editorial habilitada correctamente.';
            $this->session->set_flashdata('mensaje', $mensaje);
        } else {
            $this->session->set_flashdata('error', 'Error al modificar el estado de la editorial.');
        }
        redirect('editoriales/index');
    }
}




