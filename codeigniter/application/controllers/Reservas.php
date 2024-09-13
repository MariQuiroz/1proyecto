<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reservas extends CI_Controller {

   

    private function _verificar_sesion() {
        if (!$this->session->userdata('login')) {
            redirect('usuarios/index/3', 'refresh');
        }
    }

    public function index() {
        $this->_verificar_sesion();
        $data['reservas'] = $this->reserva_model->listar_reservas();
        $this->load->view('inc/header');
        $this->load->view('reservas/lista', $data);
        $this->load->view('inc/footer');
    }

    public function agregar($idPublicacion) {
        $this->_verificar_sesion();
        $data['publicacion'] = $this->publicacion_model->obtener_publicacion($idPublicacion);
        $this->load->view('inc/header');
        $this->load->view('reservas/agregar', $data);
        $this->load->view('inc/footer');
    }

    public function agregarbd() {
        $this->_verificar_sesion();
        $idPublicacion = $this->input->post('idPublicacion');
        $fechaReserva = $this->input->post('fechaReserva');
        
        if ($this->reserva_model->verificar_disponibilidad($idPublicacion, $fechaReserva)) {
            $data = array(
                'idUsuario' => $this->session->userdata('idUsuario'),
                'idPublicacion' => $idPublicacion,
                'fechaReserva' => $fechaReserva,
                'usuarioSesion' => $this->session->userdata('idUsuario')
            );
            $this->reserva_model->agregar_reserva($data);
            redirect('reservas/index', 'refresh');
        } else {
            $this->session->set_flashdata('error', 'La publicación no está disponible para la fecha seleccionada.');
            redirect('reservas/agregar/'.$idPublicacion, 'refresh');
        }
    }

    /*public function cancelar($idReserva) {
        $this->_verificar_sesion();
        $reserva = $this->reserva_model->obtener_reserva($idReserva);
        if ($reserva->idUsuario == $this->session->userdata('idUsuario') || $this->session->userdata('rol') == 'administrador') {
            $data = array('estado' => 3); // 3 = Cancelada
            $this->reserva_model->actualizar_reserva($idReserva, $data);
            redirect('reservas/index', 'refresh');
        } else {
            $this->session->set_flashdata('error', 'No tienes permiso para cancelar esta reserva.');
            redirect('reservas/index', 'refresh');
        }
    }*/
    public function cancelar() {
        $this->_verificar_sesion();

        $idReserva = $this->input->post('idReserva');
        $reserva = $this->reserva_model->obtener_reserva($idReserva);

        if (!$reserva) {
            $this->session->set_flashdata('error', 'Reserva no encontrada.');
            redirect('reservas/index', 'refresh');
        }

        // Verificar si el usuario actual es el dueño de la reserva o un administrador
        if ($this->session->userdata('idUsuario') != $reserva->idUsuario && $this->session->userdata('rol') != 'administrador') {
            $this->session->set_flashdata('error', 'No tienes permisos para cancelar esta reserva.');
            redirect('reservas/index', 'refresh');
        }

        if ($this->reserva_model->cancelar_reserva($idReserva)) {
            $this->session->set_flashdata('success', 'Reserva cancelada exitosamente.');
        } else {
            $this->session->set_flashdata('error', 'Hubo un error al cancelar la reserva.');
        }

        redirect('reservas/index', 'refresh');
    }


    public function mis_reservas() {
        $this->_verificar_sesion();

        $idUsuario = $this->session->userdata('idUsuario');
        $data['reservas'] = $this->reserva_model->obtener_reservas_usuario($idUsuario);

        $this->load->view('inc/header');
        $this->load->view('reservas/mis_reservas', $data);
        $this->load->view('inc/footer');
    }
   //reservas para lectores
    public function realizar_reserva($idPublicacion) {
        $this->_verificar_sesion();

        if ($this->session->userdata('rol') != 'lector') {
            redirect('usuarios/panel', 'refresh');
        }

        $data['publicacion'] = $this->publicacion_model->obtener_publicacion($idPublicacion);

        if (!$data['publicacion']) {
            $this->session->set_flashdata('error', 'Publicación no encontrada.');
            redirect('publicaciones/index', 'refresh');
        }

        if ($this->input->post()) {
            $fechaReserva = $this->input->post('fechaReserva');
            $idUsuario = $this->session->userdata('idUsuario');

            $resultado = $this->reserva_model->crear_reserva($idUsuario, $idPublicacion, $fechaReserva);

            if ($resultado) {
                $this->session->set_flashdata('success', 'Reserva realizada con éxito.');
                redirect('reservas/mis_reservas', 'refresh');
            } else {
                $this->session->set_flashdata('error', 'No se pudo realizar la reserva.');
            }
        }

        $this->load->view('inc/header');
        $this->load->view('reservas/realizar_reserva', $data);
        $this->load->view('inc/footer');
    }
}