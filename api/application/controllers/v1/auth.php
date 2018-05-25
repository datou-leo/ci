<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function login()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->users_model->check_auth_client();
			if($check_auth_client == true){
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                if(!$username || !$password ){
                    json_output(204,array('status'=>204,'message'=>'No username or password'));
                }else{
                    $response = $this->users_model->login($username,$password);
                    json_output($response['status'],$response);
                }
			}
		}
	}

	public function logout()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => '请求错误'));
		} else {
			$check_auth_client = $this->users_model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->users_model->logout();
				json_output($response['status'],$response);
			}
		}
	}
	
}
