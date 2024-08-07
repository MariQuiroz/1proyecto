<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model {
    
    public function __construct() {
        $this->load->database();
    }

    public function get_usuarios() {
        $query = $this->db->get('usuarios');
        return $query->result_array();
    }

    public function get_usuario($id) {
        $query = $this->db->get_where('usuarios', array('id' => $id));
        return $query->row_array();
    }

    public function create_usuario($data) {
        $data['contraseña'] = sha1($data['contraseña']);
        $data['fecha_creacion'] = date('Y-m-d H:i:s');
        $data['ultima_actualizacion'] = date('Y-m-d H:i:s');
        return $this->db->insert('usuarios', $data);
    }

    public function update_usuario($id, $data) {
        if (isset($data['contraseña'])) {
            $data['contraseña'] = sha1($data['contraseña']);
        }
        $data['ultima_actualizacion'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('usuarios', $data);
    }

    public function delete_usuario($id) {
        // Eliminación lógica
        $data = array(
            'estado' => 0,
            'ultima_actualizacion' => date('Y-m-d H:i:s')
        );
        $this->db->where('id', $id);
        return $this->db->update('usuarios', $data);
    }

    public function authenticate($correo, $password) {
        $this->db->where('correo', $correo);
        $this->db->where('estado', 1); // Solo usuarios activos
        $query = $this->db->get('usuarios');
        $usuario = $query->row_array();

        if ($usuario && $usuario['contraseña'] === sha1($password)) {
            return $usuario;
        } else {
            return false;
        }
    }

    public function get_by_correo($correo) {
        $query = $this->db->get_where('usuarios', array('correo' => $correo));
        return $query->row_array();
    }

    public function update_password($id, $new_password) {
        $data = array(
            'contraseña' => sha1($new_password),
            'ultima_actualizacion' => date('Y-m-d H:i:s')
        );
        $this->db->where('id', $id);
        return $this->db->update('usuarios', $data);
    }
}
