<?php
defined('BASEPATH') OR exit('No se permite acceso directo al script');

class Usuarios extends CI_Controller {
    public function index()
    {
        $data['msg'] = $this->uri->segment(3);

        if($this->session->userdata('login'))
        {
            redirect('usuarios/panel','refresh');
        }
        else
        {
            $this->load->view('inc/header');
            $this->load->view('login', $data);
            $this->load->view('inc/footer');
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
            $this->load->view('lista', $data);
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
    $data['nombres'] = $_POST['nombres'];
    $data['apellidoPaterno'] = $_POST['apellidoPaterno'];
    $data['apellidoMaterno'] = $_POST['apellidoMaterno'];
    $data['carnet'] = $_POST['carnet'];
    $data['telefono'] = $_POST['telefono'];
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
    if($this->session->userdata('rol') == 'lector')
    { 
        $this->load->view('inc/header');
        $this->load->view('panelguest');
        $this->load->view('inc/footer');
    }
}

public function agregar()
{
    $this->load->view('inc/header');
    $this->load->view('formulario');
    $this->load->view('inc/footer');
}

public function agregarbd()
{
    $data['nombres'] = $_POST['nombres'];
    $data['apellidoPaterno'] = $_POST['apellidoPaterno'];
    $data['apellidoMaterno'] = $_POST['apellidoMaterno'];
    $data['carnet'] = $_POST['carnet'];
    $data['telefono'] = $_POST['telefono'];
    $data['fechaNacimiento'] = $_POST['fechaNacimiento'];
    $data['sexo'] = $_POST['sexo'];
    $data['email'] = $_POST['email'];
    $data['username'] = $_POST['username'];
    $data['password'] =password_hash($_POST['password'], PASSWORD_DEFAULT);
    $data['rol'] = $_POST['rol'];

    $this->usuario_model->agregarUsuario($data);
    redirect('usuarios/mostrar', 'refresh');
}

public function eliminarbd()
{
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
    $idUsuario = $_POST['idUsuario'];
    $data['nombres'] = $_POST['nombres'];
    $data['apellidoPaterno'] = $_POST['apellidoPaterno'];
    $data['apellidoMaterno'] = $_POST['apellidoMaterno'];
    $data['carnet'] = $_POST['carnet'];
    $data['telefono'] = $_POST['telefono'];
    $data['fechaNacimiento'] = $_POST['fechaNacimiento'];
    $data['sexo'] = $_POST['sexo'];
    $data['email'] = $_POST['email'];
    $data['username'] = $_POST['username'];
    $data['rol'] = $_POST['rol'];

    $nombrearchivo = $idUsuario . ".jpg";
    $config['upload_path'] = './uploads/';
    $config['file_name'] = $nombrearchivo;
    $direccion = "./uploads/" . $nombrearchivo;
    if(file_exists($direccion))
    {
        unlink($direccion);
    }
    $config['allowed_types'] = 'jpg';
    $this->load->library('upload', $config);

    if(!$this->upload->do_upload())
    {
        $data['error'] = $this->upload->display_errors();
    }
    else
    {
        $data['foto'] = $nombrearchivo;
    }

    $this->usuario_model->modificarUsuario($idUsuario, $data);
    $this->upload->data();
    redirect('usuarios/mostrar', 'refresh');
}

public function deshabilitarbd()
{
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