<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model {

	public function listaUsuarios()
	{
		$this->db->select('*'); // select *
		$this->db->from('usuarios'); // tabla
		$this->db->where('estado', 1);
		return $this->db->get(); // devolución del resultado de la consulta
	}

	public function agregarUsuario($data)
	{
		public function agregarUsuario($data)
{
    // Verificar si el email ya existe
    $this->db->where('email', $data['email']);
    $query = $this->db->get('usuarios');
    
    if ($query->num_rows() > 0) {
        return array('status' => FALSE, 'message' => 'El correo electrónico ya está registrado.');
    }

    // Verificar si el login ya existe
    $this->db->where('login', $data['login']);
    $query = $this->db->get('usuarios');
    
    if ($query->num_rows() > 0) {
        return array('status' => FALSE, 'message' => 'El login ya está en uso.');
    }

    // Agregar el usuario si no existe
    if ($this->db->insert('usuarios', $data)) {
        return array('status' => TRUE, 'message' => 'Usuario agregado exitosamente.');
    } else {
        return array('status' => FALSE, 'message' => 'Error al agregar el usuario.');
    }
}

	}

	public function eliminarUsuario($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('usuarios');
	}

	public function recuperarUsuario($id)
	{
		$this->db->select('*'); // select *
		$this->db->from('usuarios'); // tabla
		$this->db->where('id', $id);
		return $this->db->get(); // devolución del resultado de la consulta
	}

	public function modificarUsuario($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update('usuarios', $data);		
	}

	public function listaUsuariosDeshabilitados()
	{
		$this->db->select('*'); // select *
		$this->db->from('usuarios'); // tabla
		$this->db->where('estado', 0);
		return $this->db->get(); // devolución del resultado de la consulta
	}

	//para el inicio de sesion
	public function validar($login, $password)
	{
		$this->db->select('*');
		$this->db->from('usuarios');
		$this->db->where('login', $login);
		$this->db->where('password', $password);
		return $this->db->get();
	}
}
