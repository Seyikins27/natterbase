<?php
 defined('BASEPATH') or exit('No Direct Script access allowed');
 use Restserver\Libraries\REST_Controller;
 
 require(APPPATH.'/libraries/REST_Controller.php');
 require(APPPATH.'/libraries/Format.php');

  class Activity extends REST_Controller
  {
      public $is_valid_token="";
	  function __construct()
	  {
		  parent:: __construct();
		  $this->load->helper(array('form', 'url'));
		  $this->load->library(array('upload','form_validation', 'Logger', 'Authorization_Token','pagination'));
          $this->load->model(array('Log_model'));
         
          
          header("Access-Control-Allow-Origin: *");
          $this->load->library('Authorization_Token');

          /**
           * User Token Validation
           */
          $this->is_valid_token = $this->authorization_token->validateToken();
          if (!empty($this->is_valid_token) AND $this->is_valid_token['status'] === TRUE)
          {
            $this->Log_model->user_id=$this->is_valid_token['data']->id;
            $config['base_url'] = base_url().'activities';
            $config['total_rows'] = count($this->Log_model->count_activities());
            $config['per_page'] = 10;
            $config["uri_segment"] = 1;
            $this->pagination->initialize($config); 
          }
        else {
         $this->logger->log("",  $this->uri->uri_string(), REST_Controller::HTTP_NOT_FOUND);
         $this->response(['status' => FALSE, 'message' => $this->is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
         exit;
         }
      }

      function get_get($offset=0)
      {
          $id=$this->is_valid_token['data']->id;
          $limit=10;
          $activities=$this->Log_model->get($limit, $offset);
          if(!empty($activities)){
            $this->logger->log($this->is_valid_token['data']->id,  $this->uri->uri_string(), REST_Controller::HTTP_OK);

            for($i=0; $i<count($activities); $i++)
            {
                
              $this->logger->report($activities[$i]->method, $activities[$i]->payload, $activities[$i]->status, $activities[$i]->created_at);
              echo"</br>";

            }
          
		   
          }
          else {
            $this->logger->log($this->is_valid_token['data']->id,  $this->uri->uri_string(), REST_Controller::HTTP_NO_CONTENT);
            $error=[
                'status'=>REST_Controller::HTTP_NO_CONTENT,
                'message'=>'No activity Log for User'
            ];
            $this->response($error ,REST_Controller::HTTP_NO_CONTENT);
          }
         
      }
    }
	  