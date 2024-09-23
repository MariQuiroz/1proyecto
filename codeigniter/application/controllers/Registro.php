<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registro extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (ENVIRONMENT !== 'development') {
            force_https();
        }
    }

    public function index() {
        $this->load->view('registro/formulario');
    }

    /*public function procesar() {
        $email = $this->input->post('email');
        
        if ($this->usuario_model->email_existe($email)) {
            $this->session->set_flashdata('error', 'El correo electrónico ya está registrado.');
            redirect('registro');
        }

        $datos_usuario = [
            'nombre' => $this->input->post('nombre'),
            'email' => $email,
            'password' => random_string('alnum', 8) // Contraseña temporal
        ];

        if ($this->usuario_model->registrar_usuario($datos_usuario)) {
            $this->enviar_email_verificacion($email, $datos_usuario['token_verificacion']);
            $this->session->set_flashdata('success', 'Registro exitoso. Por favor, verifica tu correo electrónico.');
            redirect('login');
        } else {
            $this->session->set_flashdata('error', 'Hubo un error en el registro.');
            redirect('registro');
        }
    }

    private function enviar_email_verificacion($email, $token) {
        $this->email->from('noreply@tuhemeroteca.com', 'Tu Hemeroteca');
        $this->email->to($email);
        $this->email->subject('Verifica tu cuenta');
        $this->email->message('Haz clic en el siguiente enlace para verificar tu cuenta y establecer tu contraseña: ' . 
                              site_url('registro/verificar/' . $token));
        $this->email->send();
    }

    public function verificar($token) {
        if ($this->usuario_model->verificar_cuenta($token)) {
            $this->session->set_flashdata('success', 'Tu cuenta ha sido verificada. Ahora puedes establecer tu contraseña.');
            redirect('usuario/cambiar_password');
        } else {
            $this->session->set_flashdata('error', 'Token de verificación inválido o expirado.');
            redirect('login');
        }
    }*/
    public function procesar() {
        // Validación del formulario
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[USUARIO.email]');
        $this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[6]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('registro/formulario');
        } else {
            $datos = array(
                'email' => $this->input->post('email'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'token_verificacion' => bin2hex(random_bytes(16))
            );

            if ($this->usuario_model->registrar($datos)) {
                $this->enviar_email_verificacion($datos['email'], $datos['token_verificacion']);
                $this->session->set_flashdata('mensaje', 'Registro exitoso. Por favor, verifica tu email.');
                redirect('login');
            } else {
                $this->session->set_flashdata('error', 'Error en el registro. Inténtalo de nuevo.');
                redirect('registro');
            }
        }
    }

    private function enviar_email_verificacion($email, $token) {
        $this->email->from('tu_correo@gmail.com', 'Nombre de tu Aplicación');
        $this->email->to($email);
        $this->email->subject('Verifica tu cuenta');
        $this->email->message('Haz clic en este enlace para verificar tu cuenta: ' . 
                              site_url('registro/verificar/' . $token));
        $this->email->send();
    }

    public function verificar($token) {
        if ($this->usuario_model->verificar_cuenta($token)) {
            $this->session->set_flashdata('mensaje', 'Cuenta verificada con éxito. Ya puedes iniciar sesión.');
        } else {
            $this->session->set_flashdata('error', 'Token inválido o expirado.');
        }
        redirect('login');
    }
}