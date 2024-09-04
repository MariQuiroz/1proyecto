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

    public function panel()
    {
        if($this->session->userdata('login'))
        {
            if($this->session->userdata('rol') == 'administrador')
            {
                redirect('usuarios/mostrar','refresh');
            }
            else
            {
                redirect('usuarios/lector','refresh');
            }
        }
        else
        {
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
            $this->load->view('inc/menu',$data);
            //$this->load->view('lista', $data);
            
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
        $this->load->view('registrarform');
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
        $this->load->view('panelguest', $data);
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
    $this->load->view('formulario');
    $this->load->view('inc/footer');
}

public function agregarbd()
{

    $this->_verificar_sesion();

    $data['nombres'] =strtoupper($_POST['nombres']);
    $data['apellidoPaterno'] = strtoupper($_POST['apellidoPaterno']);
    $data['apellidoMaterno'] = strtoupper($_POST['apellidoMaterno']);
    $data['carnet'] = strtoupper($_POST['carnet']);
    $data['profesion'] = strtoupper($_POST['profesion']);
    $data['fechaNacimiento'] = $_POST['fechaNacimiento'];
    $data['sexo'] = strtoupper($_POST['sexo']);
    $data['email'] = $_POST['email'];
    $data['username'] = strtolower($_POST['username']);
    $data['password'] =password_hash($_POST['password'], PASSWORD_DEFAULT);
    $data['rol'] = strtoupper($_POST['rol']);

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
    $this->load->view('formulariomodificar', $data);
    $this->load->view('inc/footer');
}

public function modificarbd()
{
    $this->_verificar_sesion();

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
        'rol' => strtoupper($this->input->post('rol'))
    );

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

    $idUsuario = $_POST['idUsuario'];
    $data['estado'] = 0;

    $this->usuario_model->modificarUsuario($idUsuario, $data);
    redirect('usuarios/mostrar', 'refresh');
}

public function deshabilitados()
{
    $lista = $this->usuario_model->listaUsuariosDeshabilitados();
    $data['usuarios'] = $lista;
    
    $this->load->view('inc/header');
    $this->load->view('listadeshabilitados', $data);
    $this->load->view('inc/footer');
}

public function habilitarbd()
{
    $this->_verificar_sesion();

    $idUsuario = $_POST['idUsuario'];
    $data['estado'] = 1;

    $this->usuario_model->modificarUsuario($idUsuario, $data);
    redirect('usuarios/deshabilitados', 'refresh');
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
}