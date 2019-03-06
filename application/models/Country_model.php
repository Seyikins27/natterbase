<?php
  defined('BASEPATH') or exit('No Direct Script access allowed');
  
  class Country_model extends CI_Model
  {
	  private $table_name="countries";
	  function __construct()
	  {
		  parent:: __construct();
	  }
	  
	  function add($data)
	  {
		$this->db->insert($this->table_name, $data);
        if($this->db->affected_rows()>=1)
         {
             return true;
         }
         else
         {
             return false;
         }
	  }
	  
	  function get($id=null)
	  {
		$result="";
		if(empty($id))
		{
		$this->db->select('name, continent, created_at');
        $this->db->from($this->table_name);
        $query=$this->db->get();
        $result=$query->result();
		}
		else
		{
		  $this->db->select('name, continent, created_at');
		  $this->db->from($this->table_name);
		  $this->db->where(array('id'=>$id));
		  $query=$this->db->get();
		  $result=$query->result();
		}
		return $result;
	  }
	  
	  function update($id, $data)
	  {
		$this->db->update($this->table_name, $data);
		$this->db->where(array('id'=>$id));
        if($this->db->affected_rows())
        {
            return true;
        }
        else
        {
            return false;
        }  
	  }
	  
	  function delete($id)
	  {
		 $this->db->delete($this->table_name, array('id'=>$id));
         if($this->db->affected_rows()>=1)
         {
             return true;
         }
         else
         {
             return false;
         }
	  }
	  
	 
  }