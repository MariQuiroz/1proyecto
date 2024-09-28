<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prestamos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('prestamo_model');
        $this->load->model('publicacion_model');
        $this->load->model('notificacion_model');
        $this->load->library('session');
        $this->load->library('form_validation');
    }

    public function index() {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('rol') != 'encargado') {
            redirect('usuarios/index');
        }

        $data['prestamos'] = $this->prestamo_model->get_prestamos_activos();
        $this->load->view('prestamos/lista', $data);
    }

    public function devolver($idPrestamo) {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('rol') != 'encargado') {
            redirect('usuarios/index');
        }
    
        $this->db->trans_start();
    
        $prestamo = $this->prestamo_model->get_prestamo($idPrestamo);
        if (!$prestamo || $prestamo->estado != ESTADO_PRESTAMO_ACTIVO) {
            $this->session->set_flashdata('error', 'Préstamo no válido para devolución.');
            redirect('prestamos');
        }
    
        $this->prestamo_model->actualizar_estado_prestamo($idPrestamo, ESTADO_PRESTAMO_FINALIZADO); // Usar constante
        $this->prestamo_model->set_fecha_devolucion_real($idPrestamo, date('Y-m-d'));
        
        $solicitud = $this->solicitud_prestamo_model->get_solicitud($prestamo->idSolicitud);
        $this->publicacion_model->actualizar_estado_publicacion($solicitud->idPublicacion, ESTADO_PUBLICACION_DISPONIBLE); // Usar constante
    
        $this->db->trans_complete();
    
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Hubo un problema al procesar la devolución.');
        } else {
            $this->session->set_flashdata('success', 'Devolución procesada exitosamente.');
            $this->notificar_disponibilidad($solicitud->idPublicacion);
        }
    
        redirect('prestamos');
    }

    private function notificar_disponibilidad($idPublicacion) {
        $usuariosInteresados = $this->notificacion_model->get_usuarios_interesados($idPublicacion);
        foreach ($usuariosInteresados as $usuario) {
            $dataNotificacion = array(
                'idUsuario' => $usuario->idUsuario,
                'idPublicacion' => $idPublicacion,
                'mensaje' => 'La publicación que le interesa ahora está disponible.',
                'fechaNotificacion' => date('Y-m-d H:i:s')
            );
            $this->notificacion_model->crear_notificacion($dataNotificacion);
        }
    }

    public function mis_prestamos() {
        if (!$this->session->userdata('logged_in')) {
            redirect('usuarios/index');
        }

        $idUsuario = $this->session->userdata('idUsuario');
        $data['prestamos'] = $this->prestamo_model->get_prestamos_usuario($idUsuario);
        $this->load->view('prestamos/mis_prestamos', $data);
    }

    public function solicitar_notificacion($idPublicacion) {
        if (!$this->session->userdata('logged_in')) {
            redirect('usuarios/index');
        }

        $idUsuario = $this->session->userdata('idUsuario');
        $this->notificacion_model->registrar_interes($idUsuario, $idPublicacion);
        $this->session->set_flashdata('success', 'Se le notificará cuando la publicación esté disponible.');
        redirect('publicaciones');
    }
}