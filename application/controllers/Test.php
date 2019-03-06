<?php
 defined('BASEPATH') or exit('NO direct Script access allowed');

 class Test extends CI_Controller
 {
     function __construct()
     {
         parent::__construct();
         $this->load->library('unit_test');
     }
     
     function Index()
     {
         echo "Using unit test class";
     }
 }