<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pais extends CI_Controller {

	public function mostrar()
	{
		$lista=$this->pais_model->listapaises();
		$data['country']=$lista;
		//$data['usr']=$usuarios;
		//$data['not']=$notific;
		$this->load->view('inc/head');
		$this->load->view('inc/menu');
		$this->load->view('listapais',$data);
		$this->load->view('inc/footer');
		$this->load->view('inc/pie');
	}
    public function agregar()
	{
		$this->load->view('inc/head');
		$this->load->view('inc/menu');
		$this->load->view('formulariopais');
		$this->load->view('inc/footer');
		$this->load->view('inc/pie');
	}
    public function agregarbd()
	{
		$data['pais']=strtoupper($_POST['pais']);
        $data['superficie']=$_POST['superficie'];
        $data['poblacion']=$_POST['poblacion'];
    
		$this->pais_model->agregarpaises($data);
        redirect('pais/mostrar','refresh');
	}
    public function eliminarbd()
	{
		$idpais=$_POST['idpais'];
		$this->pais_model->eliminarpais($idpais);
        redirect('pais/mostrar','refresh');
	}
	public function modificar()
	{
		$idpais=$_POST['idpais'];
		//echo $idestudiante
		$data['infopais']=$this->pais_model->recuperarpais($idpais);
		$this->load->view('inc/head');
		$this->load->view('inc/menu');
		$this->load->view('formmodificarpais',$data);
		$this->load->view('inc/footer');
		$this->load->view('inc/pie');
	}
	public function modificarbd()
	{
		$idpais=$_POST['idpais'];
		$data['pais']=strtoupper($_POST['pais']);
        $data['superficie']=$_POST['superficie'];
        $data['poblacion']=$_POST['poblacion'];

		$this->pais_model->modificarpais($idpais,$data);
		redirect('pais/mostrar','refresh');
	}
}
