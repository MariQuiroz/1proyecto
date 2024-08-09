<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UsuarioController extends CI_Controller {
 

    public function create() {
        if ($_POST) {
            $correo = $this->input->post('correo');
            if (valid_email($correo)) {
                $data = array(
                    'nombre' => $this->input->post('nombre'),
                    'primer_apellido' => $this->input->post('primer_apellido'),
                    'segundo_apellido' => $this->input->post('segundo_apellido'),
                    'ci' => $this->input->post('ci'),
                    'correo' => $correo,
                    'telefono' => $this->input->post('telefono'),
                    'direccion' => $this->input->post('direccion'),
                    'contraseña' => $this->input->post('contraseña'),
                    'rol' => $this->input->post('rol'),
                    'estado' => 1, // Activo por defecto
                    'id_usuario' => $this->session->userdata('usuario_id')
                );

                $this->UsuarioModel->create_usuario($data);
                redirect('usuarios');
            } else {
                // Manejar el error de validación del correo electrónico
                $this->session->set_flashdata('error', 'Correo electrónico no válido');
                redirect('usuarios/create');
            }
        } else {
            $this->load->view('usuarios/create');
        }
    }

    public function edit($id) {
        if ($_POST) {
            $correo = $this->input->post('correo');
            if (valid_email($correo)) {
                $data = array(
                    'nombre' => $this->input->post('nombre'),
                    'primer_apellido' => $this->input->post('primer_apellido'),
                    'segundo_apellido' => $this->input->post('segundo_apellido'),
                    'ci' => $this->input->post('ci'),
                    'correo' => $correo,
                    'telefono' => $this->input->post('telefono'),
                    'direccion' => $this->input->post('direccion'),
                    'rol' => $this->input->post('rol'),
                    'id_usuario' => $this->session->userdata('usuario_id')
                );

                if ($this->input->post('contraseña')) {
                    $data['contraseña'] = $this->input->post('contraseña');
                }

                $this->UsuarioModel->update_usuario($id, $data);
                redirect('usuarios');
            } else {
                // Manejar el error de validación del correo electrónico
                $this->session->set_flashdata('error', 'Correo electrónico no válido');
                redirect('usuarios/edit/'.$id);
            }
        } else {
            $data['usuario'] = $this->UsuarioModel->get_usuario($id);
            $this->load->view('usuarios/edit', $data);
        }
    }

    public function delete($id) {
        $this->UsuarioModel->delete_usuario($id);
        redirect('usuarios');
    }

    public function login() {
        if ($_POST) {
            $correo = $this->input->post('correo');
            $password = $this->input->post('password');
            $usuario = $this->UsuarioModel->authenticate($correo, $password);

            if ($usuario) {
                $this->session->set_userdata('usuario_id', $usuario['id']);
                $this->session->set_userdata('rol', $usuario['rol']);
                redirect('dashboard');
            } else {
                $this->session->set_flashdata('error', 'Credenciales incorrectas');
                redirect('login');
            }
        } else {
            $this->load->view('auth/login');
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }

    public function forgot_password() {
        if ($_POST) {
            $correo = $this->input->post('correo');
            $usuario = $this->UsuarioModel->get_by_correo($correo);

            if ($usuario) {
                // Generar token de recuperación y enviar correo
                // Suponiendo que hay una tabla 'password_resets' con 'correo' y 'token'
                $token = bin2hex(random_bytes(50));
                $this->db->insert('password_resets', array(
                    'correo' => $correo,
                    'token' => $token,
                    'created_at' => date('Y-m-d H:i:s')
                ));

                // Enviar correo con el enlace de recuperación (omitiendo la implementación del correo)
                $reset_link = site_url('usuarios/reset_password/'.$token);
                mail($correo, "Recuperación de Contraseña", "Para resetear su contraseña, haga clic en el siguiente enlace: " . $reset_link);

                $this->session->set_flashdata('message', 'Se ha enviado un enlace de recuperación a su correo electrónico');
                redirect('login');
            } else {
                $this->session->set_flashdata('error', 'Correo electrónico no encontrado');
                redirect('forgot_password');
            }
        } else {
            $this->load->view('auth/forgot_password');
        }
    }

    public function reset_password($token) {
    $query = $this->db->get_where('password_resets', array('token' => $token));
    $reset_entry = $query->row_array();

    if ($reset_entry && (strtotime($reset_entry['created_at']) + 3600) > time()) {
        if ($_POST) {
            $new_password = $this->input->post('new_password');
            $confirm_password = $this->input->post('confirm_password');

            if ($new_password === $confirm_password) {
                $usuario = $this->UsuarioModel->get_by_correo($reset_entry['correo']);
                $this->UsuarioModel->update_password($usuario['id'], $new_password);

                // Eliminar el token de restablecimiento
                $this->db->delete('password_resets', array('token' => $token));

                $this->session->set_flashdata('message', 'Su contraseña ha sido restablecida');
                redirect('login');
            } else {
                $this->session->set_flashdata('error', 'Las contraseñas no coinciden');
                redirect('usuarios/reset_password/'.$token);
            }
        } else {
            $data['token'] = $token;
            $this->load->view('auth/reset_password', $data);
        }
    } else {
        $this->session->set_flashdata('error', 'El enlace de restablecimiento es inválido o ha expirado');
        redirect('forgot_password');
    }
}
}