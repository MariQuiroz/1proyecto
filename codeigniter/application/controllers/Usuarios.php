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
        } elseif ($rol == 'lector' && !in_array($metodo_actual, ['lector', 'logout', 'perfil', 'configuracion'])) {
            redirect('usuarios/lector', 'refresh');
        }
    }

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
            if ($usuario->estado == 1) {
                if ($usuario->cambioPasswordRequerido) {
                    $this->session->set_userdata('id_cambio_password', $usuario->idUsuario);
                    redirect('usuarios/cambiar_password_obligatorio');
                } else {
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

    public function logout() {
        $this->session->sess_destroy();
        redirect('usuarios/index/1','refresh');
    }

    public function panel() {
        $this->_verificar_sesion();
        $data = array();
        $rol = $this->session->userdata('rol');
        
        switch ($rol) {
            case 'administrador':
                $data['total_usuarios'] = $this->usuario_model->contar_usuarios();
                $data['total_publicaciones'] = $this->publicacion_model->contar_publicaciones();
                $data['prestamos_activos'] = $this->prestamo_model->contar_prestamos_activos();
                $data['solicitudes_pendientes'] = $this->solicitud_model->contar_solicitudes_pendientes();
                $data['prestamos_no_devueltos'] = $this->prestamo_model->contar_prestamos_no_devueltos();
                break;
            
            case 'encargado':
                $data['total_publicaciones'] = $this->publicacion_model->contar_publicaciones();
                $data['prestamos_activos'] = $this->prestamo_model->contar_prestamos_activos();
                $data['solicitudes_pendientes'] = $this->solicitud_model->contar_solicitudes_pendientes();
                break;
            
            case 'lector':
                $idUsuario = $this->session->userdata('idUsuario');
                $data['mis_prestamos_activos'] = $this->prestamo_model->contar_prestamos_activos_usuario($idUsuario);
                $data['mis_solicitudes_pendientes'] = $this->solicitud_model->contar_solicitudes_pendientes_usuario($idUsuario);
                break;
            
            default:
                redirect('usuarios/logout', 'refresh');
                break;
        }
        
        $data['rol'] = $rol;
        
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('panel/' . $rol, $data);
        $this->load->view('inc/footer');
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


public function agregar() {
    $this->_verificar_permisos_creacion();

    $rol_usuario_actual = $this->session->userdata('rol');
    $data['es_admin'] = ($rol_usuario_actual === 'administrador');
    
    $this->load->view('inc/header');
    $this->load->view('inc/nabvar');
    $this->load->view('inc/aside');
    $this->load->view('admin/formulario', $data);
    $this->load->view('inc/footer');
}

public function agregarbd() {
    $this->_verificar_permisos_creacion();

    $rol_usuario_actual = $this->session->userdata('rol');

    $this->form_validation->set_rules('nombres', 'Nombres', 'required');
    $this->form_validation->set_rules('apellidoPaterno', 'Apellido Paterno', 'required');
    $this->form_validation->set_rules('carnet', 'Carnet', 'required|is_unique[USUARIO.carnet]');
    $this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email|is_unique[USUARIO.email]');

    if ($this->form_validation->run() == FALSE) {
        $data['es_admin'] = ($rol_usuario_actual === 'administrador');
        $this->load->view('admin/registrarform', $data);
    } else {
        $idUsuarioCreador = $this->session->userdata('idUsuario');
        
        // Generar nombre de usuario
        $nombres = $this->input->post('nombres');
        $apellidoPaterno = $this->input->post('apellidoPaterno');
        $username = $this->_generar_username($nombres, $apellidoPaterno);
        
        // Generar contraseña temporal
        $contrasena_temporal = $this->_generar_contrasena_temporal();

        $data = array(
            'nombres' => $nombres,
            'apellidoPaterno' => $apellidoPaterno,
            'apellidoMaterno' => $this->input->post('apellidoMaterno'),
            'carnet' => $this->input->post('carnet'),
            'profesion' => $this->input->post('profesion'),
            'fechaNacimiento' => $this->input->post('fechaNacimiento'),
            'sexo' => $this->input->post('sexo'),
            'email' => $this->input->post('email'),
            'username' => $username,
            'password' => password_hash($contrasena_temporal, PASSWORD_DEFAULT),
            'rol' => ($rol_usuario_actual === 'administrador') ? $this->input->post('rol') : 'lector',
            'verificado' => 1,
            'estado' => 1,
            'cambioPasswordRequerido' => 1,
            'fechaCreacion' => date('Y-m-d H:i:s'),
            'idUsuarioCreador' => $idUsuarioCreador
        );

        if ($id_nuevo_usuario = $this->usuario_model->registrarUsuario($data)) {
            if ($this->_enviar_email_bienvenida($data['email'], $username, $contrasena_temporal)) {
                $this->session->set_flashdata('mensaje', 'Usuario registrado con éxito. Se ha enviado un correo con las credenciales.');
            } else {
                $this->session->set_flashdata('error', 'Usuario registrado, pero hubo un problema al enviar el correo. Por favor, contacte al nuevo usuario.');
            }
            redirect('usuarios/mostrar', 'refresh');
        } else {
            $this->session->set_flashdata('error', 'Hubo un problema al registrar el usuario. Inténtalo de nuevo.');
            redirect('usuarios/agregar', 'refresh');
        }
    }
}

private function _generar_username($nombres, $apellidoPaterno) {
    $nombres = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nombres));
    $apellidoPaterno = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $apellidoPaterno));
    $base_username = substr($nombres, 0, 1) . $apellidoPaterno;
    
    $username = $base_username;
    $i = 1;
    while ($this->usuario_model->username_existe($username)) {
        $username = $base_username . $i;
        $i++;
    }
    
    return $username;
}

private function _generar_contrasena_temporal() {
    return bin2hex(random_bytes(4)); // Genera una contraseña de 8 caracteres hexadecimales
}



private function _enviar_email_bienvenida($email, $username, $contrasena_temporal) {
    $this->load->library('email');

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

    $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca "José Antonio Arze"');
    $this->email->to($email);
    $this->email->subject('Información de tu cuenta');

    $mensaje = "
    <html>
    <head>
        <title>Bienvenido a la Hemeroteca José Antonio Arze</title>
    </head>
    <body>
        <h2>Bienvenido!!</h2>
        <p>Tu cuenta ha sido creada exitosamente. Aquí están tus credenciales de acceso:</p>
        <p><strong>Usuario:</strong> $username</p>
        <p><strong>Contraseña temporal:</strong> $contrasena_temporal</p>
        <p>Por razones de seguridad, te pedimos que cambies tu contraseña en tu primer inicio de sesión.</p>
        <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
        <p>¡Gracias por unirte a nosotros!</p>
    </body>
    </html>
    ";

    $this->email->message($mensaje);

    return $this->email->send();
}

    public function auto_registro() {
        $this->form_validation->set_rules('nombres', 'Nombres', 'required');
        $this->form_validation->set_rules('apellidoPaterno', 'Apellido Paterno', 'required');
        $this->form_validation->set_rules('carnet', 'Carnet', 'required|is_unique[USUARIO.carnet]');
        $this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email|is_unique[USUARIO.email]');
        $this->form_validation->set_rules('username', 'Nombre de usuario', 'required|is_unique[USUARIO.username]');
        $this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirmar Contraseña', 'required|matches[password]');

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
                'rol' => 'lector',
                'verificado' => 0,
                'tokenVerificacion' => bin2hex(random_bytes(16)),
                'fechaToken' => date('Y-m-d H:i:s'),
                'intentosVerificacion' => 0,
                'estado' => 1,
                'cambioPasswordRequerido' => 0,
                'fechaCreacion' => date('Y-m-d H:i:s')
            );

            $idUsuarioCreado = $this->usuario_model->registrarUsuario($data);

            if ($idUsuarioCreado) {
                $this->usuario_model->actualizar_usuario($idUsuarioCreado, ['idUsuarioCreador' => $idUsuarioCreado]);

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('error', 'Hubo un problema al registrar el usuario. Inténtalo de nuevo.');
                    redirect('usuarios/auto_registro');
                } else {
                    if ($this->_enviar_email_verificacion($data['email'], $data['tokenVerificacion'])) {
                        $this->session->set_flashdata('mensaje', 'Registro exitoso. Por favor, verifica tu correo electrónico para activar tu cuenta.');
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
            $this->load->library('email');
    
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
    
            $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca "José Antonio arze"');
            $this->email->to($email);
            $this->email->subject('Verifica tu cuenta en la Hemeroteca');
    
            $mensaje = '
            <html>
            <head>
                <title>Verificación de cuenta</title>
            </head>
            <body>
                <h2>Bienvenido!!</h2>
                <p>Gracias por registrarte. Para completar tu registro y activar tu cuenta, por favor haz clic en el siguiente botón:</p>
                <p style="text-align: center;">
                    <a href="' . site_url('usuarios/verificar/' . $token) . '" style="background-color: #4CAF50; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer;">Verificar mi cuenta</a>
                </p>
                <p>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
                <p>' . site_url('usuarios/verificar/' . $token) . '</p>
                <p>Si no has solicitado esta verificación, por favor ignora este mensaje.</p>
            </body>
            </html>';
    
            $this->email->message($mensaje);
    
            return $this->email->send();
        }
    
        public function modificar($idUsuario) {
            $this->_verificar_sesion();
            if ($this->session->userdata('rol') != 'administrador') {
                redirect('usuarios/panel', 'refresh');
            }
        
            $usuario = $this->usuario_model->recuperarUsuario($idUsuario);
            if (!$usuario) {
                $this->session->set_flashdata('error', 'Usuario no encontrado.');
                redirect('usuarios/mostrar', 'refresh');
            }
        
            $data['infoUsuario'] = $usuario;
            $this->load->view('inc/header');
            $this->load->view('admin/formulariomodificar', $data);
            $this->load->view('inc/footer');
        }
        public function modificarbd() {
            $this->_verificar_sesion();
            if ($this->session->userdata('rol') != 'administrador') {
                redirect('usuarios/panel', 'refresh');
            }
        
            $this->form_validation->set_rules('nombres', 'Nombres', 'required');
            $this->form_validation->set_rules('apellidoPaterno', 'Apellido Paterno', 'required');
            $this->form_validation->set_rules('rol', 'Rol', 'required|in_list[administrador,encargado,lector]');
        
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('usuarios/modificar/' . $this->input->post('idUsuario'), 'refresh');
            } else {
                $idUsuario = $this->input->post('idUsuario');
                $usuario = $this->usuario_model->recuperarUsuario($idUsuario);
                if (!$usuario) {
                    $this->session->set_flashdata('error', 'Usuario no encontrado.');
                    redirect('usuarios/mostrar', 'refresh');
                }
        
                $idUsuarioModificador = $this->session->userdata('idUsuario');
                $datos_actualizados = array(
                    'nombres' => strtoupper($this->input->post('nombres')),
                    'apellidoPaterno' => strtoupper($this->input->post('apellidoPaterno')),
                    'apellidoMaterno' => strtoupper($this->input->post('apellidoMaterno')),
                    'carnet' => strtoupper($this->input->post('carnet')),
                    'fechaNacimiento' => $this->input->post('fechaNacimiento'),
                    'sexo' => strtoupper($this->input->post('sexo')),
                    'email' => $this->input->post('email'),
                    'fechaActualizacion' => date('Y-m-d H:i:s'),
                    'idUsuarioCreador' => $idUsuarioModificador
                );
        
                $nuevo_rol = strtoupper($this->input->post('rol'));
                if ($nuevo_rol != $usuario->rol) {
                    $datos_actualizados['rol'] = $nuevo_rol;
                }
        
                $nueva_profesion = strtoupper($this->input->post('profesion'));
                if ($nuevo_rol == 'LECTOR') {
                    if ($nueva_profesion != $usuario->profesion) {
                        $datos_actualizados['profesion'] = $nueva_profesion;
                    }
                } elseif ($usuario->rol == 'LECTOR' && $nuevo_rol != 'LECTOR') {
                    $datos_actualizados['profesion'] = NULL;
                }
        
                if ($this->usuario_model->modificarUsuario($idUsuario, $datos_actualizados)) {
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
                $this->load->view('inc/header');
                $this->load->view('usuarios/cambiar_password');
                $this->load->view('inc/footer');
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
    
        public function deshabilitarbd() {
            $this->_verificar_sesion();
            if ($this->session->userdata('rol') != 'administrador') {
                redirect('usuarios/panel', 'refresh');
            }
    
            $idUsuario = $this->input->post('idUsuario');
            $idUsuarioSesion = $this->session->userdata('idUsuario');
    
            $data = array(
                'estado' => 0,
                'fechaActualizacion' => date('Y-m-d H:i:s'),
                'idUsuarioCreador' => $idUsuarioSesion
            );
    
            if ($this->usuario_model->modificarUsuario($idUsuario, $data)) {
                $this->session->set_flashdata('mensaje', 'Usuario deshabilitado correctamente.');
            } else {
                $this->session->set_flashdata('error', 'No se pudo deshabilitar el usuario.');
            }
    
            redirect('usuarios/mostrar', 'refresh');
        }
    
        
        public function deshabilitados()
        {
            $this->_verificar_sesion();
            if ($this->session->userdata('rol') != 'administrador') {
                $this->session->set_flashdata('error', 'No tienes permisos para acceder a esta sección.');
                redirect('usuarios/panel', 'refresh');
            }

            $data['usuarios'] = $this->usuario_model->listaUsuariosDeshabilitados();
            
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            $this->load->view('admin/listadeshabilitados', $data);
            $this->load->view('inc/footer');
        }
        public function habilitarbd()
        {
            $this->_verificar_sesion();
            if ($this->session->userdata('rol') != 'administrador') {
                $this->session->set_flashdata('error', 'No tienes permisos para realizar esta acción.');
                redirect('usuarios/panel', 'refresh');
            }
    
            $idUsuarioSesion = $this->session->userdata('idUsuario');
            if (!$idUsuarioSesion) {
                log_message('error', 'ID de usuario no encontrado en la sesión');
                $this->session->set_flashdata('error', 'Error de sesión. Por favor, inicie sesión nuevamente.');
                redirect('usuarios/logout', 'refresh');
            }
    
            $idUsuario = $this->input->post('idUsuario');
            $data = array(
                'estado' => 1,  // 1 representa el estado habilitado
                'idUsuarioCreador' => $idUsuarioSesion,
                'fechaActualizacion' => date('Y-m-d H:i:s')
            );
    
            $result = $this->usuario_model->modificarUsuario($idUsuario, $data);
    
            if ($result) {
                $this->session->set_flashdata('mensaje', 'Usuario habilitado correctamente.');
            } else {
                $this->session->set_flashdata('error', 'No se pudo habilitar el usuario.');
            }
    
            redirect('usuarios/deshabilitados', 'refresh');
        }

        public function lector($mostrar_perfil = false) {
            $this->_verificar_sesion();
        
            if ($this->session->userdata('rol') == 'lector') {
                $this->load->model('publicacion_model');
                $this->load->model('prestamo_model');
                $this->load->model('solicitud_model');
        
                $idUsuario = $this->session->userdata('idUsuario');
        
                $data['publicaciones'] = $this->publicacion_model->listar_publicaciones();
                $data['prestamos_activos'] = $this->prestamo_model->obtener_prestamos_activos_usuario($idUsuario);
                $data['solicitudes_pendientes'] = $this->solicitud_model->obtener_solicitudes_pendientes_usuario($idUsuario);
                $data['usuario'] = $this->usuario_model->obtener_usuario($idUsuario);
        
                $this->load->view('inc/header');
                $this->load->view('inc/nabvar');
                $this->load->view('inc/aside');
                
                if ($mostrar_perfil) {
                    $this->load->view('usuarios/perfil', $data);
                } else {
                    $this->load->view('lector/panelguest', $data);
                }
                
                $this->load->view('inc/footer');
                
                log_message('debug', 'Método lector cargado. Mostrar perfil: ' . ($mostrar_perfil ? 'Sí' : 'No'));
            } else {
                redirect('usuarios/panel', 'refresh');
            }
        }
        public function historial() {
            $this->_verificar_sesion();
    
            $idUsuario = $this->session->userdata('idUsuario');
    
            $data['historial_prestamos'] = $this->prestamo_model->obtener_historial_prestamos($idUsuario);
            $data['historial_reservas'] = $this->reserva_model->obtener_historial_reservas($idUsuario);
    
            $this->load->view('inc/header');
            $this->load->view('admin/historial', $data);
            $this->load->view('inc/footer');
        }
    
        public function actualizar_preferencias_notificacion() {
            $this->_verificar_sesion();
    
            $idUsuario = $this->session->userdata('idUsuario');
            
            $preferencias = [
                'email' => $this->input->post('notificar_email') ? true : false,
                'sistema' => $this->input->post('notificar_sistema') ? true : false,
            ];
    
            if ($this->usuario_model->actualizar_preferencias_notificacion($idUsuario, $preferencias)) {
                $this->session->set_flashdata('mensaje', 'Preferencias de notificación actualizadas con éxito.');
            } else {
                $this->session->set_flashdata('error', 'Error al actualizar las preferencias de notificación.');
            }
    
            redirect('usuarios/perfil');
        }
    
        public function mostrar_preferencias_notificacion() {
            $this->_verificar_sesion();
    
            $idUsuario = $this->session->userdata('idUsuario');
            $data['preferencias'] = $this->usuario_model->obtener_preferencias_notificacion($idUsuario);
    
            $this->load->view('inc/header');
            $this->load->view('usuarios/preferencias_notificacion', $data);
            $this->load->view('inc/footer');
        }


public function lista_usuarios() {
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
public function contar_usuarios() {
    $this->_verificar_sesion();
    if ($this->session->userdata('rol') == 'administrador') {
        $total_usuarios = $this->usuario_model->contar_usuarios();
        // Procesar el resultado según sea necesario
    } else {
        redirect('usuarios/panel', 'refresh');
    }
}
public function cambiar_estado_usuario() {
    $this->_verificar_sesion();
    if ($this->session->userdata('rol') == 'administrador') {
        $idUsuario = $this->input->post('idUsuario');
        $estado = $this->input->post('estado');
        if ($this->usuario_model->cambiar_estado_usuario($idUsuario, $estado)) {
            $this->session->set_flashdata('mensaje', 'Estado del usuario actualizado con éxito.');
        } else {
            $this->session->set_flashdata('error', 'Error al actualizar el estado del usuario.');
        }
        redirect('usuarios/mostrar', 'refresh');
    } else {
        redirect('usuarios/panel', 'refresh');
    }
}
public function actualizar_token_verificacion() {
    $idUsuario = $this->input->post('idUsuario');
    $token = bin2hex(random_bytes(16));
    if ($this->usuario_model->actualizar_token_verificacion($idUsuario, $token)) {
        // Enviar email con el nuevo token
        $this->_enviar_email_verificacion($usuario->email, $token);
    } else {
        $this->session->set_flashdata('error', 'Error al actualizar el token de verificación.');
    }
}
public function recuperar_usuario($idUsuario) {
    $this->_verificar_sesion();
    if ($this->session->userdata('rol') == 'administrador') {
        $usuario = $this->usuario_model->recuperarUsuario($idUsuario);
        if ($usuario) {
            // Procesar o mostrar la información del usuario
        } else {
            $this->session->set_flashdata('error', 'Usuario no encontrado.');
            redirect('usuarios/mostrar', 'refresh');
        }
    } else {
        redirect('usuarios/panel', 'refresh');
    }
}
public function obtener_usuario($idUsuario) {
    $this->_verificar_sesion();
    $usuario = $this->usuario_model->obtener_usuario($idUsuario);
    // Procesar el resultado según sea necesario
}
public function reenviar_verificacion()
    {
        $this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('usuarios/reenviar_verificacion');
        } else {
            $email = $this->input->post('email');
            $usuario = $this->usuario_model->obtener_usuario_no_verificado($email);

            if ($usuario) {
                if ($usuario->intentosVerificacion < 3) {
                    $nuevoToken = bin2hex(random_bytes(16));
                    if ($this->usuario_model->actualizar_token_verificacion1($usuario->idUsuario, $nuevoToken)) {
                        if ($this->_enviar_email_verificacion($email, $nuevoToken)) {
                            $this->usuario_model->incrementar_intentos_verificacion($usuario->idUsuario);
                            $this->session->set_flashdata('mensaje', 'Se ha enviado un nuevo correo de verificación. Por favor, revisa tu bandeja de entrada.');
                        } else {
                            $this->session->set_flashdata('error', 'Hubo un problema al enviar el correo de verificación. Por favor, inténtalo de nuevo más tarde.');
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Hubo un problema al generar un nuevo token de verificación. Por favor, inténtalo de nuevo más tarde.');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Has excedido el número máximo de intentos de verificación. Por favor, contacta al administrador.');
                }
            } else {
                $this->session->set_flashdata('error', 'No se encontró una cuenta no verificada con ese correo electrónico.');
            }
            redirect('usuarios/reenviar_verificacion');
        }
    }
    public function recuperar_contrasena() {
        $this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $data['error'] = validation_errors();
            $this->load->view('usuarios/recuperar_contrasena', $data);
        } else {
            $email = $this->input->post('email');
            $usuario = $this->usuario_model->obtener_por_email($email);

            if ($usuario) {
                $token = bin2hex(random_bytes(50));
                if ($this->usuario_model->guardar_token_recuperacion($usuario->idUsuario, $token)) {
                    if ($this->_enviar_email_recuperacion($email, $token)) {
                        $this->session->set_flashdata('mensaje', 'Se ha enviado un correo con instrucciones para recuperar tu contraseña. Por favor, revisa tu bandeja de entrada.');
                    } else {
                        $this->session->set_flashdata('error', 'Hubo un problema al enviar el correo. Por favor, intenta de nuevo más tarde.');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Ocurrió un error al procesar tu solicitud. Por favor, intenta de nuevo.');
                }
                redirect('usuarios/index');
            } else {
                $this->session->set_flashdata('error', 'No se encontró ninguna cuenta con ese correo electrónico.');
                redirect('usuarios/recuperar_contrasena');
            }
        }
    }

    public function reset_contrasena($token) {
        $usuario = $this->usuario_model->obtener_por_token_recuperacion($token);

        if (!$usuario) {
            $this->session->set_flashdata('error', 'El enlace de recuperación es inválido o ha expirado. Por favor, solicita uno nuevo.');
            redirect('usuarios/recuperar_contrasena');
        }

        $this->form_validation->set_rules('password', 'Nueva contraseña', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirmar contraseña', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $data['token'] = $token;
            $data['error'] = validation_errors();
            $this->load->view('usuarios/reset_contrasena', $data);
        } else {
            $new_password = $this->input->post('password');
            if ($this->usuario_model->actualizar_password($usuario->idUsuario, $new_password)) {
                if ($this->usuario_model->eliminar_token_recuperacion($usuario->idUsuario)) {
                    $this->session->set_flashdata('mensaje', 'Tu contraseña ha sido actualizada correctamente. Ahora puedes iniciar sesión con tu nueva contraseña.');
                } else {
                    $this->session->set_flashdata('error', 'Tu contraseña ha sido actualizada, pero hubo un problema al finalizar el proceso. Por seguridad, contacta al administrador.');
                }
            } else {
                $this->session->set_flashdata('error', 'Hubo un problema al actualizar tu contraseña. Por favor, intenta de nuevo.');
            }
            redirect('usuarios/login');
        }
    }

    private function _enviar_email_recuperacion($email, $token) {
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

        $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca "José Antonio Arze"');
        $this->email->to($email);
        $this->email->subject('Recuperación de contraseña');

        $mensaje = '
        <html>
        <head>
            <title>Recuperación de contraseña</title>
        </head>
        <body>
            <h2>Recuperación de contraseña</h2>
            <p>Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para crear una nueva contraseña:</p>
            <p><a href="' . site_url('usuarios/reset_contrasena/' . $token) . '">Restablecer contraseña</a></p>
            <p>Si no has solicitado este cambio, puedes ignorar este correo. Este enlace expirará en 24 horas por seguridad.</p>
        </body>
        </html>';

        $this->email->message($mensaje);

        return $this->email->send();
    }
    public function perfil() {
        $this->_verificar_sesion();
        $idUsuario = $this->session->userdata('idUsuario');
        $rol = $this->session->userdata('rol');
        
        $data['usuario'] = $this->usuario_model->obtener_usuario($idUsuario);
        
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        
        if ($rol == 'lector') {
            $this->load->view('usuarios/perfil', $data);
        } else {
            $this->load->view('usuarios/perfil', $data);
        }
        
        $this->load->view('inc/footer');
    }
    
    public function configuracion() {
        $this->_verificar_sesion();
        $idUsuario = $this->session->userdata('idUsuario');
        $rol = $this->session->userdata('rol');
        
        if ($this->input->post()) {
            $this->form_validation->set_rules('nuevo_username', 'Nuevo nombre de usuario', 'required|is_unique[USUARIO.username]');
            $this->form_validation->set_rules('nueva_password', 'Nueva Contraseña', 'required|min_length[6]');
            $this->form_validation->set_rules('confirmar_password', 'Confirmar Contraseña', 'required|matches[nueva_password]');
    
            if ($this->form_validation->run()) {
                $nuevo_username = $this->input->post('nuevo_username');
                $nueva_password = $this->input->post('nueva_password');
    
                $this->db->trans_start();
                $resultado = $this->usuario_model->actualizar_configuracion($idUsuario, $nuevo_username, $nueva_password);
                $this->db->trans_complete();
    
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('error', 'Error al actualizar la configuración. Inténtalo de nuevo.');
                } else {
                    $this->session->set_flashdata('mensaje', 'Configuración actualizada con éxito.');
                    $this->session->set_userdata('username', $nuevo_username);
                }
                redirect('usuarios/configuracion');
            }
        }
    
        $data['usuario'] = $this->usuario_model->obtener_usuario($idUsuario);
        
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('usuarios/configuracion', $data);
        $this->load->view('inc/footer');
    }
    public function obtener_por_username($username) {
        $this->db->select('idUsuario, username, password, nombres, apellidoPaterno, rol, estado, cambioPasswordRequerido');
        $this->db->where('username', $username);
        return $this->db->get('USUARIO')->row();
    }
   
}
    