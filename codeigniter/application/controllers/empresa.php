<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empresa extends CI_Controller {

	public function index()
	{
		$this->load->view('inc/head');
		$this->load->view('inc/menu');
		$this->load->view('inicio');
		$this->load->view('inc/footer');
		$this->load->view('inc/pie');
	}
    public function historia()
	{
		$this->load->view('historia');
	}
    public function catalogo()
	{
		$this->load->view('inc/head');
		$this->load->view('inc/menu');
		$this->load->view('productos');
		$this->load->view('inc/footer');
		$this->load->view('inc/pie');
	}
    public function contactos()
	{
		$this->load->view('inc/head');
		$this->load->view('inc/menu');
		$this->load->view('contactos');
		$this->load->view('inc/footer');
		$this->load->view('inc/pie');
	}
	public function integracion()
	{
		$this->load->view('base');
	}
}
