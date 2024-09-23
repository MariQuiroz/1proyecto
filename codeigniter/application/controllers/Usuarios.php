<?php
defined('BASEPATH') OR exit('No se permite acceso directo al script');

class Usuarios extends CI_Controller {
    public function __construct() {
        parent::__construct();
        if (ENVIRONMENT !== 'development') {
            force_https();
        }
        $this->load->library('email');
    }

    

    private function _verificar_sesion()
    {
        // Verifica si el usuario está logueado
        if (!$this->session->userdata('login'))
        {
            redirect('usuarios/index/3', 'refresh');
        }
        
        // Asegúrate de que el ID del usuario y el rol estén en la sesión
        if (!$this->session->userdata('idUsuario') || !$this->session->userdata('rol'))
        {
            $username = $this->session->userdata('username');
            $usuario = $this->usuario_model->obtener_por_username($username);
            if ($usuario)
            {
                $this->session->set_userdata('idUsuario', $usuario->idUsuario);
                $this->session->set_userdata('rol', $usuario->rol);
            }
            else
            {
                // Si no se puede obtener el ID o el rol, cierra la sesión y redirige al login
                $this->session->sess_destroy();
                redirect('usuarios/index/2', 'refresh');
            }
        }
        
        // Verifica el rol y redirige si es necesario
        $rol = $this->session->userdata('rol');
        $metodo_actual = $this->router->fetch_method();
        
        if ($rol == 'administrador' && $metodo_actual == 'lector')
        {
            redirect('usuarios/mostrar', 'refresh');
        }
        elseif ($rol == 'lector' && !in_array($metodo_actual, ['lector', 'logout', 'modificarbd']))
        {
            redirect('usuarios/lector', 'refresh');
        }
    }

    public function index()
    {
        $data['msg'] = $this->uri->segment(3);

        if($this->session->userdata('login'))
        {
            redirect('usuarios/panel','refresh');
        }
        else
        {
           //$this->load->view('inc/header');
            $this->load->view('login', $data);
            //$this->load->view('inc/footer');
        }
    }

   /* public function validar()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $usuario = $this->usuario_model->obtener_por_username($username);

        if ($usuario && password_verify($password, $usuario->password))
        {
            // Autenticación exitosa
            $this->session->set_userdata('login', TRUE);
            $this->session->set_userdata('idUsuario', $usuario->idUsuario);
            $this->session->set_userdata('username', $usuario->username);
            $this->session->set_userdata('rol', $usuario->rol);
            redirect('usuarios/panel','refresh');
        }
        else
        {
            // Autenticación fallida
            redirect('usuarios/index/2','refresh');
        }
    }*/
    public function validar()
{
    $username = $this->input->post('username');
    $password = $this->input->post('password');

    $usuario = $this->usuario_model->obtener_por_username($username);

    if ($usuario && password_verify($password, $usuario->password))
    {
        if ($usuario->verificado == 1) {
            // Autenticación exitosa
            $this->session->set_userdata('login', TRUE);
            $this->session->set_userdata('idUsuario', $usuario->idUsuario);
            $this->session->set_userdata('username', $usuario->username);
            $this->session->set_userdata('rol', $usuario->rol);
            redirect('usuarios/panel','refresh');
        } else {
            $this->session->set_flashdata('error', 'Por favor, verifica tu cuenta de correo electrónico antes de iniciar sesión.');
            redirect('usuarios/index/4','refresh');
        }
    }
    else
    {
        // Autenticación fallida
        redirect('usuarios/index/2','refresh');
    }
}


    /*public function panel() {
        if($this->session->userdata('login')) {
            $data = array();
            
            if($this->session->userdata('rol') == 'administrador') {
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
        } else {
            redirect('usuarios/index/3','refresh');
        }
    }*/
    public function panel() {
        if($this->session->userdata('login')) {
            $data = array();
            
            if($this->session->userdata('rol') == 'administrador') {
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
        } else {
            redirect('usuarios/index/3','refresh');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('usuarios/index/1','refresh');
    }

	public function mostrar()
    {
        if($this->session->userdata('rol') == 'administrador')
        { 
            $lista = $this->usuario_model->listaUsuarios();
            $data['usuarios'] = $lista;
            
            $this->load->view('inc/header');
            $this->load->view('inc/nabvar');
            $this->load->view('inc/aside');
            //$this->load->view('inc/menu',$data);
            $this->load->view('admin/lista', $data);
            
            $this->load->view('inc/footer');
        }
        else
        {
            redirect('usuarios/panel', 'refresh');
        }
    }
    /*public function registro() {
        $this->load->view('usuarios/registro');
    }
*/
    public function procesar_registro() {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[USUARIO.email]');
        $this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[6]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('admin/registrarform');
        } else {
            $datos = array(
                'email' => $this->input->post('email'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'token_verificacion' => bin2hex(random_bytes(16))
            );

            if ($this->usuario_model->registrar($datos)) {
                $this->enviar_email_verificacion($datos['email'], $datos['token_verificacion']);
                $this->session->set_flashdata('mensaje', 'Registro exitoso. Por favor, verifica tu email.');
                redirect('usuarios/validar');
            } else {
                $this->session->set_flashdata('error', 'Error en el registro. Inténtalo de nuevo.');
                redirect('usuarios/registrar');
            }
        }
    }

    private function enviar_email_verificacion($email, $token) {
        $this->email->from('tu_correo@gmail.com', 'Nombre de tu Aplicación');
        $this->email->to($email);
        $this->email->subject('Verifica tu cuenta');
        $this->email->message('Haz clic en este enlace para verificar tu cuenta: ' . 
                              site_url('usuarios/_verificar_sesion/' . $token));
        $this->email->send();
    }

    /*public function verificar($token) {
        if ($this->usuario_model->verificar_cuenta($token)) {
            $this->session->set_flashdata('mensaje', 'Cuenta verificada con éxito. Ya puedes iniciar sesión.');
        } else {
            $this->session->set_flashdata('error', 'Token inválido o expirado.');
        }
        redirect('usuarios/validar');
    }*/

/*public function registrar()
{
    if($this->session->userdata('rol') == 'administrador')
    { 
        $this->load->view('inc/header');
        $this->load->view('admin/registrarform');
        $this->load->view('inc/footer');
    }
    else
    {
        redirect('usuarios/panel', 'refresh');
    }
}*/

public function registrarbd()
{
    $this->_verificar_sesion();

    $data['nombres'] = $_POST['nombres'];
    $data['apellidoPaterno'] = $_POST['apellidoPaterno'];
    $data['apellidoMaterno'] = $_POST['apellidoMaterno'];
    $data['carnet'] = $_POST['carnet'];
    $data['profesion'] = $_POST['profesion'];
    $data['fechaNacimiento'] = $_POST['fechaNacimiento'];
    $data['sexo'] = $_POST['sexo'];
    $data['email'] = $_POST['email'];
    $data['username'] = $_POST['username'];
    $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $data['rol'] = $_POST['rol'];

    $this->usuario_model->registrarUsuario($data);
    redirect('usuarios/mostrar', 'refresh');
}

public function lector()
{
    $this->_verificar_sesion();

    if($this->session->userdata('rol') == 'lector')
    { 
        // Cargar el modelo de publicaciones
        $this->load->model('publicacion_model');
        
        // Obtener la lista de publicaciones
        $data['publicaciones'] = $this->publicacion_model->listar_publicaciones();
        
        // Cargar las vistas
        $this->load->view('inc/header');
        $this->load->view('lector/panelguest', $data);
        $this->load->view('inc/footer');
    }
    else
    {
        redirect('usuarios/panel', 'refresh');
    }
}

public function agregar()
{
    $this->_verificar_sesion();
    
    $this->load->view('inc/header');
    $this->load->view('admin/formulario');
    $this->load->view('inc/footer');
}

public function agregarbd()
{
    $this->_verificar_sesion();

    $idUsuarioSesion = $this->session->userdata('idUsuario');
    if (!$idUsuarioSesion) {
        log_message('error', 'ID de usuario no encontrado en la sesión');
        $this->session->set_flashdata('error', 'Error de sesión. Por favor, inicie sesión nuevamente.');
        redirect('usuarios/logout', 'refresh');
    }

    $data = array(
        'nombres' => strtoupper($this->input->post('nombres')),
        'apellidoPaterno' => strtoupper($this->input->post('apellidoPaterno')),
        'apellidoMaterno' => strtoupper($this->input->post('apellidoMaterno')),
        'carnet' => strtoupper($this->input->post('carnet')),
        'profesion' => strtoupper($this->input->post('profesion')),
        'fechaNacimiento' => $this->input->post('fechaNacimiento'),
        'sexo' => strtoupper($this->input->post('sexo')),
        'email' => $this->input->post('email'),
        'username' => strtolower($this->input->post('username')),
        'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
        'rol' => strtoupper($this->input->post('rol')),
        'usuarioSesion' => $idUsuarioSesion  // Añadimos el ID del usuario que está creando el nuevo usuario
    );

    $this->usuario_model->agregarUsuario($data);
    redirect('usuarios/mostrar', 'refresh');
}

public function eliminarbd()
{
    $this->_verificar_sesion();

    $idUsuario = $_POST['idUsuario'];
    $this->usuario_model->eliminarUsuario($idUsuario);
    redirect('usuarios/mostrar', 'refresh');
}

public function modificar()
{
    $idUsuario = $_POST['idUsuario'];
    $data['infoUsuario'] = $this->usuario_model->recuperarUsuario($idUsuario);

    $this->load->view('inc/header');
    $this->load->view('admin/formulariomodificar', $data);
    $this->load->view('inc/footer');
}

public function modificarbd()
{
    $this->_verificar_sesion();

    // Verificar si el ID del usuario está en la sesión
    $idUsuarioSesion = $this->session->userdata('idUsuario');
    if (!$idUsuarioSesion) {
        log_message('error', 'ID de usuario no encontrado en la sesión');
        $this->session->set_flashdata('error', 'Error de sesión. Por favor, inicie sesión nuevamente.');
        redirect('usuarios/logout', 'refresh');
    }

    $idUsuario = $this->input->post('idUsuario');
    $data = array(
        'nombres' => strtoupper($this->input->post('nombres')),
        'apellidoPaterno' => strtoupper($this->input->post('apellidoPaterno')),
        'apellidoMaterno' => strtoupper($this->input->post('apellidoMaterno')),
        'carnet' => strtoupper($this->input->post('carnet')),
        'profesion' => strtoupper($this->input->post('profesion')),
        'fechaNacimiento' => $this->input->post('fechaNacimiento'),
        'sexo' => strtoupper($this->input->post('sexo')),
        'email' => $this->input->post('email'),
        'rol' => strtoupper($this->input->post('rol')),
        'usuarioSesion' => $idUsuarioSesion 
    );

    // Para depuración
    log_message('debug', 'Datos a actualizar: ' . print_r($data, true));
    // Actualiza el usuario con campos restringidos
    $result = $this->usuario_model->modificarUsuarioRestringido($idUsuario, $data);

    if($result) {
        $this->session->set_flashdata('success', 'Usuario actualizado correctamente.');
    } else {
        $this->session->set_flashdata('error', 'No se pudo actualizar el usuario.');
    }

    // Redirecciona
    redirect('usuarios/mostrar', 'refresh');
}

public function deshabilitarbd()
{
    $this->_verificar_sesion();

    $idUsuarioSesion = $this->session->userdata('idUsuario');
    if (!$idUsuarioSesion) {
        log_message('error', 'ID de usuario no encontrado en la sesión');
        $this->session->set_flashdata('error', 'Error de sesión. Por favor, inicie sesión nuevamente.');
        redirect('usuarios/logout', 'refresh');
    }

    $idUsuario = $this->input->post('idUsuario');
    $data = array(
        'estado' => 0,  // 0 representa el estado deshabilitado
        'usuarioSesion' => $idUsuarioSesion
    );

    $result = $this->usuario_model->modificarUsuario($idUsuario, $data);

    if ($result) {
        $this->session->set_flashdata('success', 'Usuario deshabilitado correctamente.');
    } else {
        $this->session->set_flashdata('error', 'No se pudo deshabilitar el usuario.');
    }

    redirect('usuarios/mostrar', 'refresh');
}

public function deshabilitados()
{
    $lista = $this->usuario_model->listaUsuariosDeshabilitados();
    $data['usuarios'] = $lista;
    
    $this->load->view('inc/header');
    $this->load->view('inc/nabvar');
    $this->load->view('inc/aside');
    $this->load->view('admin/listadeshabilitados', $data);
    $this->load->view('inc/footer');
}

public function habilitarbd()
{
    $this->_verificar_sesion();

    $idUsuarioSesion = $this->session->userdata('idUsuario');
    if (!$idUsuarioSesion) {
        log_message('error', 'ID de usuario no encontrado en la sesión');
        $this->session->set_flashdata('error', 'Error de sesión. Por favor, inicie sesión nuevamente.');
        redirect('usuarios/logout', 'refresh');
    }

    $idUsuario = $this->input->post('idUsuario');
    $data = array(
        'estado' => 1,  // 0 representa el estado deshabilitado
        'usuarioSesion' => $idUsuarioSesion
    );

    $result = $this->usuario_model->modificarUsuario($idUsuario, $data);

    if ($result) {
        $this->session->set_flashdata('success', 'Usuario deshabilitado correctamente.');
    } else {
        $this->session->set_flashdata('error', 'No se pudo deshabilitar el usuario.');
    }

    redirect('usuarios/mostrar', 'refresh');
}

public function listapdf()
{
    if($this->session->userdata('rol') == '1')
    { 
        $lista = $this->usuario_model->listaUsuarios();
        $lista = $lista->result();

        $this->pdf = new Pdf();
        $this->pdf->AddPage();
        $this->pdf->AliasNbPages();
        $this->pdf->SetTitle("Lista de usuarios");
        $this->pdf->SetLeftMargin(15);
        $this->pdf->SetRightMargin(15);
        $this->pdf->SetFillColor(210,210,210);
        $this->pdf->SetFont('Arial','B',11);
        $this->pdf->Cell(30);
        $this->pdf->Cell(120,10,'LISTA DE USUARIOS',0,0,'C',1);

        $this->pdf->Ln(10);
        $this->pdf->SetFont('Arial','',9);
        $num = 1;
        foreach ($lista as $row) {
            $nombres = $row->nombres;
            $apellidoPaterno = $row->apellidoPaterno;
            $apellidoMaterno = $row->apellidoMaterno;
            $rol = $row->rol;
            $this->pdf->Cell(7,5,$num,'TBLR',0,'L',0);
            $this->pdf->Cell(50,5,$nombres,'TBLR',0,'L',0);
            $this->pdf->Cell(30,5,$apellidoPaterno,'TBLR',0,'L',0);
            $this->pdf->Cell(30,5,$apellidoMaterno,'TBLR',0,'L',0);
            $this->pdf->Cell(30,5,$rol,'TBLR',0,'L',0);
            $this->pdf->Ln(5);
            $num++;
        }

        $this->pdf->Output("listausuarios.pdf","I");
    }
    else
    {
        redirect('usuarios/panel', 'refresh');
    }
}

public function historial() {
    $this->_verificar_sesion();

    $idUsuario = $this->session->userdata('idUsuario');

    // Obtener historial de préstamos
    $data['historial_prestamos'] = $this->prestamo_model->obtener_historial_prestamos($idUsuario);

    // Obtener historial de reservas
    $data['historial_reservas'] = $this->reserva_model->obtener_historial_reservas($idUsuario);

    // Cargar la vista
    $this->load->view('inc/header');
    $this->load->view('admin/historial', $data);
    $this->load->view('inc/footer');
}
/*public function cambiar_password() {
    // Asegúrate de que el usuario esté logueado
    if (!$this->session->userdata('login')) {
        redirect('login');
    }

    $this->load->view('usuario/cambiar_password');
}*/

public function procesar_cambio_password() {
    $nueva_password = $this->input->post('nueva_password');
    $confirmar_password = $this->input->post('confirmar_password');

    if ($nueva_password !== $confirmar_password) {
        $this->session->set_flashdata('error', 'Las contraseñas no coinciden.');
        redirect('usuario/cambiar_password');
    }

    $idUsuario = $this->session->userdata('idUsuario');
    if ($this->usuario_model->actualizar_password($idUsuario, $nueva_password)) {
        $this->session->set_flashdata('success', 'Contraseña actualizada con éxito.');
        redirect('usuario/perfil');
    } else {
        $this->session->set_flashdata('error', 'Hubo un error al actualizar la contraseña.');
        redirect('usuario/cambiar_password');
    }
}
private function _validar_password($password) {
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
        return false;
    }
    return true;
}

public function registrar() {
    $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[USUARIO.email]');
    $this->form_validation->set_rules('password', 'Contraseña', 'required|callback__validar_password');

    if ($this->form_validation->run() == FALSE) {
        $this->load->view('usuarios/registro');
    } else {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $token = bin2hex(random_bytes(16));

        $datos = [
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'token_verificacion' => $token,
            'fecha_token' => date('Y-m-d H:i:s'),
            'verificado' => 0,
            'rol' => 'lector'
        ];

        if ($this->usuario_model->registrar_usuario($datos)) {
            $this->_enviar_email_verificacion($email, $token);
            $this->session->set_flashdata('mensaje', 'Registro exitoso. Por favor, verifica tu email.');
            redirect('usuarios/validar');
        } else {
            $this->session->set_flashdata('error', 'Error en el registro. Inténtalo de nuevo.');
            redirect('usuarios/registrar');
        }
    }
}

private function _enviar_email_verificacion($email, $token)
{
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

    $this->email->from('quirozmolinamaritza@gmail.com', 'hemeroteca');
    $this->email->to($email);
    $this->email->subject('Verifica tu cuenta');
    $this->email->message('Por favor, haz clic en el siguiente enlace para verificar tu cuenta: ' . site_url('usuarios/verificar/' . $token));

    if (!$this->email->send()) 
    {
        // Log del error
        log_message('error', 'Error al enviar correo de verificación: ' . $this->email->print_debugger());
        return false;
    }
    else 
    {
        log_message('info', 'Correo de verificación enviado a: ' . $email);
        return true;
    }
}

public function verificar($token) {
    if ($this->usuario_model->verificar_cuenta($token)) {
        $this->session->set_flashdata('mensaje', 'Cuenta verificada con éxito. Ya puedes iniciar sesión.');
    } else {
        $this->session->set_flashdata('error', 'Token inválido o expirado.');
    }
    redirect('usuarios/validar');
}

public function cambiar_password() {
    if (!$this->session->userdata('login')) {
        redirect('usuarios/validar');
    }

    $this->form_validation->set_rules('nueva_password', 'Nueva Contraseña', 'required|callback__validar_password');
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
    $this->email->from('quirozmolinamaritza@gmail.com', 'hemeroteca');
    $this->email->to($email);
    $this->email->subject('Confirmación de cambio de contraseña');
    $this->email->message('Tu contraseña ha sido cambiada exitosamente. Si no realizaste este cambio, por favor contacta con soporte inmediatamente.');
    $this->email->send();
}

public function reenviar_verificacion()
{
    $email = $this->session->userdata('email_temp');
    if (!$email) {
        redirect('usuarios/index');
    }

    $usuario = $this->usuario_model->obtener_por_email($email);
    if ($usuario && $usuario->verificado == 0) {
        $token = bin2hex(random_bytes(16));
        $this->usuario_model->actualizar_token_verificacion($usuario->idUsuario, $token);
        $this->_enviar_email_verificacion($email, $token);
        $this->session->set_flashdata('mensaje', 'Se ha reenviado el correo de verificación.');
    } else {
        $this->session->set_flashdata('error', 'No se pudo reenviar el correo de verificación.');
    }
    redirect('usuarios/index');
}
public function recuperar_contrasena()
{
    // Cargar la vista de recuperación de contraseña
    $this->load->view('lector/recuperar_contrasena');
}

public function registro()
{
    $this->load->library('form_validation');

    $this->form_validation->set_rules('nombres', 'Nombres', 'required');
    $this->form_validation->set_rules('apellidoPaterno', 'Apellido Paterno', 'required');
    $this->form_validation->set_rules('carnet', 'Carnet', 'required|is_unique[USUARIO.carnet]');
    $this->form_validation->set_rules('profesion', 'Profesión', 'required');
    $this->form_validation->set_rules('sexo', 'Sexo', 'required');
    $this->form_validation->set_rules('email', 'Correo electrónico', 'required|valid_email|is_unique[USUARIO.email]');
    $this->form_validation->set_rules('username', 'Nombre de usuario', 'required|is_unique[USUARIO.username]');
    $this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[6]');

    if ($this->form_validation->run() == FALSE) {
        // Si la validación falla, volver a mostrar el formulario
        $this->load->view('lector/registro');
    } else {
        // Si la validación es exitosa, registrar al usuario
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
            'token_verificacion' => bin2hex(random_bytes(16)),
            'fecha_token' => date('Y-m-d H:i:s')
        );

        /*if ($this->usuario_model->registrar_usuario($data)) {
            $this->_enviar_email_verificacion($data['email'], $data['token_verificacion']);
            $this->session->set_flashdata('mensaje', 'Registro exitoso. Por favor, verifica tu correo electrónico.');
            redirect('usuarios/index');
        } else {
            $this->session->set_flashdata('error', 'Hubo un problema al registrar el usuario. Inténtalo de nuevo.');
            redirect('usuarios/registro');
        }
        */
        if ($this->usuario_model->registrar_usuario($data)) {
            if ($this->_enviar_email_verificacion($data['email'], $data['token_verificacion'])) {
                $this->session->set_flashdata('mensaje', 'Registro exitoso. Por favor, verifica tu correo electrónico.');
            } else {
                $this->session->set_flashdata('error', 'Registro exitoso, pero hubo un problema al enviar el correo de verificación. Por favor, contacta al administrador.');
            }
            redirect('usuarios/index');
        } else {
            $this->session->set_flashdata('error', 'Hubo un problema al registrar el usuario. Inténtalo de nuevo.');
            redirect('usuarios/registro');
        }
    }
}


/*private function _enviar_email_verificacion($email, $token)
{
    $this->load->library('email');

    $this->email->from('tu_correo@tudominio.com', 'Nombre de tu aplicación');
    $this->email->to($email);
    $this->email->subject('Verifica tu cuenta');
    $this->email->message('Por favor, haz clic en el siguiente enlace para verificar tu cuenta: ' . site_url('usuarios/verificar/' . $token));

    $this->email->send();
}*/
public function test_email() {
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
        'newline' => "\r\n",
        'wordwrap' => TRUE,
        'smtp_timeout' => 30,
        'smtp_keepalive' => TRUE,
        'smtp_debug' => 2,
        'smtp_ssl_verify_peer' => FALSE,
        'smtp_ssl_verify_peer_name' => FALSE,
        'smtp_ssl_allow_self_signed' => TRUE
    );

    $this->email->initialize($config);

    $this->email->from('quirozmolinamaritza@gmail.com', 'Hemeroteca "Jose Antonio Arze"');
    $this->email->to('quiroz.maritza.871@gmail.com');
    $this->email->subject('Prueba de Correo ' . date('Y-m-d H:i:s'));
    $this->email->message('Este es un correo de prueba desde la aplicación. Hora: ' . date('Y-m-d H:i:s'));

    echo "Configuración cargada:<br>";
    echo "<pre>" . print_r($config, true) . "</pre>";

    echo "Intentando enviar correo...<br>";

    if ($this->email->send(false)) {
        echo "Correo enviado correctamente<br>";
        echo "<pre>" . $this->email->print_debugger() . "</pre>";
    } else {
        echo "Error al enviar el correo<br>";
        echo "<pre>" . $this->email->print_debugger() . "</pre>";
    }
}
}