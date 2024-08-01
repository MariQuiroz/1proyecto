<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pais_model extends CI_Model {

	public function listapaises()
	{
		$this->db->select('*');
        $this->db->from('paises');
        return $this->db->get(); //devuelve el resultado
	}
    public function agregarpaises($data)
	{
		$this->db->insert('paises',$data);
	}
    public function eliminarpais($idpais)
	{
		$this->db->where('idPais',$idpais);
        $this->db->delete('paises');
	}
	public function recuperarpais($idpais)
	{
		$this->db->select('*');
		$this->db->from('paises');
		$this->db->where('idPais',$idpais);
		return $this->db->get();
	}
	public function modificarpais($idpais,$data)
	{
		$this->db->where('idPais',$idpais);
		$this->db->update('paises',$data);
	}

}
