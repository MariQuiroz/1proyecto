<?php
defined('BASEPATH') OR exit('No se permite acceso directo al script');

class Usuarios extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('usuario_model');
        $this->load->library('form_validation');
        $this->load->library('email');
        if (ENVIRONMENT !== 'development') {
            force_https();
        }
    }
    private function _obtener_id_usuario_logueado() {
        $idUsuario = $this->session->userdata('idUsuario');
        if (!$idUsuario) {
            // Si no hay un usuario logueado, redirigir al login
            redirect('usuarios/login', 'refresh');
        }
        return $idUsuario;
    }

    private function _verificar_sesion() {
        if (!$this->session->userdata('login')) {
            redirect('usuarios/index/3', 'refresh');
        }
        
        if (!$this->session->userdata('idUsuario') || !$this->session->userdata('rol')) {
            $username = $this->session->userdata('username');
            $usuario = $this->usuario_model->obtener_por_username($username);
            if ($usuario) {
                $this->session->set_userdata('idUsuario', $usuario->idUsuario);
                $this->session->set_userdata('rol', $usuario->rol);
            } else {
                $this->session->sess_destroy();
                redirect('usuarios/index/2', 'refresh');
            }
        }
        
        $rol = $this->session->userdata('rol');
        $metodo_actual = $this->router->fetch_method();
        
        if ($rol == 'administrador' && $metodo_actual == 'lector') {
            redirect('usuarios/mostrar', 'refresh');
        } elseif ($rol == 'lector' && !in_array($metodo_actual, ['lector', 'logout', 'modificarbd'])) {
            redirect('usuarios/lector', 'refresh');
        }
    }

   /* private function _verificar_admin() {
        if (!$this->session->userdata('login') || $this->session->userdata('rol') !== 'administrador') {
            $this->session->set_flashdata('error', 'No tienes permisos para realizar esta acción.');
            redirect('usuarios/panel', 'refresh');
        }
    }*/
    private function _verificar_permisos_creacion() {
        if (!$this->session->userdata('login')) {
            redirect('usuarios/login', 'refresh');
        }
        $rol = $this->session->userdata('rol');
        if ($rol !== 'administrador' && $rol !== 'encargado') {
            $this->session->set_flashdata('error', 'No tienes permisos para realizar esta acción.');
            redirect('usuarios/panel', 'refresh');
        }
    }
    public function index() {
        $data['msg'] = $this->uri->segment(3);
        if ($this->session->userdata('login')) {
            redirect('usuarios/panel','refresh');
        } else {
            $this->load->view('login', $data);
        }
    }

    public function validar() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $usuario = $this->usuario_model->obtener_por_username($username);

        if ($usuario && password_verify($password, $usuario->password)) {
            if ($usuario->estado == ESTADO_USUARIO_ACTIVO) {
                if ($usuario->cambio_password_requerido) {
                    // Redirigir a la página de cambio de contraseña
                    $this->session->set_userdata('id_cambio_password', $usuario->idUsuario);
                    redirect('usuarios/cambiar_password_obligatorio');
                } else {
                    // Iniciar sesión normalmente
                    $this->session->set_userdata('login', TRUE);
                    $this->session->set_userdata('idUsuario', $usuario->idUsuario);
                    $this->session->set_userdata('username', $usuario->username);
                    $this->session->set_userdata('rol', $usuario->rol);
                    redirect('usuarios/panel','refresh');
                }
            } else {
                $this->session->set_flashdata('error', 'Tu cuenta está inactiva. Por favor, contacta al administrador.');
                redirect('usuarios/index/5','refresh');
            }
        } else {
            redirect('usuarios/index/2','refresh');
        }
    }
    public function cambiar_password_obligatorio() {
        $id_usuario = $this->session->userdata('id_cambio_password');
        if (!$id_usuario) {
            redirect('usuarios/index');
        }

        $this->form_validation->set_rules('nueva_password', 'Nueva Contraseña', 'required|min_length[6]');
        $this->form_validation->set_rules('confirmar_password', 'Confirmar Contraseña', 'required|matches[nueva_password]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('usuarios/cambiar_password_obligatorio');
        } else {
            $nueva_password = $this->input->post('nueva_password');

            if ($this->usuario_model->actualizar_password($id_usuario, $nueva_password, false)) {
                $this->session->set_flashdata('mensaje', 'Contraseña actualizada con éxito. Ahora puedes iniciar sesión.');
                $this->session->unset_userdata('id_cambio_password');
                redirect('usuarios/index');
            } else {
                $this->session->set_flashdata('error', 'Error al actualizar la contraseña. Inténtalo de nuevo.');
                redirect('usuarios/cambiar_password_obligatorio');
            }
        }
    }

    public function panel() {
        $this->_verificar_sesion();
        $data = array();
        
        if ($this->session->userdata('rol') == 'administrador') {
            $data['total_usuarios'] = $this->usuario_model->contar_usuarios();
            $data['total_publicaciones'] = $this->publicacion_model->contar_publicaciones();
            $data['prestamos_activos'] = $this->prestamo_model->contar_prestamos_activos();
            $data['prestamos_vencidos'] = $this->prestamo_model->obtener_prestamos_vencidos();
        } else {
            $idUsuario = $this->session->userdata('idUsuario');
            $data['mis_prestamos_activos'] = $this->prestamo_model->contar_prestamos_activos_usuario($idUsuario);
            $data['mis_reservas_pendientes'] = $this->reserva_model->contar_reservas_pendientes_usuario($idUsuario);
            $data['mis_proximas_devoluciones'] = $this->prestamo_model->obtener_proximas_devoluciones_usuario($idUsuario);
        }

        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('inc/menu', $data);
        $this->load->view('inc/footer');
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('usuarios/index/1','refresh');
    }

    public function mostrar() {
        $this->_verificar_sesion();
        if ($this->session->userdata('rol') == 'administrador') { 
            $lista = $this->usuario_model->listaUsuarios();
            $data['usuarios'] = $lista;
            
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('admin/lista', $data);
            $this->load->view('inc/footer');
        } else {
            redirect('usuarios/panel', 'refresh');
        }
    }

    public function registrar() {
        $this->_verificar_permisos_creacion();

        $rol_usuario_actual = $this->session->userdata('rol');

        $this->form_validation->set_rules('nombres', 'Nombres', 'required');
        $this->form_validation->set_rules('apellidoPaterno', 'Apellido Paterno', 'required');
        $this->form_validation->set_rules('carnet', 'Carnet', 'required|is_unique[USUARIO.carnet]');
        $this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email|is_unique[USUARIO.email]');
        $this->form_validation->set_rules('username', 'Nombre de usuario', 'required|is_unique[USUARIO.username]');

        if ($this->form_validation->run() == FALSE) {
            $data['es_admin'] = ($rol_usuario_actual === 'administrador');
            $this->load->view('usuarios/registro_usuario', $data);
        } else {
            $idUsuarioCreador = $this->session->userdata('idUsuario');
            $contrasena_temporal = random_string('alnum', 10);

            $data = array(
                'nombres' => $this->input->post('nombres'),
                'apellidoPaterno' => $this->input->post('apellidoPaterno'),
                'apellidoMaterno' => $this->input->post('apellidoMaterno'),
                'carnet' => $this->input->post('carnet'),
                'profesion' => $this->input->post('profesion'),
                'fechaNacimiento' => $this->input->post('fechaNacimiento'),
                'sexo' => $this->input->post('sexo'),
                'email' => $this->input->post('email'),
                'username' => $this->input->post('username'),
                'password' => password_hash($contrasena_temporal, PASSWORD_DEFAULT),
                'rol' => ($rol_usuario_actual === 'administrador') ? $this->input->post('rol') : 'lector',
                'verificado' => 1,
                'estado' => ESTADO_USUARIO_ACTIVO,
                'cambio_password_requerido' => 1,
                'fechaCreacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $idUsuarioCreador
            );

            if ($id_nuevo_usuario = $this->usuario_model->registrar_usuario($data)) {
                if ($this->_enviar_email_bienvenida($data['email'], $data['username'], $contrasena_temporal)) {
                    $this->session->set_flashdata('mensaje', 'Usuario registrado con éxito. Se ha enviado un correo con la contraseña temporal.');
                } else {
                    $this->session->set_flashdata('error', 'Usuario registrado, pero hubo un problema al enviar el correo. Por favor, contacte al nuevo usuario.');
                }
                redirect('usuarios/mostrar', 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Hubo un problema al registrar el usuario. Inténtalo de nuevo.');
                redirect('usuarios/registrar', 'refresh');
            }
        }
    }
    private function _enviar_email_bienvenida($email, $username, $contrasena_temporal) {
        $config = array(
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

        $this->email->initialize($config);

        $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca');
        $this->email->to($email);
        $this->email->subject('Bienvenido a la Hemeroteca - Información de tu cuenta');
        $mensaje = "Hola $username,<br><br>";
        $mensaje .= "Tu cuenta ha sido creada en el sistema de la Hemeroteca. Tus credenciales de acceso son:<br>";
        $mensaje .= "Usuario: $username<br>";
        $mensaje .= "Contraseña temporal: $contrasena_temporal<br><br>";
        $mensaje .= "Por razones de seguridad, te pedimos que cambies tu contraseña en tu primer inicio de sesión.<br><br>";
        $mensaje .= "Saludos,<br>El equipo de la Hemeroteca";
        $this->email->message($mensaje);

        if (!$this->email->send()) {
            log_message('error', 'Error al enviar correo de bienvenida: ' . $this->email->print_debugger());
            return false;
        } else {
            log_message('info', 'Correo de bienvenida enviado a: ' . $email);
            return true;
        }
    }

    public function auto_registro() {
        $this->form_validation->set_rules('nombres', 'Nombres', 'required');
        $this->form_validation->set_rules('apellidoPaterno', 'Apellido Paterno', 'required');
        $this->form_validation->set_rules('carnet', 'Carnet', 'required|is_unique[USUARIO.carnet]');
        $this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email|is_unique[USUARIO.email]');
        $this->form_validation->set_rules('username', 'Nombre de usuario', 'required|is_unique[USUARIO.username]');
        $this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[6]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('lector/registro');
        } else {
            $this->db->trans_start();

            $data = array(
                'nombres' => $this->input->post('nombres'),
                'apellidoPaterno' => $this->input->post('apellidoPaterno'),
                'apellidoMaterno' => $this->input->post('apellidoMaterno'),
                'carnet' => $this->input->post('carnet'),
                'profesion' => $this->input->post('profesion'),
                'fechaNacimiento' => $this->input->post('fechaNacimiento'),
                'sexo' => $this->input->post('sexo'),
                'email' => $this->input->post('email'),
                'username' => $this->input->post('username'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'rol' => 'lector', // Asignamos el rol de lector por defecto
                'verificado' => 0,
                'tokenVerificacion' => bin2hex(random_bytes(16)),
                'fechaToken' => date('Y-m-d H:i:s'),
                'intentosVerificacion' => 0,
                'estado' => ESTADO_USUARIO_ACTIVO,
                'fechaCreacion' => date('Y-m-d H:i:s')
            );

            $idUsuarioCreado = $this->usuario_model->insertar_usuario($data);

            if ($idUsuarioCreado) {
                $this->usuario_model->actualizar_usuario($idUsuarioCreado, ['idUsuarioCreador' => $idUsuarioCreado]);

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('error', 'Hubo un problema al registrar el usuario. Inténtalo de nuevo.');
                    redirect('usuarios/auto_registro');
                } else {
                    if ($this->_enviar_email_verificacion($data['email'], $data['tokenVerificacion'])) {
                        $this->session->set_flashdata('mensaje', 'Registro exitoso. Por favor, verifica tu correo electrónico.');
                    } else {
                        $this->session->set_flashdata('error', 'Registro exitoso, pero hubo un problema al enviar el correo de verificación. Por favor, contacta al administrador.');
                    }
                    redirect('usuarios/index');
                }
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Hubo un problema al registrar el usuario. Inténtalo de nuevo.');
                redirect('usuarios/auto_registro');
            }
        }
    }

    private function _enviar_email_verificacion($email, $token) {
        $config = array(
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

        $this->email->initialize($config);

        $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca');
        $this->email->to($email);
        $this->email->subject('Verifica tu cuenta');
        $this->email->message('Por favor, haz clic en el siguiente enlace para verificar tu cuenta: ' . site_url('usuarios/verificar/' . $token));

        if (!$this->email->send()) {
            log_message('error', 'Error al enviar correo de verificación: ' . $this->email->print_debugger());
            return false;
        } else {
            log_message('info', 'Correo de verificación enviado a: ' . $email);
            return true;
        }
    }

    public function modificar($idUsuario) {
        $this->_verificar_admin(); // Solo administradores pueden modificar usuarios

        $usuario = $this->usuario_model->obtener_usuario($idUsuario);
        if (!$usuario) {
            $this->session->set_flashdata('error', 'Usuario no encontrado.');
            redirect('usuarios/mostrar', 'refresh');
        }

        $this->form_validation->set_rules('nombres', 'Nombres', 'required');
        $this->form_validation->set_rules('apellidoPaterno', 'Apellido Paterno', 'required');
        $this->form_validation->set_rules('rol', 'Rol', 'required|in_list[administrador,encargado,lector]');

        if ($this->form_validation->run() == FALSE) {
            $data['usuario'] = $usuario;
            $this->load->view('admin/modificar_usuario', $data);
        } else {
            $idUsuarioModificador = $this->session->userdata('idUsuario');
            $datos_actualizados = array(
                'nombres' => $this->input->post('nombres'),
                'apellidoPaterno' => $this->input->post('apellidoPaterno'),
                'apellidoMaterno' => $this->input->post('apellidoMaterno'),
                'rol' => $this->input->post('rol'),
                'fechaActualizacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $idUsuarioModificador // Usamos idUsuarioCreador para registrar quién modificó
            );

            if ($this->usuario_model->actualizar_usuario($idUsuario, $datos_actualizados)) {
                $this->session->set_flashdata('mensaje', 'Usuario actualizado con éxito.');
                redirect('usuarios/mostrar', 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Error al actualizar el usuario.');
                redirect('usuarios/modificar/' . $idUsuario, 'refresh');
            }
        }
    }

    public function verificar($token) {
        if ($this->usuario_model->verificar_cuenta($token)) {
            $this->session->set_flashdata('mensaje', 'Cuenta verificada con éxito. Ya puedes iniciar sesión.');
        } else {
            $this->session->set_flashdata('error', 'Token inválido o expirado.');
        }
        redirect('usuarios/index');
    }

    public function cambiar_password() {
        $this->_verificar_sesion();

        $this->form_validation->set_rules('nueva_password', 'Nueva Contraseña', 'required|min_length[6]');
        $this->form_validation->set_rules('confirmar_password', 'Confirmar Contraseña', 'required|matches[nueva_password]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('usuarios/cambiar_password');
        } else {
            $idUsuario = $this->session->userdata('idUsuario');
            $nueva_password = $this->input->post('nueva_password');

            if ($this->usuario_model->actualizar_password($idUsuario, $nueva_password)) {
                $this->session->set_flashdata('mensaje', 'Contraseña actualizada con éxito.');
                $this->_enviar_email_confirmacion_password($this->session->userdata('email'));
                redirect('usuarios/perfil');
            } else {
                $this->session->set_flashdata('error', 'Error al actualizar la contraseña.');
                redirect('usuarios/cambiar_password');
            }
        }
    }

    private function _enviar_email_confirmacion_password($email) {
        $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca');
        $this->email->to($email);
        $this->email->subject('Confirmación de cambio de contraseña');
        $this->email->message('Tu contraseña ha sido cambiada exitosamente. Si no realizaste este cambio, por favor contacta con soporte inmediatamente.');
        $this->email->send();
    }

    public function desactivar_usuario($idUsuario) {
        $this->_verificar_sesion();
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('usuarios/panel', 'refresh');
        }

        if ($this->usuario_model->cambiar_estado_usuario($idUsuario, ESTADO_USUARIO_INACTIVO)) {
            $this->session->set_flashdata('mensaje', 'Usuario desactivado con éxito.');
        } else {
            $this->session->set_flashdata('error', 'Error al desactivar el usuario.');
        }
        redirect('usuarios/mostrar', 'refresh');
    }

    public function activar_usuario($idUsuario) {
        $this->_verificar_sesion();
        if ($this->session->userdata('rol') != 'administrador') {
            redirect('usuarios/panel', 'refresh');
        }

        if ($this->usuario_model->cambiar_estado_usuario($idUsuario, ESTADO_USUARIO_ACTIVO)) {
            $this->session->set_flashdata('mensaje', 'Usuario activado con éxito.');
        } else {
            $this->session->set_flashdata('error', 'Error al activar el usuario.');
        }
        redirect('usuarios/mostrar', 'refresh');
    }
    public function actualizar_preferencias_notificacion()
    {
        $this->_verificar_sesion();

        $idUsuario = $this->session->userdata('idUsuario');
        
        $preferencias = [
            'email' => $this->input->post('notificar_email') ? true : false,
            'sistema' => $this->input->post('notificar_sistema') ? true : false,
            // Puedes añadir más tipos de notificaciones según sea necesario
        ];

        if ($this->usuario_model->actualizar_preferencias_notificacion($idUsuario, $preferencias)) {
            $this->session->set_flashdata('mensaje', 'Preferencias de notificación actualizadas con éxito.');
        } else {
            $this->session->set_flashdata('error', 'Error al actualizar las preferencias de notificación.');
        }

        redirect('usuarios/perfil');
    }

    public function mostrar_preferencias_notificacion()
    {
        $this->_verificar_sesion();

        $idUsuario = $this->session->userdata('idUsuario');
        $data['preferencias'] = $this->usuario_model->obtener_preferencias_notificacion($idUsuario);

        $this->load->view('usuarios/preferencias_notificacion', $data);
    }
}