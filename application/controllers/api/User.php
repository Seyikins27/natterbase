<?php
 defined('BASEPATH') or exit('No Direct Script access allowed');
  use Restserver\Libraries\REST_Controller;
  
  require(APPPATH.'/libraries/REST_Controller.php');
  require(APPPATH.'/libraries/Format.php');

  class User extends REST_Controller
  {
	  function __construct()
	  {
		  parent:: __construct();
		  $this->load->helper(array('form', 'url'));
			$this->load->library(array('upload','form_validation', 'encryption', 'Logger','Authorization_Token'));
			$config=array(
				'driver'=>'OpenSSL',
				'cipher' => 'aes-256',
				'mode' => 'ctr'
);
$this->encryption->initialize($config);
		  $this->load->model(array('User_model'));
	  }
	  
	  function register_post()
	  {
			header("Access-Control-Allow-Origin: *");
		  $this->form_validation->set_rules('firstname', 'Firstname', 'required|min_length[3]');
		  $this->form_validation->set_rules('lastname', 'Lastname', 'required|min_length[3]');
		  $this->form_validation->set_rules('dob', 'Date of Birth', 'required');
		  $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		  $this->form_validation->set_rules('username', 'Username', 'required|min_length[3]|is_unique[user.email]', array(
				'is_unique'=> 'This %s already exists.'
));
		  $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
		  $this->form_validation->set_rules('confpass', 'Password Confirmation', 'required|matches[password]');
		  
		  if($this->form_validation->run()==FALSE)
          {
			//if there are validation errors, json response is sent
						$errors=validation_errors();
						echo $errors;
						$this->set_response($errors, REST_Controller::HTTP_BAD_REQUEST);
						$this->logger->log("",  $this->uri->uri_string(), REST_Controller::HTTP_BAD_REQUEST);
						exit;
         }
     //if no validation errors are detected then the transaction should continue
		 else
		 {
			 //array of data to be inserted into the database: array('database_field'=>'form_field_value')
			 $password=$this->encryption->encrypt($this->input->post('confpass'));
           $data=array(
            'firstname'=>$this->input->post('firstname'),
						'lastname'=>$this->input->post('lastname'),
						'dob'=>$this->input->post('dob'),
						'email'=>$this->input->post('email'),
						'username'=>$this->input->post('username'),
						'password'=>$password,
						'created_at'=>date('Y-m-d  H:i:s')
            );
		   $success=$this->User_model->add($data);
			 // check if record inserted successfully
			
		   if($success)
		   {
			 // if database transaction is successful json response is sent
			 $this->response(['Successful'], REST_Controller::HTTP_CREATED);
			 $this->logger->log($success,   $this->uri->uri_string(), REST_Controller::HTTP_CREATED);
		   }
		   else
		   {
				$this->logger->log("",  $this->uri->uri_string(), REST_Controller::HTTP_BAD_REQUEST);
				$this->response('Not Successful', REST_Controller::HTTP_BAD_REQUEST);
		   }
      }
		}
		
		  
	  function auth_post()
	  {
			$login_data='';
			if(isset($_POST['email']))
			{
				$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
				$login_data=array('email'=>$this->input->post('email'));
			}
			else if(isset($_POST['username']))
			{
				$this->form_validation->set_rules('username', 'Username', 'required');
				$login_data=array('username'=>$this->input->post('username'));
			}
		
	
		$this->form_validation->set_rules('password', 'password', 'required');

		if($this->form_validation->run()==FALSE)
          {
			//if there are validation errors, json response is sent
			$errors=validation_errors();
			$message=[
				'status'=>REST_Controller::HTTP_BAD_REQUEST,
				'message'=>$errors,
				];
				$this->logger->log("",  $this->uri->uri_string(), REST_Controller::HTTP_BAD_REQUEST);
				$this->response($errors, REST_Controller::HTTP_BAD_REQUEST);
			  exit;
			}
			else {
				$password=$this->encryption->encrypt($this->input->post('password'));
				$dec=$this->encryption->decrypt($password);
			
				$login_result=$this->User_model->auth($login_data);
				 if($login_result)
					{
					$dec_pass=$this->encryption->decrypt($login_result[0]->password);
					if($dec_pass==$dec)
					 {
						$token_data=array(
						 'id'=>$login_result[0]->id,
						 'firstname'=>$login_result[0]->firstname,
						 'email'=>$login_result[0]->email,
						 'created_at'=>$login_result[0]->created_at,
						 'time'=>time()
						  );
						 
						 $user_token = $this->authorization_token->generateToken($token_data);
						 $message=[
							'status'=>true,
							'message'=>'successful',
							'token'=>$user_token
							];
							
							$this->logger->log($login_result[0]->id,   $this->uri->uri_string(), REST_Controller::HTTP_OK);
							$this->response($message, REST_Controller::HTTP_OK);
						}
						else
						{
							$error=[
								'status'=>REST_Controller::HTTP_UNAUTHORIZED,
								'message'=>'Wrong Password'
							];
							$this->logger->log("",  $this->uri->uri_string(), REST_Controller::HTTP_UNAUTHORIZED);
							$this->response($error, REST_Controller::HTTP_UNAUTHORIZED);
						}
					}
					else
					{
						$error=[
							'status'=>REST_Controller::HTTP_UNAUTHORIZED,
							'message'=>'Wrong Username/email or Password'
						];
						$this->logger->log("",  $this->uri->uri_string(), REST_Controller::HTTP_UNAUTHORIZED);
						$this->response($error, REST_Controller::HTTP_UNAUTHORIZED);
					}
			}
	
	  }
	  
  }
  