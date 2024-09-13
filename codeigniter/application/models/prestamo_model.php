<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prestamo_model extends CI_Model {


    public function listar_prestamos($limite = NULL, $offset = NULL) {
        $this->db->select('p.*, u.nombres, u.apellidoPaterno, pub.titulo');
        $this->db->from('PRESTAMO p');
        $this->db->join('USUARIO u', 'p.idUsuario = u.idUsuario');
        $this->db->join('PUBLICACION pub', 'p.idPublicacion = pub.idPublicacion');
        $this->db->where('p.estado', 1);
        if ($limite && $offset) {
            $this->db->limit($limite, $offset);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function obtener_prestamo($idPrestamo) {
        $this->db->where('idPrestamo', $idPrestamo);
        $query = $this->db->get('PRESTAMO');
        return $query->row();
    }

    public function agregar_prestamo($data) {
        return $this->db->insert('PRESTAMO', $data);
    }

    public function actualizar_prestamo($idPrestamo, $data) {
        $this->db->where('idPrestamo', $idPrestamo);
        return $this->db->update('PRESTAMO', $data);
    }

    public function prestamos_por_usuario($idUsuario) {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->where('estado', 1);
        $query = $this->db->get('PRESTAMO');
        return $query->result();
    }

    public function prestamos_vencidos() {
        $this->db->where('fechaDevolucionEsperada <', date('Y-m-d'));
        $this->db->where('fechaDevolucionReal IS NULL');
        $this->db->where('estado', 1);
        $query = $this->db->get('PRESTAMO');
        return $query->result();
    }

    public function registrar_devolucion($idPrestamo) {
        $data = array(
            'fechaDevolucionReal' => date('Y-m-d H:i:s'),
            'estado' => 2 // 2 = devuelto
        );
        $this->db->where('idPrestamo', $idPrestamo);
        return $this->db->update('PRESTAMO', $data);
    }
    public function contar_prestamos_activos() {
        $this->db->where('estado', 1);
        return $this->db->count_all_results('PRESTAMO');
    }

    public function obtener_prestamos_vencidos() {
        $this->db->select('p.*, u.nombres, u.apellidoPaterno, pub.titulo');
        $this->db->from('PRESTAMO p');
        $this->db->join('USUARIO u', 'p.idUsuario = u.idUsuario');
        $this->db->join('PUBLICACION pub', 'p.idPublicacion = pub.idPublicacion');
        $this->db->where('p.fechaDevolucionEsperada <', date('Y-m-d'));
        $this->db->where('p.estado', 1);
        return $this->db->get()->result();
    }

    public function contar_prestamos_activos_usuario($idUsuario) {
        $this->db->where('idUsuario', $idUsuario);
        $this->db->where('estado', 1);
        return $this->db->count_all_results('PRESTAMO');
    }

    public function obtener_proximas_devoluciones_usuario($idUsuario) {
        $this->db->select('p.*, pub.titulo');
        $this->db->from('PRESTAMO p');
        $this->db->join('PUBLICACION pub', 'p.idPublicacion = pub.idPublicacion');
        $this->db->where('p.idUsuario', $idUsuario);
        $this->db->where('p.estado', 1);
        $this->db->order_by('p.fechaDevolucionEsperada', 'ASC');
        $this->db->limit(5);
        return $this->db->get()->result();
    }
    public function obtener_historial_prestamos($idUsuario) {
        $this->db->select('p.*, pub.titulo');
        $this->db->from('PRESTAMO p');
        $this->db->join('PUBLICACION pub', 'p.idPublicacion = pub.idPublicacion');
        $this->db->where('p.idUsuario', $idUsuario);
        $this->db->order_by('p.fechaPrestamo', 'DESC');
        return $this->db->get()->result();
    }

    public function crear_prestamo_desde_reserva($idReserva, $fechaDevolucionEsperada) {
        $this->db->trans_start(); // Inicio de la transacción

        // Obtener datos de la reserva
        $reserva = $this->db->get_where('RESERVA', ['idReserva' => $idReserva])->row();

        if (!$reserva) {
            $this->db->trans_rollback();
            return false;
        }

        // Actualizar estado de la reserva
        $this->db->where('idReserva', $idReserva);
        $this->db->update('RESERVA', ['estado' => 2]); // 2 = Finalizada

        // Crear nuevo préstamo
        $data_prestamo = [
            'idUsuario' => $reserva->idUsuario,
            'idPublicacion' => $reserva->idPublicacion,
            'fechaPrestamo' => date('Y-m-d H:i:s'),
            'fechaDevolucionEsperada' => $fechaDevolucionEsperada,
            'estado' => 1 // 1 = Activo
        ];
        $this->db->insert('PRESTAMO', $data_prestamo);

        // Actualizar estado de la publicación
        $this->db->where('idPublicacion', $reserva->idPublicacion);
        $this->db->update('PUBLICACION', ['estado' => 2]); // 2 = Prestado

        $this->db->trans_complete(); // Fin de la transacción

        if ($this->db->trans_status() === FALSE) {
            // Si algo salió mal, se hace rollback automáticamente
            return false;
        }

        return true;
    }
}