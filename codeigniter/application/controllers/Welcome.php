<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$lista=$this->estudiante_model->listaestudiantes();
		$data['alumnos']=$lista;
		$this->load->view('welcome_message',$data);
	}
	public function pruebabd()
	{
		$query=$this->db->get('estudiantes');
		$exceconsulta=$query->result();
		print_r($exceconsulta);
	}
	public function curso()
	{
		$lista=$this->estudiante_model->listaestudiantes();
		$data['alumnos']=$lista;
		//$data['usr']=$usuarios;
		//$data['not']=$notific;
		$this->load->view('inc/head');
		$this->load->view('inc/menu');
		$this->load->view('welcome_message',$data);
		$this->load->view('inc/footer');
		$this->load->view('inc/pie');
	
		
	}
}
