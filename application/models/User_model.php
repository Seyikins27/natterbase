<?php
  defined('BASEPATH') or exit('No Direct Script access allowed');
  
  class User_model extends CI_Model
  {
	  private $table_name="user";
	  function __construct()
	  {
		  parent:: __construct();
	  }
	  
	  function add($data)
	  {
		 $this->db->insert($this->table_name, $data);
		 return $this->db->insert_id();
        
	  }
	  
	  function auth($data)
	  {
		$query=$this->db->get_where('user', $data);
		return $query->result();
	  }
	 
  }