<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup extends CI_Controller
{
	var $caption;
	var $title;
	var $iframe;

	var $view  = 'setup';
	var $table = 'config';

	var $rule_post = array(
		array(
			'field' => 'title',
			'rules' => 'trim'),
		array(
			'field' => 'weburl',
			'rules' => 'trim'),
	    array(
			'field' => 'icpcode',
			'rules' => 'trim'),
		array(
			'field' => 'keywords',
			'rules' => 'trim'),
		array(
			'field' => 'description',
			'rules' => 'trim'),
		array(
			'field' => 'code',
			'rules' => 'trim'),
		);

	function Setup()
	{
		parent::__construct();
		list($this->caption,$nav,$sub) = $this->backview->menu_caption_index();
		$this->load->vars(array(
			'title'     =>$this->title = strtolower(get_class()),
			'caption'   => $this->caption,
			'nav_index' => $nav,
			'sub_index' => $sub));
	}

	// index.php?ctrl/func/type_id/page/order/each/cond
	function index()
	{
		$this->load->database();
		$q  = $this->db->select("config,value")->get_where("config",array("category"=>"site"))->result_array();
		$rs = new stdClass;
		foreach($q as $r)
		{
			$rs->{$r['config']} = $r['value'];
		}
		unset($q,$r);

		$this->backview->view("ajax/{$this->view}",get_defined_vars());
	}

	function post(){
		$this->load->library('form_validation',$this->rule_post);
		if(!$this->form_validation->run())
		{
			return $this->index();
		}
		$rs = $this->input->post();

		$this->load->database();
		foreach($rs as $k => $v)
		{
			$result = $this->db->update("config"
				,array('value'=>$v)
				,array("category"=>"site",'config'=>$k));
			if(!$result)
			{
				$this->form_validation->set_error('title','配置更新失败，请重试');
				return $this->index();
			}
		}
        $this->lang->load('common');//加载common_lang.php
		$this->backview->success($this->lang->line('item_put_success'));
	}
}