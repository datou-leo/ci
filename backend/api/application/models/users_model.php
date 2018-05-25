<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key       = "api";

    public function check_auth_client(){
        $client_service = $this->input->get_request_header('Client-service', TRUE);
        $auth_key  = $this->input->get_request_header('Auth-key', TRUE);


        if($client_service == $this->client_service && $auth_key == $this->auth_key){
            return true;
        } else {
            return json_output(401,array('status' => 401,'message' => '未授权.'));
        }
    }

    public function login($username,$password)
    {
        $q  = $this->db->select('password,id')->from('users')->where('username',$username)->get()->row();
        if($q == ""){
            return array('status' => 204,'message' => '用户名或者密码错误');
        } else {
            $table_password = $q->password;
            $id              = $q->id;
            if (md5($password) == $table_password) {
               $last_login = time();
               $token =  md5(rand());
               $expired_at = strtotime('+12 hours');
               $this->db->trans_start();
               $this->db->where('id',$id)->update('users',array('last_login' => $last_login));
               $this->db->insert('users_auth',array('type_id' => $id,'token' => $token,'expired_at' => $expired_at,'timeline'=>time()));
               if ($this->db->trans_status() === FALSE){
                  $this->db->trans_rollback();
                  return array('status' => 500,'message' => '服务器错误');
               } else {
                  $this->db->trans_commit();
                  return array('status' => 200,'message' => '登录成功','id' => $id, 'token' => $token);
               }
            } else {
               return array('status' => 204,'message' => '密码错误');
            }
        }
    }

    public function logout()
    {
        $users_id  = $this->input->get_request_header('User-id', TRUE);
        $token     = $this->input->get_request_header('Authorization', TRUE);
        $this->db->where('type_id',$users_id)->where('token',$token)->delete('users_auth');
        return array('status' => 200,'message' => '已成功退出登录.');
    }

    public function auth()
    {
        $users_id  = $this->input->get_request_header('User-id', TRUE);
        $token     = $this->input->get_request_header('Authorization', TRUE);
        $q  = $this->db->select('expired_at')->from('users_auth')->where('type_id',$users_id)->where('token',$token)->get()->row();
        if(empty($q)){
            return json_output(401,array('status' => 401,'message' => '未授权.'));
        } else {
            if($q->expired_at < time()){
                return json_output(401,array('status' => 401,'message' => '登录会话已过期'));
            } else {
                $updated_at = time();
                $expired_at = strtotime('+12 hours');
                $this->db->where('type_id',$users_id)->where('token',$token)->update('users_auth',array('expired_at' => $expired_at,'updated_at' => $updated_at));
                return array('status' => 200,'message' => '授权成功.');
            }
        }
    }



}
