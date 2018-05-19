<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller
{
	function index()
	{
		// get account info
		$this->load->database();
		$admin = $this->db->get_where('admin_user',array('account'=>$this->backauth->get_account()));
		$admin = $admin->row();
		if(isset($admin->role)){
			$admin->role=$this->backauth->auth_caption[$admin->role];
		}

		// default password notice?
		if('backend' == $admin->account && '7fef6171469e80d32c0559f88b377245' == $admin->password)
		{
			$default_password_notice = 1;
		}

		// database backup within one week?
		$this->db->select("value");
		$q = $this->db->get_where('config',array('category'=>'admin','config'=>'backup'));
		$q = $q->row();
		$q = intval($q->value);
		if($this->db->dbdriver == 'mysql' || $this->db->dbdriver=='mysqli')
		{
    		if($q<10)
    		{
    			$default_databkup_notice = 2;
    		}else if(time()-$q > 3600*24*7){
    			$default_databkup_notice = 1;
    		} else {
    			$databkup_timestamp      = $q;
    		}
		}

		if(isset($_SERVER['HTTP_HOST']))
		{
			$server_domain = $_SERVER['HTTP_HOST'];
		} else {
			$server_domain = $this->input->ip_address();
		}

		$sys_zlib        = function_exists('gzclose');
		$sys_safe_mode   = (boolean)ini_get('safe_mode');
		$sys_socket      = function_exists('fsockopen');
		$sys_max_upload  = ini_get('upload_max_filesize');
		$sys_charset     = $this->config->item('charset');
		$server_name     = $this->input->server('SERVER_NAME');
		$server_ip       = gethostbyname($server_name);
		$server_htmlize  = $this->config->item('htmlize');
		$server_log      =($this->config->item('log_threshold')<1?true:false);
		$server_timezone = $this->config->item('timezone');

		/*
		$server_domain_encode=$this->uri->encode($server_domain);

		//get alexa image url
		$alexa_image_url=$this->js_alexa_image($server_domain,1,'t',300,190);
		*/

		$this->load->view("welcome",get_defined_vars());
	}
}