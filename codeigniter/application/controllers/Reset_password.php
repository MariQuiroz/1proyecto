<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reset_password extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('usuario_model');
        $this->load->library('email');
        $this->load->helper('string');
    }

    public function index() {
        $this->load->view('reset_password/solicitar');
    }

    public function solicitar() {
        $email = $this->input->post('email');
        $usuario = $this->usuario_model->verificar_email($email);

        if ($usuario) {
            $token = random_string('alnum', 20);
            $this->usuario_model->guardar_token_reset($usuario->idUsuario, $token);

            $this->enviar_email_reset($email, $token);

            $this->session->set_flashdata('success', 'Se ha enviado un correo con instrucciones para restablecer tu contraseña.');
            redirect('reset_password/index');
        } else {
            $this->session->set_flashdata('error', 'No se encontró ninguna cuenta con ese correo electrónico.');
            redirect('reset_password/index');
        }
    }

    private function enviar_email_reset($email, $token) {
        $this->email->from('noreply@tuhemeroteca.com', 'Tu Hemeroteca');
        $this->email->to($email);
        $this->email->subject('Restablecimiento de Contraseña');
        $this->email->message('Haz clic en el siguiente enlace para restablecer tu contraseña: ' . 
                              site_url('reset_password/reset/' . $token));
        $this->email->send();
    }

    public function reset($token) {
        $usuario = $this->usuario_model->verificar_token_reset($token);

        if ($usuario) {
            $this->load->view('reset_password/nueva_password', ['token' => $token]);
        } else {
            $this->session->set_flashdata('error', 'El enlace de restablecimiento no es válido o ha expirado.');
            redirect('reset_password/index');
        }
    }

    public function actualizar() {
        $token = $this->input->post('token');
        $nueva_password = $this->input->post('nueva_password');
        $confirmar_password = $this->input->post('confirmar_password');

        $usuario = $this->usuario_model->verificar_token_reset($token);

        if ($usuario && $nueva_password === $confirmar_password) {
            $this->usuario_model->actualizar_password($usuario->idUsuario, $nueva_password);
            $this->session->set_flashdata('success', 'Tu contraseña ha sido actualizada con éxito.');
            redirect('usuarios/login');
        } else {
            $this->session->set_flashdata('error', 'Hubo un error al actualizar tu contraseña. Por favor, intenta de nuevo.');
            redirect('reset_password/reset/' . $token);
        }
    }
}