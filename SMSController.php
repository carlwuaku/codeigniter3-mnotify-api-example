<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of SMSController - contains logic for sending of sms. Can be integrated into an existing controller file or as a standalone controller
 * @author carl
 */
class SMSController extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  /*
  * this is the route called by an incoming request. e.g. https://yoursite.com/smscontroller/send_sms 
  * request type is post here but can be changed to a get request simply by changing the $this->input->post(...) to $this->input->get(...)
  * the required data are the message and the phone numbers to receive the message. multiple phone numbers must be separated by commas.
  * the controller 
  */ 
  public function send_sms()
  {
    
        $msg = $this->input->post("message");
        $recipients = explode(",", $this->input->post("recipients"));//returns an array of strings
        //separate receipients by comma
        $this->load->model("Sms_model");
        $res = "";
        foreach ($recipients as $r) {
          $this->sendSingleSms($msg, $r);
          
        }
        $response['status'] = "1";
        $response['data'] = "$res";
    
    echo json_encode($response);
  }

	/**
     * send a single sms to a number. 
     * @param string $message
     * @param string $recipient +233 is prepended to the last 9 digits
     * @return string
     */
    private function sendSingleSms($message, $recipient)
  {
 
        $this->load->model("Sms_model");
        $r = "+233" . substr($recipient, -9);
        $res = $this->send($r, $message);
        return $res;
  }
  
  /**
     * call the sms api. the api_key and sender_id must be defined.
     * @param string $to the phone number to send to
     * @param string $message the content of the sms as a string. new lines should be specified as \n
     * @return string a json string containing the status of the request.
     */
  
  private function send($to, $message) {
        $key = "XXXXXXXXXXXXXXXXXXXXXXX";  // put your own API Key here
        $sender_id = "My SenderId"; //11 Characters maximum. this id must have been registered and approved by Mnotify

//encode the message
        $msg = urlencode($message);

//prepare your url
        $url = "https://apps.mnotify.net/smsapi?"
                . "key=$key"
                . "&to=$to"
                . "&msg=$msg"
                . "&sender_id=$sender_id";
        $response = file_get_contents($url);
        return $response;
    }
   

}
