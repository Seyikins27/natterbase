<?php
  defined('BASEPATH') or exit('No Direct Script access allowed');
  
  class Log_model extends CI_Model
  {
	  private $table_name="log";
	  public $user_id;
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

	  function count_activities()
	  {
		$this->db->select('method, payload, status, created_at');
		$this->db->from($this->table_name);
		$this->db->where(array('user_id'=>$this->user_id));
		$query=$this->db->get();
		$result=$query->result();
		return $result;
	  }
	  
	  function get($limit, $offset)
	  {
		$this->db->limit($limit, $offset);
		$this->db->select('method, payload, status, created_at');
		$this->db->from($this->table_name);
		$this->db->order_by('log_id','ASC');
		$this->db->where(array('user_id'=>$this->user_id));
		$query=$this->db->get();
		$result=$query->result();
		return $result;
	  }
	  
	  function update()
	  {
		  
	  }
	  
	  function delete()
	  {
		  
	  }
	  
	 
  }