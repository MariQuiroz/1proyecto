<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prestamos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('prestamo_model');
        $this->load->model('reserva_model');
        $this->load->model('publicacion_model');
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
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('prestamos/mis_prestamos', 'refresh');
        }
        $data['prestamos'] = $this->prestamo_model->listar_prestamos();
        $this->load->view('inc/header');
        $this->load->view('prestamos/lista', $data);
        $this->load->view('inc/footer');
    }

    /*public function agregar($idReserva = NULL) {
        $this->_verificar_sesion();
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('prestamos/index', 'refresh');
        }
        if ($idReserva) {
            $data['reserva'] = $this->reserva_model->obtener_reserva($idReserva);
            $data['publicacion'] = $this->publicacion_model->obtener_publicacion($data['reserva']->idPublicacion);
        } else {
            $data['publicaciones'] = $this->publicacion_model->listar_publicaciones();
        }
        $this->load->view('inc/header');
        $this->load->view('prestamos/agregar', $data);
        $this->load->view('inc/footer');
    }*/
    public function agregar() {
        $this->_verificar_sesion();

        if ($this->session->userdata('rol') != 'administrador') {
            redirect('prestamos/index', 'refresh');
        }

        $data['usuarios'] = $this->usuario_model->obtener_usuarios_activos();
        $data['publicaciones'] = $this->publicacion_model->obtener_publicaciones_disponibles();

        $this->load->view('inc/header');
        $this->load->view('prestamos/agregar', $data);
        $this->load->view('inc/footer');
    }

    public function agregarbd() {
        $this->_verificar_sesion();
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('prestamos/index', 'refresh');
        }
        $data = array(
            'idUsuario' => $this->input->post('idUsuario'),
            'idPublicacion' => $this->input->post('idPublicacion'),
            'fechaPrestamo' => date('Y-m-d H:i:s'),
            'fechaDevolucionEsperada' => $this->input->post('fechaDevolucionEsperada'),
            'usuarioSesion' => $this->session->userdata('idUsuario')
        );
        $idReserva = $this->input->post('idReserva');
        if ($idReserva) {
            $data['idReserva'] = $idReserva;
            $this->reserva_model->actualizar_reserva($idReserva, array('estado' => 2)); // 2 = Finalizada
        }
        $this->prestamo_model->agregar_prestamo($data);
        redirect('prestamos/index', 'refresh');
    }

    public function devolver($idPrestamo) {
        $this->_verificar_sesion();
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('prestamos/index', 'refresh');
        }
        if ($this->prestamo_model->registrar_devolucion($idPrestamo)) {
            $this->session->set_flashdata('success', 'Devolución registrada con éxito.');
        } else {
            $this->session->set_flashdata('error', 'No se pudo registrar la devolución.');
        }
        redirect('prestamos/index', 'refresh');
    }

    public function mis_prestamos() {
        $this->_verificar_sesion();
        $idUsuario = $this->session->userdata('idUsuario');
        $data['prestamos'] = $this->prestamo_model->prestamos_por_usuario($idUsuario);
        $this->load->view('inc/header');
        $this->load->view('prestamos/mis_prestamos', $data);
        $this->load->view('inc/footer');
    }

    public function vencidos() {
        $this->_verificar_sesion();
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('prestamos/index', 'refresh');
        }
        $data['prestamos'] = $this->prestamo_model->prestamos_vencidos();
        $this->load->view('inc/header');
        $this->load->view('prestamos/vencidos', $data);
        $this->load->view('inc/footer');
    }

    public function crear_desde_reserva($idReserva) {
        $this->_verificar_sesion();

        if ($this->session->userdata('rol') != 'administrador') {
            redirect('usuarios/panel', 'refresh');
        }

        $fechaDevolucionEsperada = $this->input->post('fechaDevolucionEsperada');

        if ($this->prestamo_model->crear_prestamo_desde_reserva($idReserva, $fechaDevolucionEsperada)) {
            $this->session->set_flashdata('success', 'Préstamo creado exitosamente.');
        } else {
            $this->session->set_flashdata('error', 'Hubo un error al crear el préstamo.');
        }

        redirect('prestamos/index', 'refresh');
    }

    /*public function confirmar($idReserva) {
        $this->_verificar_sesion();
    
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('usuarios/panel', 'refresh');
        }
    
        $data['reserva'] = $this->reserva_model->obtener_reserva($idReserva);
        
        if (!$data['reserva']) {
            $this->session->set_flashdata('error', 'Reserva no encontrada.');
            redirect('reservas/index', 'refresh');
        }
    
        $this->load->view('inc/header');
        $this->load->view('prestamos/confirmar_prestamo', $data);
        $this->load->view('inc/footer');
    }
    */
    public function confirmar($idReserva = null) {
        $this->_verificar_sesion();

        if ($this->session->userdata('rol') != 'administrador') {
            redirect('usuarios/panel', 'refresh');
        }

        if ($idReserva === null) {
            // Si no se proporciona un idReserva, mostramos una lista de reservas pendientes
            $data['reservas_pendientes'] = $this->reserva_model->obtener_reservas_pendientes();
            $this->load->view('inc/header');
            $this->load->view('prestamos/seleccionar_reserva', $data);
            $this->load->view('inc/footer');
        } else {
            $data['reserva'] = $this->reserva_model->obtener_reserva($idReserva);
            
            if (!$data['reserva']) {
                $this->session->set_flashdata('error', 'Reserva no encontrada.');
                redirect('prestamos/index', 'refresh');
            }

            $data['publicacion'] = $this->publicacion_model->obtener_publicacion($data['reserva']->idPublicacion);

            $this->load->view('inc/header');
            $this->load->view('prestamos/confirmar_prestamo', $data);
            $this->load->view('inc/footer');
        }
    }
    //prestamo para el  administrador
    public function buscar_reservas() {
        $this->_verificar_sesion_admin();

        if ($this->input->post('carnet')) {
            $carnet = $this->input->post('carnet');
            $usuario = $this->usuario_model->obtener_por_carnet($carnet);

            if ($usuario) {
                $data['reservas'] = $this->reserva_model->obtener_reservas_usuario($usuario->idUsuario);
                $data['usuario'] = $usuario;
            } else {
                $this->session->set_flashdata('error', 'Usuario no encontrado.');
            }
        }

        $this->load->view('inc/header');
        $this->load->view('prestamos/buscar_reservas', $data ?? null);
        $this->load->view('inc/footer');
    }

    public function confirmar_prestamo($idReserva) {
        $this->_verificar_sesion_admin();

        $reserva = $this->reserva_model->obtener_reserva($idReserva);

        if (!$reserva) {
            $this->session->set_flashdata('error', 'Reserva no encontrada.');
            redirect('prestamos/buscar_reservas', 'refresh');
        }

        if ($this->input->post()) {
            $fechaDevolucion = $this->input->post('fechaDevolucion');
            $resultado = $this->prestamo_model->crear_prestamo($reserva->idUsuario, $reserva->idPublicacion, $fechaDevolucion);

            if ($resultado) {
                $this->reserva_model->actualizar_estado_reserva($idReserva, 'completada');
                $this->publicacion_model->actualizar_estado_publicacion($reserva->idPublicacion, 'prestado');
                $this->session->set_flashdata('success', 'Préstamo registrado con éxito.');
                redirect('prestamos/buscar_reservas', 'refresh');
            } else {
                $this->session->set_flashdata('error', 'No se pudo registrar el préstamo.');
            }
        }

        $data['reserva'] = $reserva;
        $this->load->view('inc/header');
        $this->load->view('prestamos/confirmar_prestamo', $data);
        $this->load->view('inc/footer');
    }

}