<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model {

    public function obtener_por_username($username)
    {
        return $this->db->get_where('USUARIO', ['username' => $username])->row();
    }

    public function registrarUsuario($data)
    {
        // Asegurarse de que la contraseña esté hasheada
        if (isset($data['password']) && !password_get_info($data['password'])['algo']) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        // Establecer valores por defecto si no están presentes
        $data['verificado'] = $data['verificado'] ?? 0;
        $data['estado'] = $data['estado'] ?? 1;
        $data['fechaCreacion'] = date('Y-m-d H:i:s');
        $data['cambioPasswordRequerido'] = $data['cambioPasswordRequerido'] ?? 1;

        $this->db->insert('USUARIO', $data);
        return $this->db->insert_id();
    }

    public function listaUsuarios()
{
    $this->db->select('idUsuario, nombres, apellidoPaterno, email, rol, profesion, estado');
    $this->db->where('estado', 1);
    return $this->db->get('USUARIO')->result();
}


    public function obtener_usuario($idUsuario)
    {
        return $this->db->get_where('USUARIO', ['idUsuario' => $idUsuario])->row();
    }

    public function modificarUsuario($idUsuario, $data)
    {
        $data['fechaActualizacion'] = date('Y-m-d H:i:s');
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $data);
    }

    public function listaUsuariosDeshabilitados()
{
    $this->db->where('estado', 0);
    return $this->db->get('USUARIO')->result();
}
    public function contar_usuarios()
    {
        return $this->db->count_all('USUARIO');
    }

    public function verificar_cuenta($token)
    {
        $this->db->where('tokenVerificacion', $token);
        $this->db->where('fechaToken >', date('Y-m-d H:i:s', strtotime('-24 hours')));
        $usuario = $this->db->get('USUARIO')->row();
    
        if ($usuario) {
            $this->db->where('idUsuario', $usuario->idUsuario);
            $this->db->update('USUARIO', [
                'verificado' => 1,
                'tokenVerificacion' => null,
                'fechaToken' => null,
                'fechaActualizacion' => date('Y-m-d H:i:s')
            ]);
            return true;
        }
        return false;
    }

    public function actualizar_password($idUsuario, $nueva_password, $requerir_cambio = false)
    {
        $data = [
            'password' => password_hash($nueva_password, PASSWORD_DEFAULT),
            'cambioPasswordRequerido' => $requerir_cambio ? 1 : 0,
            'fechaActualizacion' => date('Y-m-d H:i:s')
        ];
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $data);
    }

    public function cambiar_estado_usuario($idUsuario, $estado)
    {
        $data = [
            'estado' => $estado,
            'fechaActualizacion' => date('Y-m-d H:i:s')
        ];
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $data);
    }

    public function obtener_por_email($email)
    {
        return $this->db->get_where('USUARIO', ['email' => $email])->row();
    }

    public function actualizar_token_verificacion($idUsuario, $token)
    {
        $data = [
            'tokenVerificacion' => $token,
            'fechaToken' => date('Y-m-d H:i:s'),
            'fechaActualizacion' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('idUsuario', $idUsuario);
        $this->db->update('USUARIO', $data);
        
        return $this->db->affected_rows() > 0;
    }

    public function actualizar_preferencias_notificacion($idUsuario, $preferencias)
    {
        $data = [
            'preferenciasNotificacion' => json_encode($preferencias),
            'fechaActualizacion' => date('Y-m-d H:i:s')
        ];
        $this->db->where('idUsuario', $idUsuario);
        return $this->db->update('USUARIO', $data);
    }

    public function obtener_preferencias_notificacion($idUsuario)
    {
        $this->db->select('preferenciasNotificacion');
        $this->db->where('idUsuario', $idUsuario);
        $result = $this->db->get('USUARIO')->row();
        
        if ($result && $result->preferenciasNotificacion) {
            return json_decode($result->preferenciasNotificacion, true);
        }
        
        return []; // Retorna un array vacío si no hay preferencias
    }
    public function recuperarUsuario($idUsuario)
{
    $this->db->where('idUsuario', $idUsuario);
    $query = $this->db->get('USUARIO');
    return $query->row();
}

}
