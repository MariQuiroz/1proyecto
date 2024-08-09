<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Estudiante extends CI_Controller {
	public function demo(){
		$lista=$this->estudiante_model->listaestudiantes();
		$data['alumnos']=$lista;

		$this->load->view('inc/vistaslte/head');
		$this->load->view('inc/vistaslte/menu');
		$this->load->view('inc/vistaslte/aside');
		$this->load->view('inc/vistaslte/test');
		$this->load->view('inc/vistaslte/footer');
	}

	public function demo1(){
		$lista=$this->estudiante_model->listaestudiantes();
		$data['alumnos']=$lista;

		$this->load->view('inc/vistaslte/head');
		//$this->load->view('inc/vistaslte/menu');
		$this->load->view('datatable');
		$this->load->view('inc/vistaslte/test');
		$this->load->view('inc/vistaslte/footer');
	}

	public function curso()
	{
		$lista=$this->estudiante_model->listaclientes();
		$data['personas']=$lista;
		//$data['usr']=$usuarios;
		//$data['not']=$notific;
		$this->load->view('inc/head');
		$this->load->view('inc/menu');
		$this->load->view('lista',$data);
		$this->load->view('inc/footer');
		$this->load->view('inc/pie');
	}
	public function deshabilitados()
	{
		$lista=$this->estudiante_model->listadeshabilitados();
		$data['alumnos']=$lista;
		//$data['usr']=$usuarios;
		//$data['not']=$notific;
		$this->load->view('inc/head');
		$this->load->view('inc/menu');
		$this->load->view('deshabilitados',$data);
		$this->load->view('inc/footer');
		$this->load->view('inc/pie');
	}
    public function agregar()
	{
		$this->load->view('inc/head');
		$this->load->view('inc/menu');
		$this->load->view('formulario');
		$this->load->view('inc/footer');
		$this->load->view('inc/pie');
	}
    public function agregarbd()
	{
		$data['nombre']=strtoupper($_POST['nombre']);
        $data['primerApellido']=strtoupper($_POST['apellido1']);
        $data['segundoApellido']=strtoupper($_POST['apellido2']);
        $data['nota']=$_POST['nota'];

		$this->estudiante_model->agregarestudiante($data);
        redirect('estudiante/demo','refresh');
	}
    public function eliminarbd()
	{
		$idestudiante=$_POST['idestudiante'];
		$this->estudiante_model->eliminarestudiante($idestudiante);
        redirect('estudiante/demo','refresh');
	}
	public function modificar()
	{
		$idestudiante=$_POST['idestudiante'];
		//echo $idestudiante
		$data['infoestudiante']=$this->estudiante_model->recuperarestudiante($idestudiante);
		$this->load->view('inc/head');
		$this->load->view('inc/menu');
		$this->load->view('formmodificar',$data);
		$this->load->view('inc/footer');
		$this->load->view('inc/pie');
	}
	public function modificarbd()
	{
		$idestudiante=$_POST['idestudiante'];
		$data['nombre']=strtoupper($_POST['nombre']);
        $data['primerApellido']=strtoupper($_POST['apellido1']);
        $data['segundoApellido']=strtoupper($_POST['apellido2']);
        $data['nota']=$_POST['nota'];

		$this->estudiante_model->modificarestudiante($idestudiante,$data);
		redirect('estudiante/demo','refresh');
	}
	public function deshabilitarbd()
	{
		$idestudiante=$_POST['idestudiante'];
		$data['habilitado']='0';
       
		$this->estudiante_model->modificarestudiante($idestudiante,$data);
		redirect('estudiante/curso','refresh');
	}
	public function habilitarbd()
	{
		$idestudiante=$_POST['idestudiante'];
		$data['habilitado']='1';
       
		$this->estudiante_model->modificarestudiante($idestudiante,$data);
		redirect('estudiante/deshabilitados','refresh');
	}


}
