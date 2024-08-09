<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class hemeroteca extends CI_Controller {
	public function mostrar(){

		$this->load->view('inc/vistaslte/head');
		$this->load->view('inc/vistaslte/navar');
		$this->load->view('inc/vistaslte/aside');
        $this->load->view('inc/vistaslte/menu');
		$this->load->view('inc/vistaslte/test');
		$this->load->view('inc/vistaslte/footer');
	}

    public function login() {
        $this->load->view('inc/vistaslte/head');
     
        if ($_POST) {
            $recaptchaResponse = $this->input->post('g-recaptcha-response');
            $secretKey = '6LcWryIqAAAAABAvzgFfLsGUHTxAE4plYH9be54S';
            $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';

            $response = file_get_contents($recaptchaUrl . '?secret=' . $secretKey . '&response=' . $recaptchaResponse);
            $responseKeys = json_decode($response, true);

            if (intval($responseKeys["success"]) !== 1) {
                $this->session->set_flashdata('error', 'Por favor, complete el reCAPTCHA');
                redirect('hemeroteca/login');
            } else {
                $correo = $this->input->post('correo');
                $password = $this->input->post('password');
                $usuario = $this->Usuario_model->authenticate($correo, $password);

                if ($usuario) {
                    $this->session->set_userdata('usuario_id', $usuario['id']);
                    $this->session->set_userdata('rol', $usuario['rol']);
                    redirect('dashboard');
                } else {
                    $this->session->set_flashdata('error', 'Credenciales incorrectas');
                    redirect('hemeroteca/login');
                }
            }
        } else {
            $this->load->view('inc/vistaslte/iniciar_sesion');  // Asegúrate de que la ruta sea correcta
        }
    
		$this->load->view('inc/vistaslte/footer');
    }

    public function create_usuario() {
        $this->load->view('inc/vistaslte/head');
        if ($_POST) {
            $correo = $this->input->post('correo');
            if (valid_email($correo)) {
                $recaptchaResponse = $this->input->post('g-recaptcha-response');
                $secretKey = '6LcWryIqAAAAABAvzgFfLsGUHTxAE4plYH9be54S';
                $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
    
                $response = file_get_contents($recaptchaUrl . '?secret=' . $secretKey . '&response=' . $recaptchaResponse);
                $responseKeys = json_decode($response, true);
    
                if (intval($responseKeys["success"]) !== 1) {
                    $this->session->set_flashdata('error', 'Por favor, complete el reCAPTCHA');
                    redirect('hemeroteca/create_usuario');
                } else {
                    $password = $this->input->post('contraseña');
                    $repeat_password = $this->input->post('repetir_contraseña');
    
                    if ($password !== $repeat_password) {
                        $this->session->set_flashdata('error', 'Las contraseñas no coinciden');
                        redirect('hemeroteca/create_usuario');
                    } else {
                        $data = array(
                            'nombre' => $this->input->post('nombre'),
                            'primer_apellido' => $this->input->post('primer_apellido'),
                            'segundo_apellido' => $this->input->post('segundo_apellido'),
                            'ci' => $this->input->post('ci'),
                            'correo' => $correo,
                            'telefono' => $this->input->post('telefono'),
                            'direccion' => $this->input->post('direccion'),
                            'contraseña' => sha1($password), // Encriptación SHA-1
                            'rol' => $this->input->post('rol'), // Rol seleccionado por el usuario
                            'estado' => 1, // Activo por defecto
                            'id_usuario' => $this->input->post('rol') // ID de usuario se asocia al rol
                        );
    
                        $this->Usuario_model->create_usuario($data);
                        $this->session->set_flashdata('message', 'Usuario creado exitosamente');
                        redirect('hemeroteca/login');
                    }
                }
            } else {
                $this->session->set_flashdata('error', 'Correo electrónico no válido');
                redirect('hemeroteca/login');
            }
        } else {
            $this->load->view('inc/vistaslte/crear_usuario');
        }
        $this->load->view('inc/vistaslte/footer');
    }

    public function forgot_password() {
        $this->load->view('inc/vistaslte/head');
        if ($_POST) {
            $correo = $this->input->post('correo');
            $usuario = $this->Usuario_model->get_by_correo($correo);

            if ($usuario) {
                $token = bin2hex(random_bytes(50));
                $this->db->insert('password_resets', array(
                    'correo' => $correo,
                    'token' => $token,
                    'created_at' => date('Y-m-d H:i:s')
                ));

                $reset_link = site_url('hemeroteca/forgot_password/'.$token);
                mail($correo, "Recuperación de Contraseña", "Para restablecer su contraseña, haga clic en el siguiente enlace: " . $reset_link);

                $this->session->set_flashdata('message', 'Se ha enviado un enlace de recuperación a su correo electrónico');
                redirect('hemeroteca/login');
            } else {
                $this->session->set_flashdata('error', 'Correo electrónico no encontrado');
                redirect('hemeroteca/forgot_password');
            }
        } else {
            $this->load->view('inc/vistaslte/restablecer_password');
        }
        $this->load->view('inc/vistaslte/footer');
    }

    public function reset_password($token) {
        $query = $this->db->get_where('password_resets', array('token' => $token));
        $reset_entry = $query->row_array();

        if ($reset_entry && (strtotime($reset_entry['created_at']) + 3600) > time()) {
            if ($_POST) {
                $new_password = $this->input->post('new_password');
                $confirm_password = $this->input->post('confirm_password');

                if ($new_password === $confirm_password) {
                    $usuario = $this->Usuario_model->get_by_correo($reset_entry['correo']);
                    $this->Usuario_model->update_password($usuario['id'], $new_password);

                    // Eliminar el token de restablecimiento
                    $this->db->delete('password_resets', array('token' => $token));

                    $this->session->set_flashdata('message', 'Su contraseña ha sido restablecida');
                    redirect('hemeroteca/login');
                } else {
                    $this->session->set_flashdata('error', 'Las contraseñas no coinciden');
                    redirect('hemeroteca/reset_password/'.$token);
                }
            } else {
                $data['token'] = $token;
                $this->load->view('inc/vistaslte/reset_password', $data);
            }
        } else {
            $this->session->set_flashdata('error', 'El enlace de restablecimiento es inválido o ha expirado');
            redirect('hemeroteca/forgot_password');
        }
    }
}

