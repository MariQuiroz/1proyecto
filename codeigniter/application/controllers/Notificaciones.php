<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificaciones extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('notificacion_model');
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
        $idUsuario = $this->session->userdata('idUsuario');
        $data['notificaciones'] = $this->notificacion_model->get_notificaciones_usuario($idUsuario);
        
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('notificaciones/lista', $data);
        $this->load->view('inc/footer');
    }

    public function marcar_como_leida($idNotificacion) {
        $this->_verificar_sesion();
        if ($this->notificacion_model->marcar_como_leida($idNotificacion)) {
            $this->session->set_flashdata('mensaje', 'Notificación marcada como leída.');
        } else {
            $this->session->set_flashdata('error', 'Error al marcar la notificación como leída.');
        }
        redirect('notificaciones');
    }

    public function eliminar($idNotificacion) {
        $this->_verificar_sesion();
        if ($this->notificacion_model->eliminar_notificacion($idNotificacion)) {
            $this->session->set_flashdata('mensaje', 'Notificación eliminada correctamente.');
        } else {
            $this->session->set_flashdata('error', 'Error al eliminar la notificación.');
        }
        redirect('notificaciones');
    }
}