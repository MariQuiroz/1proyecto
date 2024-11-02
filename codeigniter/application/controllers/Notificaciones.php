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
        $this->load->view('notificaciones/preferencias', $data);
    }

    public function notificar_disponibilidad($idPublicacion) {
        $usuarios_interesados = $this->Notificacion_model->obtener_usuarios_interesados($idPublicacion);
        $publicacion = $this->Publicacion_model->obtener_publicacion($idPublicacion);

        foreach ($usuarios_interesados as $usuario) {
            $preferencias = $this->Notificacion_model->obtener_preferencias($usuario->idUsuario);
            
            if ($preferencias->notificarSistema) {
                $this->Notificacion_model->crear_notificacion(
                    $usuario->idUsuario,
                    $idPublicacion,
                    'La publicación "' . $publicacion->titulo . '" ya está disponible.'
                );
            }

            if ($preferencias->notificarEmail) {
                $this->_enviar_email_disponibilidad($usuario->idUsuario, $publicacion);
            }
        }
    }

    private function _enviar_email_disponibilidad($idUsuario, $publicacion) {
        $usuario = $this->Usuario_model->obtener_usuario($idUsuario);
        $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca UMSS');
        $this->email->to($usuario->email);
        $this->email->subject('Publicación Disponible - Hemeroteca UMSS');
        $this->email->message('La publicación "' . $publicacion->titulo . '" ya está disponible en la hemeroteca.');
        $this->email->send();
    }
    public function agregar_interes($idPublicacion) {
        $this->_verificar_sesion();
        
        if ($this->session->userdata('rol') !== 'lector') {
            $this->session->set_flashdata('error', 'Solo los lectores pueden solicitar notificaciones.');
            redirect('publicaciones');
        }

        $idUsuario = $this->session->userdata('idUsuario');
        if ($this->Notificacion_model->agregar_interes_publicacion($idUsuario, $idPublicacion)) {
            $this->session->set_flashdata('mensaje', 'Se te notificará cuando esta publicación esté disponible.');
        } else {
            $this->session->set_flashdata('error', 'Hubo un error al registrar tu interés.');
        }
        redirect('publicaciones');
    }

    public function marcar_todas_leidas() {
        $this->_verificar_sesion();
        
        $idUsuario = $this->session->userdata('idUsuario');
        $rol = $this->session->userdata('rol');
        
        if (!$idUsuario || !$rol) {
            $this->session->set_flashdata('error', 'Sesión no válida');
            redirect('usuarios/login');
            return;
        }
        
        // Verificar el tipo de notificaciones según el rol
        $tipos_notificacion = [];
        if ($rol == 'encargado') {
            $tipos_notificacion = [
                NOTIFICACION_NUEVA_SOLICITUD,
                NOTIFICACION_APROBACION_PRESTAMO,
                NOTIFICACION_RECHAZO_PRESTAMO
                // Otros tipos específicos para encargados
            ];
        } else if ($rol == 'lector') {
            $tipos_notificacion = [
                NOTIFICACION_SOLICITUD_PRESTAMO,
                NOTIFICACION_APROBACION_PRESTAMO,
                NOTIFICACION_RECHAZO_PRESTAMO,
                NOTIFICACION_DEVOLUCION
                // Otros tipos específicos para lectores
            ];
        } else if ($rol == 'administrador') {
            $tipos_notificacion = [
                NOTIFICACION_VENCIMIENTO,
                // Otros tipos específicos para administradores
                // NO incluir NOTIFICACION_NUEVA_SOLICITUD
            ];
        }
        
        if ($this->Notificacion_model->marcar_todas_leidas($idUsuario, $rol, $tipos_notificacion)) {
            $this->session->set_flashdata('mensaje', 'Todas las notificaciones han sido marcadas como leídas');
        } else {
            $this->session->set_flashdata('error', 'Hubo un error al marcar las notificaciones');
        }
        
        $previous_url = $this->session->userdata('previous_url');
        redirect($previous_url ? $previous_url : 'notificaciones');
    }
    
    public function eliminar_leidas() {
        $this->_verificar_sesion();
        
        $idUsuario = $this->session->userdata('idUsuario');
        $rol = $this->session->userdata('rol');
        
        if (!$idUsuario || !$rol) {
            $this->session->set_flashdata('error', 'Sesión no válida');
            redirect('usuarios/index', 'refresh');
            return;
        }
        
        // Verificar el tipo de notificaciones según el rol
        $tipos_notificacion = [];
        switch($rol) {
            case 'encargado':
                $tipos_notificacion = [
                    NOTIFICACION_NUEVA_SOLICITUD,
                    NOTIFICACION_APROBACION_PRESTAMO,
                    NOTIFICACION_RECHAZO_PRESTAMO
                ];
                break;
            case 'lector':
                $tipos_notificacion = [
                    NOTIFICACION_SOLICITUD_PRESTAMO,
                    NOTIFICACION_APROBACION_PRESTAMO,
                    NOTIFICACION_RECHAZO_PRESTAMO,
                    NOTIFICACION_DEVOLUCION
                ];
                break;
            case 'administrador':
                $tipos_notificacion = [
                    NOTIFICACION_VENCIMIENTO
                    // NO incluir NOTIFICACION_NUEVA_SOLICITUD
                ];
                break;
        }
        
        if ($this->Notificacion_model->eliminar_notificaciones_leidas($idUsuario, $rol, $tipos_notificacion)) {
            $this->session->set_flashdata('mensaje', 'Las notificaciones leídas han sido eliminadas');
        } else {
            $this->session->set_flashdata('error', 'Hubo un error al eliminar las notificaciones');
        }
        
        redirect('notificaciones', 'refresh');
    }
    
    public function eliminar($idNotificacion) {
        $this->_verificar_sesion();
        
        $idUsuario = $this->session->userdata('idUsuario');
        $rol = $this->session->userdata('rol');
        
        // Verificar que la notificación pertenezca al usuario y sea del tipo correcto según su rol
        if ($this->Notificacion_model->validar_notificacion($idNotificacion, $idUsuario, $rol)) {
            if ($this->Notificacion_model->eliminar_notificacion($idNotificacion, $idUsuario)) {
                $this->session->set_flashdata('mensaje', 'Notificación eliminada correctamente');
            } else {
                $this->session->set_flashdata('error', 'Error al eliminar la notificación');
            }
        } else {
            $this->session->set_flashdata('error', 'No tienes permiso para eliminar esta notificación');
        }
        
        redirect('notificaciones', 'refresh');
    }
}