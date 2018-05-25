<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Article extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('article_model');
        $check_auth_client = $this->users_model->check_auth_client();
		if($check_auth_client != true){
			die($this->output->get_output());
		}

    }
    //http://local.ci.com/api/v1/article/index
    //Client-service:frontend-client
    //Auth-key:api
    //User-id:1
    //Authorization:05d6a571b0e03c1bc8a46d1d5afd00cd
	public function index()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET'){
			json_output(400,array('status' => 400,'message' => '请求错误'));
		} else {
			$check_auth_client = $this->users_model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->users_model->auth();
		        if($response['status'] == 200){
		        	$resp = $this->article_model->article_all_data();
	    			json_output($response['status'],$resp);
		        }
			}
		}
	}
    //http://local.ci.com/api/v1/article/detail/2
    //Client-service:frontend-client
    //Auth-key:api
    //User-id:1
    //Authorization:05d6a571b0e03c1bc8a46d1d5afd00cd
	public function detail($id)
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET' || $this->uri->segment(4) == '' || is_numeric($this->uri->segment(4)) == FALSE){
			json_output(400,array('status' => 400,'message' => '请求错误'));
		} else {
			$check_auth_client = $this->users_model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->users_model->auth();
		        if($response['status'] == 200){
		        	$resp = $this->article_model->article_detail_data($id);
					json_output($response['status'],$resp);
		        }
			}
		}
	}
    //http://local.ci.com/api/v1/article/create
    //Client-service:frontend-client
    //Auth-key:api
    //User-id:1
    //Authorization:05d6a571b0e03c1bc8a46d1d5afd00cd
    //title:第3篇文章标题
    //author:chendongpu
	public function create()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => '请求错误'));
		} else {
			$check_auth_client = $this->users_model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->users_model->auth();
		        $respStatus = $response['status'];
		        if($response['status'] == 200){
					$params = $this->input->post();
					if ($params['title'] == "" || $params['author'] == "") {
						$respStatus = 400;
						$resp = array('status' => 400,'message' =>  '标题内容作者不能为空');
					} else {
		        		$resp = $this->article_model->article_create_data($params);
					}
					json_output($respStatus,$resp);
		        }
			}
		}
	}
    //http://local.ci.com/api/v1/article/update/2
    //Client-service:frontend-client
    //Auth-key:api
    //User-id:1
    //Authorization:05d6a571b0e03c1bc8a46d1d5afd00cd
    //title:第一篇文章标题
    //author:chendongpu
	public function update($id)
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST' || $this->uri->segment(4) == '' || is_numeric($this->uri->segment(4)) == FALSE){
			json_output(400,array('status' => 400,'message' => '请求错误'));
		} else {
			$check_auth_client = $this->users_model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->users_model->auth();
		        $respStatus = $response['status'];
		        if($response['status'] == 200){
					$params = $this->input->post();
					if ($params['title'] == "" || $params['author'] == "") {
						$respStatus = 400;
						$resp = array('status' => 400,'message' =>  '标题内容作者不能为空');
					} else {
		        		$resp = $this->article_model->article_update_data($id,$params);
					}
					json_output($respStatus,$resp);
		        }
			}
		}
	}

    //http://local.ci.com/api/v1/article/delete/3
    //Client-service:frontend-client
    //Auth-key:api
    //User-id:1
    //Authorization:05d6a571b0e03c1bc8a46d1d5afd00cd
	public function delete($id)
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'GET' || $this->uri->segment(4) == '' || is_numeric($this->uri->segment(4)) == FALSE){
			json_output(400,array('status' => 400,'message' => '请求错误'));
		} else {
			$check_auth_client = $this->users_model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->users_model->auth();
		        if($response['status'] == 200){
		        	$resp = $this->article_model->article_delete_data($id);
					json_output($response['status'],$resp);
		        }
			}
		}
	}

}
