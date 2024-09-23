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
    public function verificar_email($email) {
        $this->db->where('email', $email);
        $query = $this->db->get('USUARIO');
        return $query->row();
    }

    public function guardar_token_reset($idUsuario, $token) {
        $data = array(
            'token_reset' => $token,
            'token_reset_expiracion' => date('Y-m-d H:i:s', strtotime('+1 hour'))
        );
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $data);
    }

    public function verificar_token_reset($token) {
        $this->db->where('token_reset', $token);
        $this->db->where('token_reset_expiracion >', date('Y-m-d H:i:s'));
        $query = $this->db->get('USUARIO');
        return $query->row();
    }

    public function actualizar_password($idUsuario, $nueva_password) {
        $data = array(
            'password' => password_hash($nueva_password, PASSWORD_DEFAULT),
            'token_reset' => NULL,
            'token_reset_expiracion' => NULL
        );
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $data);
    }

    public function email_existe($email) {
        $this->db->where('email', $email);
        $query = $this->db->get('USUARIO');
        return $query->num_rows() > 0;
    }

   /* public function registrar_usuario($datos) {
        $datos['token_verificacion'] = bin2hex(random_bytes(16));
        $datos['fecha_token'] = date('Y-m-d H:i:s');
        $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
        $datos['token_verificacion'] = bin2hex(random_bytes(16)); // Genera un token único
        $datos['verificado'] = 0; // 0 = no verificado, 1 = verificado
        return $this->db->insert('USUARIO', $datos);
    }*/
    public function registrar_usuario($data)
{
    // Eliminar cualquier campo vacío para evitar problemas con campos que no pueden ser NULL
    foreach ($data as $key => $value) {
        if ($value === '') {
            unset($data[$key]);
        }
    }

    return $this->db->insert('USUARIO', $data);
}

    /*public function verificar_cuenta($token) {
        $this->db->where('token_verificacion', $token);
        $this->db->update('USUARIO', ['verificado' => 1, 'token_verificacion' => null]);
        return $this->db->affected_rows() > 0;
    }*/
    public function verificar_cuenta($token) {
        $this->db->where('token_verificacion', $token);
        $this->db->where('fecha_token >', date('Y-m-d H:i:s', strtotime('-24 hours')));
        $usuario = $this->db->get('USUARIO')->row();
    
        if ($usuario) {
            $this->db->where('idUsuario', $usuario->idUsuario);
            $this->db->update('USUARIO', [
                'verificado' => 1,
                'token_verificacion' => null,
                'fecha_token' => null
            ]);
            return true;
        }
        return false;
    }
    public function registrar($datos) {
        return $this->db->insert('USUARIO', $datos);
    }

   /* public function verificar_cuenta($token) {
        $this->db->where('token_verificacion', $token);
        $this->db->where('verificado', 0);
        $usuario = $this->db->get('USUARIO')->row();

        if ($usuario) {
            $this->db->where('idUsuario', $usuario->idUsuario);
            return $this->db->update('USUARIO', ['verificado' => 1, 'token_verificacion' => null]);
        }

        return false;
    }*/
    public function obtener_por_email($email)
{
    $this->db->select('*');
    $this->db->from('USUARIO');
    $this->db->where('email', $email);
    $query = $this->db->get();
    
    if ($query->num_rows() > 0) {
        return $query->row();
    }
    return null;
}

public function actualizar_token_verificacion($idUsuario, $token)
{
    $data = array(
        'token_verificacion' => $token,
        'fecha_token' => date('Y-m-d H:i:s')
    );
    
    $this->db->where('idUsuario', $idUsuario);
    $this->db->update('USUARIO', $data);
    
    return $this->db->affected_rows() > 0;
}


}
