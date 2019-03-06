<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inputs
{
   public $array;

   function __construct()
   {
     $this->CI =& get_instance();
   
    }
    
    function get_key()
    {
        $arr=explode('=',$this->array);
        return $arr[0];
    }

    function get_value()
    {
        $arr=explode('=',$this->array);
        return $arr[1];
    }
}
