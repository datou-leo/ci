<?php
class Password extends CI_Controller
{
	var $rule_post = array(
		array(
			'field' => 'oldpass',
			'label' => 'lang:oldpass',
			'rules' => 'required|md5|callback_check_oldpass'),
		array(
			'field' => 'password',
			'label' => 'lang:password',
			'rules' => 'required|min_length[6]'),
		array(
			'field' => 'passconf',
			'label' => 'lang:passconf',
			'rules' => 'required|matches[password]'),
	);

	function check_oldpass($md5)
	{
		$this->load->database();
		$this->db->select('password');
		$q = $this->db->get_where("admin_user"
			,array('account'=>$this->backauth->get_account()));
		$q = $q->row_array();

		if(empty($q['password']) || strcmp($q['password'],$md5))
		{
			$this->form_validation->set_message('check_oldpass'
				,$this->lang->line('oldpass_incorrect'));
			return false;
		}
		return true;
	}

    function Password()
    {
        parent::__construct();
        $this->load->language("admin/password");
    }

	function index()
	{
		$this->backview->view("ajax/password",get_defined_vars());
	}

	function post()
	{
		$backward = 'index';
		$this->load->library('form_validation',$this->rule_post);
		if(!$this->form_validation->run()){
			return $this->$backward();
		}

		$this->load->database();
		$account = $this->backauth->get_account();
		$newpass = md5(set_value('password'));
		$result  = $this->db->update("admin_user",
			array('password'=>$newpass),
			array('account'=>$account));
		if(!$result)
		{
			$this->form_validation->set_error('password','update_failure');
			return $this->$backward();
		}

		$this->backview->success($this->lang->line('success'));
	}
}