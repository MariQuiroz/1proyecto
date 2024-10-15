<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificaciones extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Notificacion_model');
        $this->load->library('session');
        $this->load->library('email');
    }

    private function _verificar_sesion() {
        if (!$this->session->userdata('login')) {
            redirect('usuarios/login');
        }
    }

    public function index() {
        $this->_verificar_sesion();
        $idUsuario = $this->session->userdata('idUsuario');
        $rol = $this->session->userdata('rol');
    
        if (!$idUsuario || !$rol) {
            redirect('usuarios/login');
        }
    
        $notificaciones = $this->Notificacion_model->obtener_notificaciones($idUsuario, $rol);
        $data['notificaciones'] = $notificaciones;
        $data['rol'] = $rol;
    
        // Pasar las constantes de tipo de notificación a la vista
        $data['NOTIFICACION_SOLICITUD_PRESTAMO'] = NOTIFICACION_SOLICITUD_PRESTAMO;
        $data['NOTIFICACION_APROBACION_PRESTAMO'] = NOTIFICACION_APROBACION_PRESTAMO;
        $data['NOTIFICACION_RECHAZO_PRESTAMO'] = NOTIFICACION_RECHAZO_PRESTAMO;
        $data['NOTIFICACION_DEVOLUCION'] = NOTIFICACION_DEVOLUCION;
        $data['NOTIFICACION_DISPONIBILIDAD'] = NOTIFICACION_DISPONIBILIDAD;
        $data['NOTIFICACION_NUEVA_SOLICITUD'] = NOTIFICACION_NUEVA_SOLICITUD;
        $data['NOTIFICACION_VENCIMIENTO'] = NOTIFICACION_VENCIMIENTO;
    
        log_message('info', 'Cargando notificaciones para usuario ID=' . $idUsuario . ', Rol=' . $rol . ', Cantidad=' . count($notificaciones));
    
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('notificaciones/lista', $data);
        $this->load->view('inc/footer');
    }

    public function marcar_leida($idNotificacion) {
        $this->_verificar_sesion();
        $this->Notificacion_model->marcar_como_leida($idNotificacion);
        redirect('notificaciones');
    }

    public function preferencias() {
        $this->_verificar_sesion();
        $idUsuario = $this->session->userdata('idUsuario');

        if ($this->input->post()) {
            $preferencias = array(
                'disponibilidad' => $this->input->post('notificar_disponibilidad') ? TRUE : FALSE,
                'email' => $this->input->post('notificar_email') ? TRUE : FALSE,
                'sistema' => $this->input->post('notificar_sistema') ? TRUE : FALSE
            );
            $this->Notificacion_model->guardar_preferencias($idUsuario, $preferencias);
            $this->session->set_flashdata('mensaje', 'Preferencias actualizadas correctamente.');
            redirect('notificaciones/preferencias');
        }

        $data['preferencias'] = $this->Notificacion_model->obtener_preferencias($idUsuario);
        
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('notificaciones/preferencias', $data);
        $this->load->view('inc/footer');
    }

    public function agregar_interes($idPublicacion) {
        $this->_verificar_sesion();
        $idUsuario = $this->session->userdata('idUsuario');
        
        if ($this->Notificacion_model->agregar_interes_publicacion($idUsuario, $idPublicacion)) {
            $this->session->set_flashdata('mensaje', 'Se te notificará cuando esta publicación esté disponible.');
        } else {
            $this->session->set_flashdata('error', 'Hubo un error al registrar tu interés.');
        }
        
        redirect('publicaciones/ver/' . $idPublicacion);
    }

    private function _enviar_email($to, $subject, $message) {
        $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca UMSS');
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
        return $this->email->send();
    }
    public function ver($idNotificacion) {
        $this->_verificar_sesion();
        
        $notificacion = $this->Notificacion_model->obtener_notificacion($idNotificacion);
        
        if (!$notificacion) {
            $this->session->set_flashdata('error', 'La notificación no existe.');
            redirect('notificaciones');
        }
        
        // Marcar la notificación como leída
        $this->Notificacion_model->marcar_como_leida($idNotificacion);
        
        // Redirigir según el tipo de notificación
        switch ($notificacion->tipo) {
            case NOTIFICACION_NUEVA_SOLICITUD:
                redirect('solicitudes/pendientes');
                break;
            case NOTIFICACION_SOLICITUD_PRESTAMO:
            case NOTIFICACION_APROBACION_PRESTAMO:
            case NOTIFICACION_RECHAZO_PRESTAMO:
                redirect('solicitudes/detalle/' . $notificacion->idPublicacion);
                break;
            case NOTIFICACION_DEVOLUCION:
            case NOTIFICACION_VENCIMIENTO:
                redirect('prestamos/detalle/' . $notificacion->idPublicacion);
                break;
            case NOTIFICACION_DISPONIBILIDAD:
                redirect('publicaciones/ver/' . $notificacion->idPublicacion);
                break;
            default:
                redirect('notificaciones');
        }
    }
}