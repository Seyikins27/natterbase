<?php
 defined('BASEPATH') or exit('No Direct Script access allowed');
 use Restserver\Libraries\REST_Controller;
 
 require(APPPATH.'/libraries/REST_Controller.php');
 require(APPPATH.'/libraries/Format.php');

  class Country extends REST_Controller
  {
      public $is_valid_token="";
	  function __construct()
	  {
		  parent:: __construct();
		  $this->load->helper(array('form', 'url'));
		  $this->load->library(array('upload','form_validation', 'Logger', 'Authorization_Token'));
          $this->load->model(array('Country_model'));
          
          header("Access-Control-Allow-Origin: *");
          $this->load->library('Authorization_Token');

          /**
           * User Token Validation
           */
          $this->is_valid_token = $this->authorization_token->validateToken();
          if (!empty($this->is_valid_token) AND $this->is_valid_token['status'] === TRUE)
          {
          }
        else {
         $this->logger->log("",  $this->uri->uri_string(), REST_Controller::HTTP_NOT_FOUND);
         $this->response(['status' => FALSE, 'message' => $this->is_valid_token['message'] ], REST_Controller::HTTP_NOT_FOUND);
         exit;
         }
	  }
	  
	  function add_post()
	  {

		  $this->form_validation->set_rules('name', 'Country', 'required|min_length[3]');
		  $this->form_validation->set_rules('continent', 'Continent', 'required|min_length[3]');
		  if($this->form_validation->run()==FALSE)
          {
			//if there are validation errors, json response is sent
			$errors=validation_errors();
            echo $errors;
            $this->logger->log($this->is_valid_token['data']->id,  $this->uri->uri_string(), REST_Controller::HTTP_BAD_REQUEST);
			$this->response($errors, REST_Controller::HTTP_BAD_REQUEST);
			exit;
         }
     //if no validation errors are detected then the transaction should continue
		 else
		 {
			 //array of data to be inserted into the database: array('database_field'=>'form_field_value')
           $data=array(
           'name'=>$this->input->post('name'),
		   'continent'=>$this->input->post('continent'),
           'created_at'=>date('Y-m-d: H:i:s')
            );
		   $success=$this->Country_model->add($data);
		   // check if record inserted successfully
		   if($success)
		   {
             // if database transaction is successful json response is sent
             $this->logger->log($this->is_valid_token['data']->id,  $this->uri->uri_string(), REST_Controller::HTTP_CREATED);
             $this->response(['Successful'], REST_Controller::HTTP_CREATED);
            
		   }
		   else
		   {
              $this->logger->log($this->is_valid_token['data']->id,  $this->uri->uri_string(), REST_Controller::HTTP_BAD_REQUEST);
			  $this->response('Not Successful', REST_Controller::HTTP_BAD_REQUEST);
		   }
        }
      
      }
      
      function get_get($id=null)
      {
        $countries='';
        if(!empty($id))
        {
            $data=$this->Country_model->get($id);
            if(!empty($data))
            {
               $countries=[$data];
            }
            else
            {
                $this->logger->log($this->is_valid_token['data']->id,  $this->uri->uri_string(), REST_Controller::HTTP_NO_CONTENT);
                $error=[
                    'status'=>REST_Controller::HTTP_NO_CONTENT,
                    'message'=>'Country does not exist'
                ];
                $this->response($error ,REST_Controller::HTTP_NO_CONTENT);
                exit;
            }
        }
        else
        {
            $data=$this->Country_model->get();
            if(!empty($data))
            {
                $countries=[$data];
            }
            else
            {
                $this->logger->log($this->is_valid_token['data']->id,  $this->uri->uri_string(), REST_Controller::HTTP_NO_CONTENT);
                $error=[
                    'status'=>REST_Controller::HTTP_NO_CONTENT,
                    'message'=>'Country does not exist'
                ];
                $this->response($error ,REST_Controller::HTTP_NO_CONTENT);
                exit;
            }
        }
        $this->logger->log($this->is_valid_token['data']->id,  $this->uri->uri_string(), REST_Controller::HTTP_OK);
        $this->response([$countries], REST_Controller::HTTP_OK);
      }

      function update_put($id)
      {
        
         $this->put('name');
         $this->put('continent');
        
         //this checks if there are any validation errors
        
             //array of data to be inserted into the database: array('database_field'=>'form_field_value')
             $data=array(
             'name'=>$this->put('name', TRUE),
             'continent'=>$this->put('continent', TRUE)
             );
             $success=$this->Country_model->update($id,$data);
             // check if record updated successfully
             if($success==true)
             {
             // if database transaction is successful json response is sent
             $this->logger->log($this->is_valid_token['data']->id,  $this->uri->uri_string(), REST_Controller::HTTP_OK);
                $this->response(['Update Successful'], REST_Controller::HTTP_OK);
                 exit;
             }
             else
             {
                $this->logger->log($this->is_valid_token['data']->id,  $this->uri->uri_string(), REST_Controller::HTTP_NOT_MODIFIED);
                 $this->response(['Not Successful'], REST_Controller::HTTP_NOT_MODIFIED);
                 exit;
             }
         }
   
 
      function delete_delete($id)
      {
          //check if username is not empty
          if(!empty($id))
          {
              $delete=$this->Country_model->delete($id);
             
              // check if record deleted successfully
              if($delete)
              {
              // if database transaction is successful json response is sent
               $this->logger->log($this->is_valid_token['data']->id,  $this->uri->uri_string(), REST_Controller::HTTP_OK);
                [$this->response(['message'=>'Country Deleted'], REST_Controller::HTTP_OK)];
                exit;
              }
              else
              {
                $this->logger->log($this->is_valid_token['data']->id,  $this->uri->uri_string(), REST_Controller::HTTP_NOT_IMPLEMENTED);
                 [$this->response(['message'=>'Invalid Parameter'], REST_Controller::HTTP_NOT_IMPLEMENTED)];
                 exit;
              }
          }
          else
          {
            $this->logger->log($this->is_valid_token['data']->id,  $this->uri->uri_string(), HTTP_NOT_ACCEPTABLE);
             $this->response(['message'=>'Nothing to Delete'], REST_Controller::HTTP_NOT_ACCEPTABLE);
             exit;
          }
      }
  }
  