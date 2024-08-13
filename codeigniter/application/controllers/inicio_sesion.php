<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio_sesion extends CI_Controller {

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
        $password = md5($_POST['password']);

        $consulta = $this->usuario_model->validar($login, $password);

        if ($consulta->num_rows() > 0)
        {
            // Validación efectiva
            foreach ($consulta->result() as $row)
            {
                $this->session->set_userdata('idusuario', $row->idUsuario);
                $this->session->set_userdata('login', $row->login);
                $this->session->set_userdata('tipo', $row->tipo);
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
            if ($this->session->userdata('tipo') == 'admin')
            {
                // El usuario ya está logueado
                redirect('usuario/index', 'refresh');
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
}
