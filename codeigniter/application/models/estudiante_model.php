<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Estudiante_model extends CI_Model {

	public function listaestudiantes()
	{
		$this->db->select('*'); // slecet *
		$this->db->from('estudiantes'); //tabla
		$this->db->where('habilitado','1');
		return $this->db->get(); //devolución del resultado de la consulta
	}

	public function agregarestudiante($data)
	{
		$this->db->insert('estudiantes',$data);
	}

	public function eliminarestudiante($idestudiante)
	{
		$this->db->where('idEstudiante',$idestudiante);
		$this->db->delete('estudiantes');
	}

	public function recuperarestudiante($idestudiante)
	{
		$this->db->select('*'); // slecet *
		$this->db->from('estudiantes'); //tabla
		$this->db->where('idEstudiante',$idestudiante);
		return $this->db->get(); //devolución del resultado de la consulta
	}

	public function modificarestudiante($idestudiante,$data)
	{
		$this->db->where('idEstudiante',$idestudiante);
		$this->db->update('estudiantes',$data);		
	}

	public function listaestudiantesdeshabilitados()
	{
		$this->db->select('*'); // slecet *
		$this->db->from('estudiantes'); //tabla
		$this->db->where('habilitado','0');
		return $this->db->get(); //devolución del resultado de la consulta
	}
}
