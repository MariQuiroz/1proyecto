
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reserva_model extends CI_Model {

    
    public function listar_reservas($limite = NULL, $offset = NULL) {
        $this->db->select('r.*, u.nombres, u.apellidoPaterno, p.titulo');
        $this->db->from('RESERVA r');
        $this->db->join('USUARIO u', 'r.idUsuario = u.idUsuario');
        $this->db->join('PUBLICACION p', 'r.idPublicacion = p.idPublicacion');
        $this->db->where('r.estado', 1);
        if ($limite && $offset) {
            $this->db->limit($limite, $offset);
        }
        $query = $this->db->get();
        return $query->result();
    }

    /*public function obtener_reserva($idReserva) {
        $this->db->where('idReserva', $idReserva);
        $query = $this->db->get('RESERVA');
        return $query->row();
    }*/

    public function agregar_reserva($data) {
        return $this->db->insert('RESERVA', $data);
    }

    public function actualizar_reserva($idReserva, $data) {
        $this->db->where('idReserva', $idReserva);
        return $this->db->update('RESERVA', $data);
    }

    public function eliminar_reserva($idReserva) {
        $this->db->where('idReserva', $idReserva);
        return $this->db->delete('RESERVA');
    }

    public function reservas_por_usuario($idUsuario) {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->where('estado', 1);
        $query = $this->db->get('RESERVA');
        return $query->result();
    }

    public function verificar_disponibilidad($idPublicacion, $fechaReserva) {
        $this->db->where('idPublicacion', $idPublicacion);
        $this->db->where('fechaReserva', $fechaReserva);
        $this->db->where('estado', 1);
        $query = $this->db->get('RESERVA');
        return $query->num_rows() == 0;
    }
    public function obtener_reservas_usuario($idUsuario) {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->where('estado', 1); // Asumiendo que 1 es el estado para reservas activas
        return $this->db->get('RESERVA')->result();
    }
    public function obtener_historial_reservas($idUsuario) {
        $this->db->select('r.*, pub.titulo');
        $this->db->from('RESERVA r');
        $this->db->join('PUBLICACION pub', 'r.idPublicacion = pub.idPublicacion');
        $this->db->where('r.idUsuario', $idUsuario);
        $this->db->order_by('r.fechaReserva', 'DESC');
        return $this->db->get()->result();
    }

    public function cancelar_reserva($idReserva) {
        return $this->db->update('RESERVA', ['estado' => 3], ['idReserva' => $idReserva]);
    }

    public function obtener_reservas_pendientes() {
        $this->db->select('r.*, u.nombres as nombre_usuario, p.titulo as titulo_publicacion');
        $this->db->from('RESERVA r');
        $this->db->join('USUARIO u', 'r.idUsuario = u.idUsuario');
        $this->db->join('PUBLICACION p', 'r.idPublicacion = p.idPublicacion');
        $this->db->where('r.estado', 1); // Asumiendo que 1 es el estado para reservas pendientes
        return $this->db->get()->result();
    }

    public function obtener_reserva($idReserva) {
        $this->db->select('r.*, u.nombres as nombre_usuario');
        $this->db->from('RESERVA r');
        $this->db->join('USUARIO u', 'r.idUsuario = u.idUsuario');
        $this->db->where('r.idReserva', $idReserva);
        return $this->db->get()->row();
    }
    public function contar_reservas_pendientes_usuario($idUsuario) {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->where('estado', 1); // Asumiendo que 1 es el estado para reservas pendientes
        return $this->db->count_all_results('RESERVA');
    }
}