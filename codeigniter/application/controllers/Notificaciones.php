<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificaciones extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Notificacion_model');
        $this->load->library('session');
        $this->load->library('email');
        $this->load->model('Publicacion_model');
        $this->load->model('Usuario_model');

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
    /*public function ver($idNotificacion) {
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
    }*/
    public function ver($idNotificacion) {
        $this->_verificar_sesion();
        
        $notificacion = $this->Notificacion_model->obtener_notificacion($idNotificacion);
        
        if (!$notificacion) {
            $this->session->set_flashdata('error', 'La notificación no existe.');
            redirect('notificaciones');
            return;
        }
        
        // Marcar la notificación como leída
        $this->Notificacion_model->marcar_como_leida($idNotificacion);
        
        // Obtener datos relacionados según el tipo de notificación
        $datos_relacionados = $this->_obtener_datos_relacionados($notificacion);
        
        // Redirigir según el tipo de notificación
        switch ($notificacion->tipo) {
            case NOTIFICACION_NUEVA_SOLICITUD:
                redirect('solicitudes/pendientes');
                break;
                
            case NOTIFICACION_SOLICITUD_PRESTAMO:
            case NOTIFICACION_APROBACION_PRESTAMO:
            case NOTIFICACION_RECHAZO_PRESTAMO:
                if (isset($datos_relacionados['idSolicitud'])) {
                    redirect('solicitudes/detalle/' . $datos_relacionados['idSolicitud']);
                } else {
                    redirect('solicitudes/mis_solicitudes');
                }
                break;
                
            case NOTIFICACION_DEVOLUCION:
            case NOTIFICACION_VENCIMIENTO:
                if (isset($datos_relacionados['idPrestamo'])) {
                    redirect('prestamos/detalle/' . $datos_relacionados['idPrestamo']);
                } else {
                    redirect('prestamos/mis_prestamos');
                }
                break;
                
            case NOTIFICACION_DISPONIBILIDAD:
                if (isset($datos_relacionados['idPublicacion'])) {
                    redirect('publicaciones/ver/' . $datos_relacionados['idPublicacion']);
                } else {
                    redirect('publicaciones');
                }
                break;
                
            default:
                redirect('notificaciones');
        }
    }
    
    private function _obtener_datos_relacionados($notificacion) {
        // Asegurarse de que el modelo está cargado
        if (!isset($this->Solicitud_model)) {
            $this->load->model('Solicitud_model');
        }
        if (!isset($this->Prestamo_model)) {
            $this->load->model('Prestamo_model');
        }
        
        $datos = array();
        
        switch ($notificacion->tipo) {
            case NOTIFICACION_SOLICITUD_PRESTAMO:
            case NOTIFICACION_APROBACION_PRESTAMO:
            case NOTIFICACION_RECHAZO_PRESTAMO:
                $solicitud = $this->Solicitud_model->obtener_solicitud_por_publicacion(
                    $notificacion->idPublicacion,
                    $notificacion->idUsuario
                );
                if ($solicitud) {
                    $datos['idSolicitud'] = $solicitud->idSolicitud;
                }
                break;
                
            case NOTIFICACION_DEVOLUCION:
            case NOTIFICACION_VENCIMIENTO:
                $prestamo = $this->Prestamo_model->obtener_prestamo_por_publicacion(
                    $notificacion->idPublicacion,
                    $notificacion->idUsuario
                );
                if ($prestamo) {
                    $datos['idPrestamo'] = $prestamo->idPrestamo;
                }
                break;
        }
        
        return $datos;
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


    private function _enviar_email_disponibilidad($idUsuario, $publicacion) {
        $usuario = $this->Usuario_model->obtener_usuario($idUsuario);
        $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca UMSS');
        $this->email->to($usuario->email);
        $this->email->subject('Publicación Disponible - Hemeroteca UMSS');
        $this->email->message('La publicación "' . $publicacion->titulo . '" ya está disponible en la hemeroteca.');
        $this->email->send();
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
                NOTIFICACION_ELIMINACION
                
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
                    NOTIFICACION_VENCIMIENTO,
                    NOTIFICACION_ELIMINACION
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
    // Método para gestionar las preferencias de notificación
    public function configurar_preferencias() {
        $this->_verificar_sesion();
        
        $idUsuario = $this->session->userdata('idUsuario');
        
        if ($this->input->post()) {
            $preferencias = array(
                'notificarDisponibilidad' => $this->input->post('notificar_disponibilidad') ? 1 : 0,
                'notificarEmail' => $this->input->post('notificar_email') ? 1 : 0,
                'notificarSistema' => $this->input->post('notificar_sistema') ? 1 : 0
            );
            
            if ($this->Notificacion_model->actualizar_preferencias($idUsuario, $preferencias)) {
                $this->session->set_flashdata('mensaje', 'Preferencias de notificación actualizadas correctamente');
            } else {
                $this->session->set_flashdata('error', 'Error al actualizar las preferencias');
            }
            redirect('notificaciones/preferencias');
        }

        $data['preferencias'] = $this->Notificacion_model->obtener_preferencias($idUsuario);
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('notificaciones/preferencias', $data);
        $this->load->view('inc/footer');
    }

    // Método mejorado para notificar disponibilidad
    public function notificar_disponibilidad($idPublicacion) {
        $this->db->trans_start();

        try {
            $publicacion = $this->Publicacion_model->obtener_publicacion_detallada($idPublicacion);
            if (!$publicacion) {
                throw new Exception('Publicación no encontrada');
            }

            $usuarios_interesados = $this->Notificacion_model->obtener_usuarios_interesados($idPublicacion);
            
            foreach ($usuarios_interesados as $usuario) {
                $preferencias = $this->Notificacion_model->obtener_preferencias($usuario->idUsuario);
                
                if (!$preferencias) continue;

                // Notificación del sistema
                if ($preferencias->notificarSistema) {
                    $this->_crear_notificacion_sistema($usuario->idUsuario, $publicacion);
                }

                // Notificación por email
                if ($preferencias->notificarEmail) {
                    $this->_enviar_notificacion_email($usuario->idUsuario, $publicacion);
                }

                // Actualizar estado de interés
                $this->Notificacion_model->actualizar_estado_interes(
                    $usuario->idUsuario,
                    $idPublicacion,
                    ESTADO_INTERES_NOTIFICADO
                );
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error en notificar_disponibilidad: ' . $e->getMessage());
            return false;
        }
    }

    // Método mejorado para agregar interés
    public function agregar_interes($idPublicacion) {
        $this->_verificar_sesion();
        
        if ($this->session->userdata('rol') !== 'lector') {
            $this->session->set_flashdata('error', 'Acceso no autorizado');
            redirect('publicaciones');
            return;
        }

        $idUsuario = $this->session->userdata('idUsuario');
        
        $this->db->trans_start();
        
        try {
            // Verificar si ya existe un interés
            $interes_existente = $this->Notificacion_model->obtener_estado_interes($idUsuario, $idPublicacion);
            
            if ($interes_existente) {
                throw new Exception('Ya existe una notificación registrada para esta publicación');
            }

            // Registrar nuevo interés
            $resultado = $this->Notificacion_model->registrar_interes($idUsuario, $idPublicacion);
            
            if (!$resultado) {
                throw new Exception('Error al registrar el interés');
            }

            $this->session->set_flashdata('mensaje', 'Notificación registrada correctamente');
            $this->db->trans_complete();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $e->getMessage());
        }

        redirect('publicaciones');
    }

    private function _crear_notificacion_sistema($idUsuario, $publicacion) {
        return $this->Notificacion_model->crear_notificacion(
            $idUsuario,
            $publicacion->idPublicacion,
            NOTIFICACION_DISPONIBILIDAD,
            sprintf(
                "La publicación '%s' ya está disponible para préstamo",
                $publicacion->titulo
            )
        );
    }

    private function _enviar_notificacion_email($idUsuario, $publicacion) {
        $usuario = $this->Usuario_model->obtener_usuario($idUsuario);
        if (!$usuario || !$usuario->email) return false;

        $this->email->initialize($this->_get_email_config());
        $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca UMSS');
        $this->email->to($usuario->email);
        $this->email->subject('Publicación Disponible - Hemeroteca UMSS');
        
        $mensaje = $this->load->view('emails/publicacion_disponible', [
            'usuario' => $usuario,
            'publicacion' => $publicacion
        ], true);

        $this->email->message($mensaje);
        return $this->email->send();
    }

    private function _get_email_config() {
        return array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_user' => 'quirozmolinamaritza@gmail.com',
            'smtp_pass' => 'zdmk qkfw wgdf lshq',
            'smtp_crypto' => 'tls',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        );
    }
    public function agregar_interes_simple($idPublicacion) {
        $this->_verificar_sesion();
        
        if ($this->session->userdata('rol') !== 'lector') {
            $this->session->set_flashdata('error', 'Solo los lectores pueden solicitar notificaciones.');
            redirect('publicaciones');
            return;
        }
    
        $idUsuario = $this->session->userdata('idUsuario');
        
        // Verificar que no exista un interés activo
        $interes_existente = $this->Notificacion_model->obtener_estado_interes($idUsuario, $idPublicacion);
        
        if ($interes_existente && $interes_existente->estado == ESTADO_INTERES_SOLICITADO) {
            $this->session->set_flashdata('error', 'Ya tienes una notificación activa para esta publicación.');
            redirect('publicaciones');
            return;
        }
    
        // Registrar el nuevo interés
        $data = [
            'idUsuario' => $idUsuario,
            'idPublicacion' => $idPublicacion,
            'fechaInteres' => date('Y-m-d H:i:s'),
            'estado' => ESTADO_INTERES_SOLICITADO
        ];
    
        if ($this->Notificacion_model->agregar_interes_publicacion($data)) {
            // Crear notificación de confirmación
            $this->Notificacion_model->crear_notificacion(
                $idUsuario,
                $idPublicacion,
                NOTIFICACION_SOLICITUD_PRESTAMO,
                'Te notificaremos cuando esta publicación esté disponible.'
            );
            
            $this->session->set_flashdata('mensaje', 'Se te notificará cuando la publicación esté disponible.');
        } else {
            $this->session->set_flashdata('error', 'Hubo un error al registrar la notificación.');
        }
        
        redirect('publicaciones');
    }
    
    public function cancelar_interes($idPublicacion) {
        $this->_verificar_sesion();
        
        if ($this->session->userdata('rol') !== 'lector') {
            $this->session->set_flashdata('error', 'Acceso no autorizado.');
            redirect('publicaciones');
            return;
        }
    
        $idUsuario = $this->session->userdata('idUsuario');
        
        if ($this->Notificacion_model->eliminar_interes($idUsuario, $idPublicacion)) {
            $this->session->set_flashdata('mensaje', 'Notificación cancelada.');
        } else {
            $this->session->set_flashdata('error', 'Error al cancelar la notificación.');
        }
        
        redirect('publicaciones');
    }
}