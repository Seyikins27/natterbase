<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logger
{
   private $CI;

   function __construct()
   {
     $this->CI =& get_instance();
     $this->CI->load->model('Log_model');
   }

   function log($user=null, $url, $status)
   {
       $method=$_SERVER['REQUEST_METHOD'];
         $data=array(
             'user_id'=>$user,
             'method'=>$method,
             'payload'=>$url,
             'status'=>$status
         );

         $this->CI->Log_model->add($data);
   }
   //generate a log of all users activities
   function report($method, $payload, $status, $time)
   {
       echo "User ".$this->method_verb($method)." ".$payload." and returned with  ".$this->status_verb($status)." at ".$time;
   }
    
   //output verbs for status codes
   function status_verb($status)
   {
      $verb="";
      switch($status)
      {
        case 200:
        $verb="Successful";
        break;

         case 201:
         $verb="Successfuly Added";
         break; 

         case 204:
         $verb="No result to display";
         break; 

         case 302:
         $verb="Resource not Found";
         break; 
        
         case 304:
         $verb="Resource not Updated";
         break; 

         case 400:
         $verb="Wrong Request";
         break; 

         case 401:
         $verb="User is not authorised";
         break; 

         case 403:
         $verb="User is forbidden access";
         break; 

         case 404:
         $verb="The content was not found";
         break; 

         case 405:
         $verb="The method is not allowed";
         break; 

         case 501:
         $verb="The request could not be processed";
         break; 
        
      }
      return $verb;

   }
   //convert methods to verbs
   function method_verb($method)
   {
       $verb;
       switch($method)
       {
           case "POST":
           $verb="Added";
           break;

           case "GET":
           $verb="Retrieved";
           break;

           case "PUT":
           $verb="updated";
           break;

           case "DELETE":
           $verb="deleted";
           break;

           default:
           $verb="viewed";
           break;
       }
       return $verb;
   }

}