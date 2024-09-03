<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller {

    //aqui inicia lo del inicio de sesion

	public function index()
    {
        $data['msg'] = $this->uri->segment(3);

        if ($this->session->userdata('login'))
        {
            // El usuario ya está logueado
            redirect('usuario/panel', 'refresh');
        }
        else
        {
            // Usuario no está logueado
            $this->load->view('inc/header');
            $this->load->view('login', $data);
            $this->load->view('inc/footer');
        }
    }

    public function validar()
    {
        $login = $_POST['login'];
        $password = hash('sha256', $_POST['password']);

        $consulta = $this->usuario_model->validar($login, $password);

        if ($consulta->num_rows() > 0)
        {
            // Validación efectiva
            foreach ($consulta->result() as $row)
            {
                $this->session->set_userdata('idUsuario', $row->idUsuario);
                $this->session->set_userdata('login', $row->login);
                $this->session->set_userdata('rol', $row->rol);
                redirect('usuario/panel', 'refresh');
            }
        }
        else
        {
            // No hay validación efectiva y redirigimos a login
            redirect('usuario/index/2', 'refresh');
        }
    }

    public function panel()
    {
        if ($this->session->userdata('login'))
        {
            if ($this->session->userdata('rol') == 1)
            {
                // El usuario ya está logueado
                redirect('usuario/mostrar', 'refresh');
            }
            else
            {
                redirect('usuario/guest', 'refresh');
            }
        }
        else
        {
            // Usuario no está logueado
            redirect('usuario/index/3', 'refresh');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('usuario/index/1', 'refresh');
    }


	public function mostrar()
	{
		if($this->session->userdata('rol')==1)
		{ 
			$lista=$this->usuario_model->listaUsuarios();
			$data['usuarios']=$lista;
			
			$this->load->view('inc/header');
			$this->load->view('lista',$data);
			$this->load->view('inc/footer');
		}
		else
		{
			redirect('usuario/panel','refresh');
		}
	}

	public function guest()
	{
		if($this->session->userdata('rol')==2)
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
    $data['nombre'] = $_POST['nombre'];
    $data['primerApellido'] = $_POST['primerapellido'];
    $data['segundoApellido'] = $_POST['segundoapellido'];
    $data['ci'] = $_POST['ci'];
    $data['domicilio'] = $_POST['domicilio'];
    $data['telefono'] = $_POST['telefono'];
    $data['email'] = $_POST['email'];
    $data['login'] = $_POST['login'];
    $data['password'] = hash('sha256', $_POST['password']);
    $data['rol'] = $_POST['rol'];
    $data['estado'] = 1; // Asignar un valor simple, no un array
    $data['foto'] = null; // Puedes añadir lógica para manejar la foto si es necesario
    
    // Determinar el valor de idUsuario basado en el rol
    switch ($data['rol']) {
        case 1:
            $data['idUsuario'] = 1; // Administrador
            break;
        case 2:
            $data['idUsuario'] = 2; // Encargado
            break;
        case 3:
        default:
            $data['idUsuario'] = 3; // Lector o valor por defecto si el rol no es reconocido
            break;
    }

    $result = $this->usuario_model->agregarUsuario($data);

    if ($result['status']) {
        $this->session->set_flashdata('message', $result['message']);
        redirect('usuario/mostrar', 'refresh');
    } else {
        $this->session->set_flashdata('error', $result['message']);
        redirect('usuario/agregar', 'refresh'); // Redirigir de vuelta al formulario de agregar
    }
}


		

	public function eliminarbd()
	{
		$id=$_POST['id'];
		$this->usuario_model->eliminarUsuario($id);
		redirect('usuario/mostrar','refresh');
	}

	public function modificar()
	{
		$id=$_POST['id'];
		$data['infousuario']=$this->usuario_model->recuperarUsuario($id);

		$this->load->view('inc/header');
		$this->load->view('formulariomodificar',$data);
		$this->load->view('inc/footer');
	}

	public function modificarbd()
{
    $id = $_POST['id'];
    
    // Recopilación de datos
    $data['nombre'] = $_POST['nombre'];
    $data['primerApellido'] = $_POST['primerapellido'];
    $data['segundoApellido'] = $_POST['segundoapellido'];
    $data['ci'] = $_POST['ci'];
    $data['domicilio'] = $_POST['domicilio'];
    $data['telefono'] = $_POST['telefono'];
    $data['email'] = $_POST['email'];
    $data['login'] = $_POST['login'];
    $data['password'] = hash('sha256', $_POST['password']);
    $data['rol'] = $_POST['rol'];
    $data['estado'] = 1; // Mantén el estado activo por defecto
    $nombrearchivo = $id.".jpg";

    // Configuración para la carga del archivo
    $config['upload_path'] = './uploads/';
    $config['file_name'] = $nombrearchivo;
    $config['allowed_types'] = 'jpg';
    $this->load->library('upload', $config);

    // Verificación si existe un archivo anterior y eliminarlo
    $direccion = "./uploads/".$nombrearchivo;
    if (file_exists($direccion)) {
        unlink($direccion);
    }

    // Intento de carga de archivo
    if ($this->upload->do_upload('userfile')) {
        // Si la carga es exitosa, se actualiza el campo 'foto'
        $data['foto'] = $nombrearchivo;
    } else {
        // Si falla, se puede mostrar el error en la vista pero no en la base de datos
        $uploadError = $this->upload->display_errors();
        // Puedes decidir qué hacer con este error, como guardarlo en la sesión o redirigir con el mensaje
    }

    // Actualización de usuario en la base de datos
    $this->usuario_model->modificarUsuario($id, $data);

    // Redirección después de la actualización
    redirect('usuario/mostrar', 'refresh');
}


	public function deshabilitarbd()
	{
		$id=$_POST['id'];
		$data['estado']=0;

		$this->usuario_model->modificarUsuario($id,$data);
		redirect('usuario/mostrar','refresh');
	}

	public function deshabilitados()
	{
		$lista=$this->usuario_model->listaUsuariosDeshabilitados();
		$data['usuarios']=$lista;
		

		$this->load->view('inc/header');
		$this->load->view('listadeshabilitados',$data);
		$this->load->view('inc/footer');
	}

	public function habilitarbd()
	{
		$id=$_POST['id'];
		$data['estado']=1;

		$this->usuario_model->modificarUsuario($id,$data);
		redirect('usuario/deshabilitados','refresh');
	}

	/*public function listapdf()
	{
		if($this->session->userdata('tipo')=='admin')
		{ 
			$lista=$this->usuario_model->listaUsuarios();
			$lista=$lista->result();

			$this->pdf=new Pdf();
			$this->pdf->AddPage();
			$this->pdf->AliasNbPages();
			$this->pdf->SetTitle("Lista de usuarios");
			$this->pdf->SetLeftMargin(15);
			$this->pdf->SetRightMargin(15);
			$this->pdf->SetFillColor(210,210,210);//color fondo
			$this->pdf->SetFont('Arial','B',11);
			$this->pdf->Cell(30);//creamos una celda vacia
			$this->pdf->Cell(120,10,'LISTA DE USUARIOS',0,0,'C',1);
			$this->pdf->Ln(10);//salto de linea para generar el siguiente contenido

			$this->pdf->Cell(9,5,'No.','TBLR',0,'L',0);//estas son las cabeceras de la tabla
			$this->pdf->Cell(50,5,'NOMBRE','TBLR',0,'L',0);
			$this->pdf->Cell(50,5,'PRIMER APELLIDO','TBLR',0,'L',0);
			$this->pdf->Cell(50,5,'SEGUNDO APELLIDO','TBLR',0,'L',0);
			$this->pdf->Cell(15,5,'NOTA','TBLR',0,'L',0);
			$this->pdf->Ln(5);

			$this->pdf->SetFont('Arial','',9);
			$num=1;
			//se cicla la lista
			foreach ($lista as $row) {
				//esto es necesario hacer porque no reconoce la celda este codigo
				$nombre=$row->nombre;
				$primerapellido=$row->primerApellido;
				$segundoapellido=$row->segundoApellido;
				$nota=$row->nota;
				//los tamaños deben ir coincidiendo con los titulares
				$this->pdf->Cell(9,5,$num,'TBLR',0,'L',0);
				$this->pdf->Cell(50,5,$nombre,'TBLR',0,'L',0);
				$this->pdf->Cell(50,5,$primerapellido,'TBLR',0,'L',0);
				$this->pdf->Cell(50,5,$segundoapellido,'TBLR',0,'L',0);
				$this->pdf->Cell(15,5,$nota,'TBLR',0,'L',0);
				$this->pdf->Ln(5);
				$num++;
			}

			$this->pdf->Output("listaUsuarios.pdf","D");
			//la letra D es para la descarga forzosa del documento
			//la letra I para vizualizar el documento a imprimir

		}
		else
		{
			redirect('usuario/panel','refresh');
		}
	}
		*/

	
}