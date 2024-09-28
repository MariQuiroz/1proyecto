
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitudes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('solicitud_prestamo_model');
        $this->load->model('publicacion_model');
        $this->load->model('notificacion_model');
        $this->load->model('prestamo_model');
        $this->load->library('session');
        $this->load->library('form_validation');
    }

    private function _verificar_sesion() {
        if (!$this->session->userdata('logged_in')) {
            redirect('usuarios/index');
        }
    }

    public function crear($idPublicacion) {
        // Verificar si el usuario está autenticado
        $this->_verificar_sesion();
    
        // Verificar si el idPublicacion es válido
        if (!is_numeric($idPublicacion)) {
            show_404(); // Mostrar error 404 si el id no es numérico
        }
    
        // Obtener los detalles de la publicación
        $data['publicacion'] = $this->publicacion_model->get_publicacion($idPublicacion);
        
        // Verificar si la publicación existe
        if (empty($data['publicacion'])) {
            show_404(); // Mostrar error 404 si la publicación no existe
        }
    
        // Establecer reglas de validación
        $this->form_validation->set_rules('motivo', 'Motivo', 'required', array('required' => 'El motivo es obligatorio.'));
    
        // Si la validación falla, mostrar el formulario nuevamente
        if ($this->form_validation->run() === FALSE) {
            // Cargar la vista del formulario de solicitud
            $this->load->view('inc/header');
            $this->load->view('solicitudes/solicitud_form', $data); // Vista que muestra el formulario
            $this->load->view('inc/footer');
        } else {
            // Insertar nueva solicitud en la base de datos
            $solicitudData = array(
                'idUsuario' => $this->session->userdata('idUsuario'),
                'idPublicacion' => $idPublicacion,
                'motivoConsulta' => $this->input->post('motivo'),
                'estadoSolicitud' => 'pendiente',
                'fechaSolicitud' => date('Y-m-d H:i:s')
            );
    
            // Llamar al modelo para crear la solicitud
            $idSolicitud = $this->solicitud_prestamo_model->crear_solicitud($solicitudData);
            
            // Verificar si la solicitud se creó correctamente
            if ($idSolicitud) {
                $this->session->set_flashdata('success', 'Solicitud creada correctamente.');
                $this->notificar_encargados($idSolicitud);
            } else {
                $this->session->set_flashdata('error', 'Hubo un problema al crear la solicitud.');
            }
    
            // Redirigir al panel del lector
            redirect('lector/panel');
        }
    }
    
    private function notificar_encargados($idSolicitud) {
        // Lógica para notificar a los encargados
        $encargados = $this->usuario_model->obtener_usuarios_por_rol('encargado');
        foreach ($encargados as $encargado) {
            $dataNotificacion = array(
                'idUsuario' => $encargado->idUsuario,
                'idSolicitud' => $idSolicitud,
                'mensaje' => 'Nueva solicitud de préstamo pendiente.',
                'estadoNotificacion' => ESTADO_NOTIFICACION_PENDIENTE,
                'fechaNotificacion' => date('Y-m-d H:i:s')
            );
            $this->notificacion_model->crear_notificacion($dataNotificacion);
        }
    }

    public function listar_pendientes() {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('rol') != 'encargado') {
            redirect('usuarios/index');
        }

        $data['solicitudes'] = $this->solicitud_prestamo_model->get_solicitudes_pendientes();
        $this->load->view('solicitudes/pendientes', $data);
    }

    public function aprobar($idSolicitud) {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('rol') != 'encargado') {
            redirect('usuarios/index');
        }

        $this->db->trans_start();

        $this->solicitud_prestamo_model->actualizar_estado_solicitud($idSolicitud, 'aprobada');
        $solicitud = $this->solicitud_prestamo_model->get_solicitud($idSolicitud);
        $this->publicacion_model->actualizar_estado_publicacion($solicitud->idPublicacion, 'en_prestamo');

        // Crear el préstamo
        $dataPrestamo = array(
            'idSolicitud' => $idSolicitud,
            'fechaPrestamo' => date('Y-m-d H:i:s'),
            'fechaDevolucionEstimada' => date('Y-m-d', strtotime('+7 days'))
        );
        $this->load->model('prestamo_model');
        $this->prestamo_model->crear_prestamo($dataPrestamo);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Hubo un problema al aprobar la solicitud.');
        } else {
            $this->session->set_flashdata('success', 'Solicitud aprobada y préstamo creado exitosamente.');
            // Notificar al usuario
            $this->notificar_usuario_aprobacion($solicitud->idUsuario, $solicitud->idPublicacion);
        }

        redirect('solicitudes/listar_pendientes');
    }

    /*public function rechazar($idSolicitud) {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('rol') != 'encargado') {
            redirect('usuarios/login');
        }

        $this->solicitud_prestamo_model->actualizar_estado_solicitud($idSolicitud, 'rechazada');
        $solicitud = $this->solicitud_prestamo_model->get_solicitud($idSolicitud);

        $this->session->set_flashdata('success', 'Solicitud rechazada.');
*/
    /*public function aprobar($idSolicitud) {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('rol') != 'encargado') {
            redirect('usuarios/index');
        }

        $this->db->trans_start();

        $solicitud = $this->solicitud_prestamo_model->get_solicitud($idSolicitud);
        if (!$solicitud || $solicitud->estado != 'pendiente') {
            $this->session->set_flashdata('error', 'Solicitud no válida para aprobación.');
            redirect('solicitudes/listar_pendientes');
        }

        $this->solicitud_prestamo_model->actualizar_estado_solicitud($idSolicitud, 'aprobada');
        $this->publicacion_model->actualizar_estado_publicacion($solicitud->idPublicacion, 'en_prestamo');

        $dataPrestamo = array(
            'idSolicitud' => $idSolicitud,
            'fechaPrestamo' => date('Y-m-d H:i:s'),
            'fechaDevolucionEstimada' => date('Y-m-d', strtotime('+7 days'))
        );
        $idPrestamo = $this->prestamo_model->crear_prestamo($dataPrestamo);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Hubo un problema al aprobar la solicitud.');
        } else {
            $this->session->set_flashdata('success', 'Solicitud aprobada y préstamo creado exitosamente.');
            $this->notificar_usuario_aprobacion($solicitud->idUsuario, $solicitud->idPublicacion);
        }

        redirect('solicitudes/listar_pendientes');
    }*/

    public function rechazar($idSolicitud) {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('rol') != 'encargado') {
            redirect('usuarios/index');
        }

        $solicitud = $this->solicitud_prestamo_model->get_solicitud($idSolicitud);
        if (!$solicitud || $solicitud->estado != 'pendiente') {
            $this->session->set_flashdata('error', 'Solicitud no válida para rechazo.');
            redirect('solicitudes/listar_pendientes');
        }

        $this->solicitud_prestamo_model->actualizar_estado_solicitud($idSolicitud, 'rechazada');
        $this->session->set_flashdata('success', 'Solicitud rechazada.');
        $this->notificar_usuario_rechazo($solicitud->idUsuario, $solicitud->idPublicacion);

        redirect('solicitudes/listar_pendientes');
    }

    private function notificar_usuario_aprobacion($idUsuario, $idPublicacion) {
        $dataNotificacion = array(
            'idUsuario' => $idUsuario,
            'idPublicacion' => $idPublicacion,
            'mensaje' => 'Su solicitud de préstamo ha sido aprobada.',
            'fechaNotificacion' => date('Y-m-d H:i:s')
        );
        $this->notificacion_model->crear_notificacion($dataNotificacion);
    }

    private function notificar_usuario_rechazo($idUsuario, $idPublicacion) {
        $dataNotificacion = array(
            'idUsuario' => $idUsuario,
            'idPublicacion' => $idPublicacion,
            'mensaje' => 'Su solicitud de préstamo ha sido rechazada.',
            'fechaNotificacion' => date('Y-m-d H:i:s')
        );
        $this->notificacion_model->crear_notificacion($dataNotificacion);
    }

    public function mis_solicitudes() {
        if (!$this->session->userdata('logged_in')) {
            redirect('usuarios/index');
        }

        $idUsuario = $this->session->userdata('idUsuario');
        $data['solicitudes'] = $this->solicitud_prestamo_model->get_solicitudes_usuario($idUsuario);
        $this->load->view('solicitudes/mis_solicitudes', $data);
    }
   /* public function crear() {
        if (!$this->session->userdata('logged_in')) {
            redirect('usuarios/index');
        }
    
        $this->form_validation->set_rules('idPublicacion', 'Publicación', 'required');
    
        if ($this->form_validation->run() == FALSE) {
            $data['publicaciones'] = $this->publicacion_model->obtener_publicaciones_disponibles();
            $this->load->view('solicitudes/crear', $data);
        } else {
            $data = array(
                'idUsuario' => $this->session->userdata('idUsuario'),
                'idPublicacion' => $this->input->post('idPublicacion'),
                'estado' => 'pendiente',
                'fechaSolicitud' => date('Y-m-d H:i:s')
            );
    
            if ($this->solicitud_prestamo_model->crear_solicitud($data)) {
                $this->session->set_flashdata('success', 'Solicitud enviada correctamente.');
            } else {
                $this->session->set_flashdata('error', 'Hubo un problema al enviar la solicitud.');
            }
            redirect('solicitudes/mis_solicitudes');
        }
    }*/
}