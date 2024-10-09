<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitudes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Solicitud_model');
        $this->load->model('Prestamo_model');  // Añade esta línea
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
    
        /*public function crear($idPublicacion) {
            $this->_verificar_rol(['lector']);
        
            $publicacion = $this->Publicacion_model->obtener_publicacion($idPublicacion);
        
            if (!$publicacion) {
                $this->session->set_flashdata('error', 'La publicación seleccionada no existe.');
                redirect('publicaciones/index');
            }
        
            if ($publicacion->estado != ESTADO_PUBLICACION_DISPONIBLE) {
                $this->session->set_flashdata('error', 'La publicación seleccionada no está disponible para préstamo.');
                redirect('publicaciones/index');
            }
        
            if ($this->input->post()) {
                $idUsuario = $this->session->userdata('idUsuario');
                $resultado = $this->Solicitud_model->crear_solicitud($idUsuario, $idPublicacion);
        
                if ($resultado) {
                    $this->session->set_flashdata('mensaje', 'Solicitud creada con éxito.');
                    redirect('solicitudes/mis_solicitudes');
                } else {
                    $this->session->set_flashdata('error', 'Error al crear la solicitud.');
                }
            }
        
            $data['publicacion'] = $publicacion;
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/crear', $data);
            $this->load->view('inc/footer');
        }
    
    
        public function aprobar($idSolicitud) {
            $this->_verificar_rol(['administrador', 'encargado']);
        
            $idEncargado = $this->session->userdata('idUsuario');
            $fechaPrestamo = date('Y-m-d H:i:s'); // Podrías permitir que el encargado especifique esta fecha
        
            $resultado = $this->Solicitud_model->aprobar_solicitud($idSolicitud, $idEncargado, $fechaPrestamo);
        
            if ($resultado) {
                $this->session->set_flashdata('mensaje', 'Solicitud aprobada y préstamo registrado con éxito.');
            } else {
                $this->session->set_flashdata('error', 'Error al aprobar la solicitud.');
            }
        
            redirect('solicitudes/pendientes');
        }
        public function rechazar($idSolicitud) {
            $this->_verificar_rol(['administrador', 'encargado']);
        
            $idEncargado = $this->session->userdata('idUsuario');
        
            $resultado = $this->Solicitud_model->rechazar_solicitud($idSolicitud, $idEncargado);
        
            if ($resultado) {
                $this->session->set_flashdata('mensaje', 'Solicitud rechazada con éxito.');
            } else {
                $this->session->set_flashdata('error', 'Error al rechazar la solicitud.');
            }
        
            redirect('solicitudes/pendientes');
        }*/
    
        public function pendientes() {
            $this->_verificar_rol(['administrador', 'encargado']);
    
            $data['solicitudes'] = $this->Solicitud_model->obtener_solicitudes_pendientes();
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/pendientes', $data);
            $this->load->view('inc/footer');
        }
    
        public function eliminar($idSolicitud) {
            $this->_verificar_rol(['administrador', 'encargado']);
    
            $idUsuario = $this->session->userdata('idUsuario');
            $resultado = $this->Solicitud_model->eliminar_solicitud($idSolicitud, $idUsuario);
    
            if ($resultado) {
                $this->session->set_flashdata('mensaje', 'Solicitud eliminada con éxito.');
            } else {
                $this->session->set_flashdata('error', 'Error al eliminar la solicitud.');
            }
    
            redirect('solicitudes/pendientes');
        }
        public function mis_solicitudes() {
            $this->_verificar_rol(['lector']);
            
            $idUsuario = $this->session->userdata('idUsuario');
            $data['solicitudes'] = $this->Solicitud_model->obtener_solicitudes_usuario($idUsuario);
            
            // No es necesario pasar las constantes a la vista si están definidas globalmente
            
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/mis_solicitudes', $data);
            $this->load->view('inc/footer');
        }
    
        public function confirmar($idPublicacion) {
            $this->_verificar_rol(['lector']);
            
            $idUsuario = $this->session->userdata('idUsuario');
            $publicacion = $this->Publicacion_model->obtener_publicacion($idPublicacion);
            
            if (!$publicacion) {
                $this->session->set_flashdata('error', 'La publicación seleccionada no existe.');
                redirect('publicaciones/index');
            }
            
            if ($publicacion->estado != ESTADO_PUBLICACION_DISPONIBLE) {
                $this->session->set_flashdata('error', 'La publicación seleccionada no está disponible para préstamo.');
                redirect('publicaciones/index');
            }
            
            $resultado = $this->Solicitud_model->crear_solicitud($idUsuario, $idPublicacion);
            
            if ($resultado) {
                $this->session->set_flashdata('mensaje', 'Solicitud creada con éxito.');
                redirect('solicitudes/mis_solicitudes');
            } else {
                $this->session->set_flashdata('error', 'Error al crear la solicitud.');
                redirect('publicaciones/index');
            }
        }
        public function detalle($idSolicitud) {
            $this->_verificar_sesion();
            
            $solicitud = $this->Solicitud_model->obtener_detalle_solicitud($idSolicitud);
            
            if (!$solicitud) {
                $this->session->set_flashdata('error', 'La solicitud no existe.');
                redirect('solicitudes/mis_solicitudes');
            }
            
            // Verificar si el usuario actual tiene permiso para ver esta solicitud
            if ($this->session->userdata('rol') == 'lector' && $solicitud->idUsuario != $this->session->userdata('idUsuario')) {
                $this->session->set_flashdata('error', 'No tienes permiso para ver esta solicitud.');
                redirect('solicitudes/mis_solicitudes');
            }
            
            $data['solicitud'] = $solicitud;
            
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/detalle', $data);
            $this->load->view('inc/footer');
        }
        public function crear($idPublicacion) {
            $this->_verificar_rol(['lector']);
    
            $publicacion = $this->Publicacion_model->obtener_publicacion($idPublicacion);
    
            if (!$publicacion || $publicacion->estado != ESTADO_PUBLICACION_DISPONIBLE) {
                $this->session->set_flashdata('error', 'La publicación no está disponible para préstamo.');
                redirect('publicaciones/index');
            }
    
            if ($this->input->post()) {
                $this->db->trans_start();
    
                $idUsuario = $this->session->userdata('idUsuario');
                $resultado = $this->Solicitud_model->crear_solicitud($idUsuario, $idPublicacion);
    
                if ($resultado) {
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->session->set_flashdata('error', 'Error al crear la solicitud. Por favor, intente de nuevo.');
                    } else {
                        $this->session->set_flashdata('mensaje', 'Solicitud creada con éxito.');
                        redirect('solicitudes/mis_solicitudes');
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('error', 'Error al crear la solicitud.');
                }
            }
    
            $data['publicacion'] = $publicacion;
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/crear', $data);
            $this->load->view('inc/footer');
        }
    
        public function aprobar($idSolicitud) {
            $this->_verificar_rol(['administrador', 'encargado']);
        
            $this->db->trans_start();
        
            $idEncargado = $this->session->userdata('idUsuario');
            $resultado = $this->Solicitud_model->aprobar_solicitud($idSolicitud, $idEncargado);
        
            if ($resultado) {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('error', 'Error al aprobar la solicitud. Por favor, intente de nuevo.');
                    redirect('solicitudes/pendientes');
                } else {
                    $this->session->set_flashdata('mensaje', 'Solicitud aprobada y préstamo registrado con éxito.');
                    
                    // Obtener los datos para la ficha de préstamo
                    $ficha_prestamo = $this->Prestamo_model->obtener_datos_ficha_prestamo($resultado);
                    
                    if (!is_array($ficha_prestamo)) {
                        $ficha_prestamo = array();
                    }
                    
                    // Agregar una variable para indicar que la ficha ha sido mostrada
                    $ficha_prestamo['ficha_mostrada'] = true;
                    
                    // Cargar la vista de la ficha de préstamo
                    $this->load->view('prestamos/ficha_prestamo', $ficha_prestamo);
                    return;
                }
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error al aprobar la solicitud.');
                redirect('solicitudes/pendientes');
            }
        }

        public function rechazar($idSolicitud) {
            $this->_verificar_rol(['administrador', 'encargado']);
    
            $this->db->trans_start();
    
            $idEncargado = $this->session->userdata('idUsuario');
            $resultado = $this->Solicitud_model->rechazar_solicitud($idSolicitud, $idEncargado);
    
            if ($resultado) {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('error', 'Error al rechazar la solicitud. Por favor, intente de nuevo.');
                } else {
                    $this->session->set_flashdata('mensaje', 'Solicitud rechazada con éxito.');
                }
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Error al rechazar la solicitud.');
            }
    
            redirect('solicitudes/pendientes');
        }
        public function aprobadas() {
            $this->_verificar_rol(['administrador', 'encargado']);
            $data['solicitudes'] = $this->Solicitud_model->obtener_solicitudes_por_estado(ESTADO_SOLICITUD_APROBADA);
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/aprobadas', $data);
            $this->load->view('inc/footer');
        }
        
        public function rechazadas() {
            $this->_verificar_rol(['administrador', 'encargado']);
            $data['solicitudes'] = $this->Solicitud_model->obtener_solicitudes_por_estado(ESTADO_SOLICITUD_RECHAZADA);
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/rechazadas', $data);
            $this->load->view('inc/footer');
        }
        
        public function historial() {
            $this->_verificar_rol(['administrador', 'encargado']);
            $data['solicitudes'] = $this->Solicitud_model->obtener_historial_solicitudes();
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('solicitudes/historial', $data);
            $this->load->view('inc/footer');
        }
}