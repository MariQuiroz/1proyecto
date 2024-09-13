<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model {

    public function obtener_por_username($username)
    {
        $this->db->select('*');
        $this->db->from('USUARIO');
        $this->db->where('username', $username);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }

    public function registrarUsuario($data)
    {
        // Asegúrate de hashear la contraseña antes de guardarla
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->db->insert('USUARIO', $data);
    }
		public function listaUsuarios()
    {
        $this->db->select('*');
        $this->db->from('USUARIO');
        $this->db->where('estado', 1);
        return $this->db->get();
    }


    public function agregarUsuario($data)
    {
        $this->db->insert('USUARIO', $data);
    }

    public function eliminarUsuario($idUsuario)
    {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->delete('USUARIO');
    }

    public function recuperarUsuario($idUsuario)
    {
        $this->db->select('*');
        $this->db->from('USUARIO');
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->get();
    }

    public function modificarUsuario($idUsuario, $data)
    {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->update('USUARIO', $data);
    }
    public function modificarUsuarioRestringido($idUsuario, $data)
{
    // Lista de campos permitidos para actualizar
    $campos_permitidos = [
        'nombres', 'apellidoPaterno', 'apellidoMaterno', 'carnet', 
        'profesion', 'fechaNacimiento', 'sexo', 'email', 'usuarioSesion'
    ];

    // Filtrar $data para incluir solo los campos permitidos
    $data_filtrada = array_intersect_key($data, array_flip($campos_permitidos));

    // Depuración
    log_message('debug', 'Datos a actualizar: ' . print_r($data_filtrada, true));

    $this->db->where('idUsuario', $idUsuario);
    return $this->db->update('USUARIO', $data_filtrada);
}
    public function listaUsuariosDeshabilitados()
    {
        $this->db->select('*');
        $this->db->from('USUARIO');
        $this->db->where('estado', 0);
        return $this->db->get();
    }
    public function contar_usuarios() {
        return $this->db->count_all('USUARIO');
    }
    public function obtener_usuarios_activos() {
        $this->db->select('idUsuario, nombres, apellidoPaterno');
        $this->db->from('USUARIO');
        $this->db->where('estado', 1); // Asumiendo que 1 es el estado para usuarios activos
        return $this->db->get()->result();
    }
}
