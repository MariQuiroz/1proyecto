<?php
defined('BASEPATH') OR exit('No se permite acceso directo al script');

class Usuarios extends CI_Controller {

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

    public function validar()
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

public function registrar()
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
}

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
}