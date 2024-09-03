<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Carrera_model extends CI_Model {

	public function listaCarreras()
	{
		$this->db->select('*'); // select *
		$this->db->from('carreras'); //tabla
		return $this->db->get(); //devolución del resultado de la consulta
	}

	public function inscribirestudiante($idCarrera,$data)
	{
		$this->db->trans_start();
		$this->db->insert('estudiantes',$data);
		$idEstudiante=$this->db->insert_id();

		$data2['idCarrera']=$idCarrera;
		$data2['idEstudiante']=$idEstudiante;
		$this->db->insert('inscripcion',$data2);

		$this->db->trans_complete();

		if($this->db->trans_status()===FALSE)
		{
			return false;
		}
	}
}
