<?php
defined('BASEPATH') OR exit('No se permite acceso directo al script');

class Home extends CI_Controller {
    
    public function index() {
        if ($this->session->userdata('login')) {
            redirect('usuarios/welcome');
        }
        $this->load->view('pagina/inc/header');
        $this->load->view('pagina/inc/navbar');
        $this->load->view('pagina/pagina_web');
        $this->load->view('pagina/inc/footer');
    }

    public function proceso() {
        $this->load->view('pagina/inc/header');
        $this->load->view('pagina/inc/navbar');
        $this->load->view('pagina/proceso');
        $this->load->view('pagina/inc/footer');
    }
    public function sobre_nosotros() {
        $this->load->view('pagina/inc/header');
        $this->load->view('pagina/inc/navbar');
        $this->load->view('pagina/sobre_nosotros');
        $this->load->view('pagina/inc/footer');
    }
    public function contacto() {
        $this->load->view('pagina/inc/header');
        $this->load->view('pagina/inc/navbar');
        $this->load->view('pagina/contacto');
        $this->load->view('pagina/inc/footer');
    }
    public function catalogo() {
        $this->load->model('Publicacion_model');
        
        $data['publicaciones'] = $this->Publicacion_model->obtener_catalogo_publico();
        $data['tipos'] = $this->Publicacion_model->obtener_tipos_catalogo();
        
        $this->load->view('pagina/inc/header');
        $this->load->view('pagina/inc/navbar');
        $this->load->view('pagina/catalogo', $data);
        $this->load->view('pagina/inc/footer');
    }
}