<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prestamos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Prestamo_model');
        $this->load->model('Solicitud_model');
        $this->load->model('Publicacion_model');
        $this->load->library('session');
        $this->load->library('form_validation');
    }

    private function _verificar_sesion() {
        if (!$this->session->userdata('login')) {
            redirect('usuarios/login');
        }
    }

    private function _verificar_rol($roles_permitidos) {
        $this->_verificar_sesion();
        $rol_actual = $this->session->userdata('rol');
        if (!in_array($rol_actual, $roles_permitidos)) {
            $this->session->set_flashdata('error', 'No tienes permiso para realizar esta acción.');
            redirect('usuarios/panel');
        }
    }

    public function iniciar($idSolicitud) {
        $this->_verificar_rol(['administrador', 'encargado']);
    
        $solicitud = $this->Solicitud_model->obtener_solicitud($idSolicitud);
        if (!$solicitud || $solicitud->estadoSolicitud != ESTADO_SOLICITUD_APROBADA) {
            $this->session->set_flashdata('error', 'La solicitud no es válida para iniciar un préstamo.');
            redirect('solicitudes/pendientes');
        }
    
        $this->db->trans_start();
    
        $idEncargado = $this->session->userdata('idUsuario');
        $resultado = $this->Prestamo_model->iniciar_prestamo($idSolicitud, $idEncargado);
    
        if ($resultado) {
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Hubo un error al iniciar el préstamo. Por favor, intente de nuevo.');
            } else {
                $this->session->set_flashdata('mensaje', 'Préstamo iniciado con éxito.');
                
                // Obtener los datos para la ficha de préstamo
                $ficha_prestamo = $this->Prestamo_model->obtener_datos_ficha_prestamo($resultado);
                
                // Cargar la vista de la ficha de préstamo
                $this->load->view('prestamos/ficha_prestamo', $ficha_prestamo);
                return; // Detenemos la ejecución aquí para mostrar la ficha
            }
        } else {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'No se pudo iniciar el préstamo. Verifique la disponibilidad de la publicación.');
        }
    
        redirect('prestamos/activos');
    }
    
    public function generar_ficha($idPrestamo) {
        $this->_verificar_rol(['administrador', 'encargado']);

        $ficha_prestamo = $this->generar_ficha_prestamo($idPrestamo);
        $this->load->view('prestamos/ficha_prestamo', $ficha_prestamo);
    }

    private function generar_ficha_prestamo($idPrestamo) {
        $prestamo = $this->Prestamo_model->obtener_prestamo_detallado($idPrestamo);
        
        $data = [
            'titulo' => $prestamo->titulo,
            'fecha_publicacion' => $prestamo->fechaPublicacion,
            'ubicacion' => $prestamo->ubicacionFisica,
            'carnet_lector' => $prestamo->carnet,
            'profesion' => $prestamo->profesion,
            'fecha_prestamo' => $prestamo->fechaPrestamo,
            'prestado_por' => $prestamo->nombres_encargado . ' ' . $prestamo->apellidoPaterno_encargado,
          
        ];
        
        return $data;
    }
public function finalizar($idPrestamo) {
    $this->_verificar_rol(['administrador', 'encargado']);

    $prestamo = $this->Prestamo_model->obtener_prestamo($idPrestamo);
    if (!$prestamo || $prestamo->estadoPrestamo != ESTADO_PRESTAMO_ACTIVO) {
        $this->session->set_flashdata('error', 'El préstamo no es válido para ser finalizado.');
        redirect('prestamos/activos');
    }

    $idEncargado = $this->session->userdata('idUsuario');
    $resultado = $this->Prestamo_model->finalizar_prestamo($idPrestamo, $idEncargado);

    if ($resultado) {
        $this->session->set_flashdata('mensaje', 'Préstamo finalizado con éxito.');
    } else {
        $this->session->set_flashdata('error', 'No se pudo finalizar el préstamo. Por favor, intente de nuevo.');
    }

    redirect('prestamos/activos');
}

    public function activos() {
        $this->_verificar_rol(['administrador', 'encargado']);
        $data['prestamos'] = $this->Prestamo_model->obtener_prestamos_activos();
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('prestamos/activos', $data);
        $this->load->view('inc/footer');
    }

    public function historial() {
        $this->_verificar_rol(['administrador', 'encargado']);
        $data['prestamos'] = $this->Prestamo_model->obtener_historial_prestamos();
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('prestamos/historial', $data);
        $this->load->view('inc/footer');
    }

    public function mis_prestamos() {
        $this->_verificar_rol(['lector']);
        $idUsuario = $this->session->userdata('idUsuario');
        $data['prestamos'] = $this->Prestamo_model->obtener_prestamos_usuario($idUsuario);
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('prestamos/mis_prestamos', $data);
        $this->load->view('inc/footer');
    }
}