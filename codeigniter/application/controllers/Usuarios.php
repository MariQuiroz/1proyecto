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
    
    $this->form_validation->set_rules('nombres', 'Nombres', 'required|trim');
    $this->form_validation->set_rules('apellidoPaterno', 'Apellido Paterno', 'required|trim');
    $this->form_validation->set_rules('carnet', 'Carnet', 'required|trim|is_unique[USUARIO.carnet]');
    $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[USUARIO.email]');

    if ($this->form_validation->run() == FALSE) {
        $data['es_admin'] = ($this->session->userdata('rol') === 'administrador');
        $this->load->view('inc/header');
        $this->load->view('inc/nabvar');
        $this->load->view('inc/aside');
        $this->load->view('admin/formulario', $data);
        $this->load->view('inc/footer');
        return;
    }

    try {
        $this->db->trans_start();

        // Datos del usuario
        $data = $this->_preparar_datos_usuario();
        
        // Registrar usuario
        $id_nuevo_usuario = $this->usuario_model->registrarUsuario($data);
        
        if (!$id_nuevo_usuario) {
            throw new Exception('Error al registrar el usuario');
        }

        // Enviar email
        $email_enviado = $this->_enviar_email_bienvenida(
            $data['email'], 
            $data['username'], 
            $this->input->post('contrasena_temporal')
        );

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            throw new Exception('Error en la transacción de base de datos');
        }

        $mensaje = 'Usuario registrado con éxito.';
        if (!$email_enviado) {
            $mensaje .= ' Nota: No se pudo enviar el correo de bienvenida.';
        }

        $this->session->set_flashdata('mensaje', $mensaje);
        redirect('usuarios/mostrar');

    } catch (Exception $e) {
        $this->db->trans_rollback();
        log_message('error', 'Error en agregarbd: ' . $e->getMessage());
        $this->session->set_flashdata('error', 'Error al registrar el usuario: ' . $e->getMessage());
        redirect('usuarios/agregar');
    }
}

private function _verificar_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    $this->db->select('idUsuario');
    $this->db->from('USUARIO');
    $this->db->where('email', $email);
    
    return $this->db->get()->num_rows() === 0;
}
private function _get_email_config() {
    return array(
        'protocol' => 'smtp',
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 587,
        'smtp_user' => 'quirozmolinamaritza@gmail.com',
        'smtp_pass' => 'zdmk qkfw wgdf lshq',
        'smtp_crypto' => 'tls',
        'mailtype' => 'html',
        'charset' => 'utf-8',
        'newline' => "\r\n",
        'smtp_timeout' => 30,
        'smtp_keepalive' => true,
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    );
}

private function _enviar_email_bienvenida($email, $username, $contrasena_temporal) {
    try {
        // Verificar el email primero
        if (!$this->_verificar_email($email)) {
            log_message('error', 'Email inválido o ya existe: ' . $email);
            return false;
        }

        $this->email->initialize($this->_get_email_config());

        $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca UMSS');
        $this->email->to($email);
        $this->email->subject('Bienvenido a la Hemeroteca UMSS');

        $mensaje = $this->load->view('emails/bienvenida', array(
            'username' => $username,
            'contrasena' => $contrasena_temporal
        ), true);

        $this->email->message($mensaje);

        if (!$this->email->send()) {
            log_message('error', 'Error al enviar email: ' . $this->email->print_debugger());
            throw new Exception('Error al enviar el correo electrónico');
        }

        return true;

    } catch (Exception $e) {
        log_message('error', 'Exception al enviar email: ' . $e->getMessage());
        return false;
    }
}

/**
 * Prepara los datos del usuario para su registro
 * @return array Datos del usuario formateados
 */
private function _preparar_datos_usuario() {
    // Obtener el rol del usuario actual para determinar el rol que puede asignar
    $rol_usuario_actual = $this->session->userdata('rol');
    $idUsuarioCreador = $this->session->userdata('idUsuario');
    
    // Generar nombre de usuario a partir de nombres y apellido
    $nombres = $this->input->post('nombres');
    $apellidoPaterno = $this->input->post('apellidoPaterno');
    $username = $this->_generar_username($nombres, $apellidoPaterno);
    
    // Generar contraseña temporal
    $contrasena_temporal = $this->_generar_contrasena_temporal();
    
    // Preparar datos base del usuario
    $data = array(
        'nombres' => trim(strtoupper($nombres)),
        'apellidoPaterno' => trim(strtoupper($apellidoPaterno)),
        'apellidoMaterno' => trim(strtoupper($this->input->post('apellidoMaterno'))),
        'carnet' => trim(strtoupper($this->input->post('carnet'))),
        'profesion' => trim($this->input->post('profesion')),
        'fechaNacimiento' => $this->input->post('fechaNacimiento'),
        'sexo' => $this->input->post('sexo'),
        'email' => trim(strtolower($this->input->post('email'))),
        'username' => $username,
        'password' => password_hash($contrasena_temporal, PASSWORD_DEFAULT),
        'verificado' => 1, // Usuario creado por admin/encargado ya está verificado
        'estado' => 1,
        'cambioPasswordRequerido' => 1, // Forzar cambio de contraseña en primer login
        'fechaCreacion' => date('Y-m-d H:i:s'),
        'idUsuarioCreador' => $idUsuarioCreador
    );

    // Determinar el rol según quien crea el usuario
    if ($rol_usuario_actual === 'administrador') {
        $data['rol'] = $this->input->post('rol');
    } else {
        // Si es encargado, solo puede crear lectores
        $data['rol'] = 'lector';
    }

    // Validar profesión solo para lectores
    if ($data['rol'] === 'lector') {
        if (empty($data['profesion'])) {
            throw new Exception('La profesión es requerida para usuarios lectores.');
        }
    } else {
        $data['profesion'] = null; // No aplica para admin/encargado
    }

    // Guardar la contraseña temporal en sesión para el email
    $this->session->set_flashdata('contrasena_temporal', $contrasena_temporal);

    log_message('debug', 'Datos de usuario preparados para: ' . $username);
    
    return $data;
}

/**
 * Genera un nombre de usuario único basado en el nombre y apellido
 * @param string $nombres
 * @param string $apellidoPaterno
 * @return string
 */
private function _generar_username($nombres, $apellidoPaterno) {
    $nombres = strtolower($nombres);
    $apellidoPaterno = strtolower($apellidoPaterno);
    
    // Limpiar caracteres especiales y espacios
    $nombres = preg_replace('/[^a-z0-9]/', '', $nombres);
    $apellidoPaterno = preg_replace('/[^a-z0-9]/', '', $apellidoPaterno);
    
    // Crear username base: primera letra del nombre + apellido
    $base_username = substr($nombres, 0, 1) . $apellidoPaterno;
    
    $username = $base_username;
    $contador = 1;
    
    // Verificar si ya existe y generar uno único
    while ($this->usuario_model->username_existe($username)) {
        $username = $base_username . $contador;
        $contador++;
    }
    
    return $username;
}

/**
 * Genera una contraseña temporal segura
 * @return string
 */
private function _generar_contrasena_temporal() {
    $length = 8;
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    
    return $str;
}

/*private function _enviar_email_bienvenida($email, $username, $contrasena_temporal) {
    try {
        // Cargar la librería de email
        $this->load->library('email');

        // Validar parámetros
        if (empty($email) || empty($username) || empty($contrasena_temporal)) {
            log_message('error', 'Parámetros incompletos para envío de email');
            return false;
        }

        // Configuración actualizada de SMTP
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_user' => 'quirozmolinamaritza@gmail.com',
            'smtp_pass' => 'zdmk qkfw wgdf lshq',
            'smtp_crypto' => 'tls',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'wordwrap' => TRUE,
            'newline' => "\r\n",
            'crlf' => "\r\n",
            // Configuraciones adicionales para SSL
            'smtp_timeout' => '30',
            'validate' => TRUE,
            'priority' => 1,
            'smtp_keepalive' => TRUE,
            // Deshabilitar verificación de SSL
            'smtp_verify' => FALSE,
            'smtp_auto_tls' => FALSE
        );

        // Inicializar email con la nueva configuración
        $this->email->initialize($config);
        
        // Limpiar cualquier configuración previa
        $this->email->clear(TRUE);

        // Configurar el email
        $this->email->set_mailtype('html');
        $this->email->set_newline("\r\n");
        $this->email->from($config['smtp_user'], 'Hemeroteca "José Antonio Arze"');
        $this->email->to($email);
        $this->email->subject('Bienvenido a la Hemeroteca - Información de tu cuenta');

        // Contenido del email
        $mensaje = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style type="text/css">
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header {
                    background-color: #003366;
                    color: white;
                    padding: 20px;
                    text-align: center;
                    border-radius: 5px 5px 0 0;
                }
                .content {
                    background-color: #ffffff;
                    padding: 20px;
                    border: 1px solid #dddddd;
                    border-radius: 0 0 5px 5px;
                }
                .credentials {
                    background-color: #f5f5f5;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 5px;
                    border-left: 4px solid #003366;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>¡Bienvenido a la Hemeroteca!</h1>
            </div>
            <div class="content">
                <h2>¡Hola!</h2>
                <p>Tu cuenta ha sido creada exitosamente en la Hemeroteca "José Antonio Arze".</p>
                
                <div class="credentials">
                    <h3>Tus credenciales de acceso:</h3>
                    <p><strong>Usuario:</strong> ' . htmlspecialchars($username) . '</p>
                    <p><strong>Contraseña temporal:</strong> ' . htmlspecialchars($contrasena_temporal) . '</p>
                </div>

                <p><strong>Importante:</strong> Por razones de seguridad, deberás cambiar tu contraseña en tu primer inicio de sesión.</p>
            </div>
        </body>
        </html>';

        $this->email->message($mensaje);

        // Intentar enviar el email con manejo de errores detallado
        if (!$this->email->send()) {
            $error = $this->email->print_debugger(['headers']);
            log_message('error', 'Error detallado al enviar email: ' . $error);
            
            // Intentar un segundo envío con configuración alternativa si el primero falla
            $config['smtp_port'] = 465; // Puerto alternativo
            $config['smtp_crypto'] = 'ssl'; // Cambiar a SSL
            
            $this->email->initialize($config);
            if (!$this->email->send()) {
                log_message('error', 'Segundo intento fallido: ' . $this->email->print_debugger(['headers']));
                return false;
            }
        }

        log_message('info', 'Email de bienvenida enviado exitosamente a: ' . $email);
        return true;

    } catch (Exception $e) {
        log_message('error', 'Exception al enviar email: ' . $e->getMessage());
        return false;
    }
}
*/

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
        
                $data['publicaciones'] = $this->publicacion_model->listar_todas_publicaciones();
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
            redirect('usuarios/index');
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
    