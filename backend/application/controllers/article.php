<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH.'/libraries/Backdata_controller.php';
class Article extends Backdata_controller
{
	var $rule_post = array(
		array(
			'field' => 'title',
			'label' => '标题',
			'rules' => 'trim|required|max_length[255]'
		),
		array(
			'field' => 'type_id',
			'rules' => 'intval|required'
		),
		array(
			'field' => 'recommend',
			'rules' => 'intval'
		),
		array(
			'field' => 'show',
			'rules' => 'intval'
		),
		array(
			'field' => 'author',
			'label' => '作者',
			'rules' => 'trim'
		),
		array(
			'field' => 'timeline',
			'label' => '发布时间',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'from',
			'label' => '摘自',
			'rules' => 'trim'
		),
		array(
			'field' => 'url',
			'label' => '链接',
			'rules' => 'trim'
		),
		array(
			'field' => 'content',
			'label' => '新闻内容',
			'rules' => 'trim|required'
		)
	);

	var $search_post = array(
		array(
			'field' => 'title',
			'label' => '标题',
			'rules' => 'trim'
		),
		array(
			'field' => 'timeline',
			'rules' => 'trim'
		),
		array(
			'field' => 'expire',
			'rules' => 'trim'
		),
	);

	function on_load()
	{
		$this->view = $this->table = strtolower(get_class($this));
		$this->load->model('dataset_atom','my_model');
		//$this->load->model('dataset_flow','my_flow');
		$this->load->library('category');
		$this->category->table_name('article_type');
		$type_tree = $this->category->type_tree();
		$this->load->vars(array('type_tree'=>$type_tree));
	}

	function on_post()
	{
		return array('rule'=>$this->rule_post);
	}
	function searchnews()
	{
		$type_id = $this->uri->segment(3,-1);
		$this->backview->view("ajax/{$this->view}_searchnews",get_defined_vars());
	}
	function searchnewspost()
	{
		$type_id = $this->uri->segment(3,-1);
		$this->load->database();
		$this->load->library('form_validation',$this->search_post);
		if(!$this->form_validation->run())
		{
			return $this->index();
		}
		$form    = $this->form_validation->to_array();
		$form    = $this->uri->encode(serialize($form));
		//var_dump($form);exit;
		$type_id = $this->uri->segment(3,-1);
		redirect("{$this->title}/index/$type_id/1/null/10/$form");
	}

	function on_put()
	{
		return array('rule'=>$this->rule_post);
	}

	function copy()
	{
		$this->load->database();

		$type_id = $this->uri->segment(3,-1);
		$id      = $this->uri->segment(4,-1);
		$page    = $this->uri->segment(5,-1);
		$order   = $this->uri->segment(6,-1);
		$each    = $this->uri->segment(7,-1);
		$cond    = $this->uri->segment(8,-1);

		//复制数据
		$rs = $this->db->select('*')->order_by('sort_id','desc')->get_where($this->table,array('id'=>$id))->row_array();

		unset($rs['id']);
		unset($rs['sort_id']);

		//print_r($rs);

		$this->db->insert($this->table,$rs); //写入数据
		$ins_id = $this->db->insert_id();		//获取新写入的数据ID
		$this->db->update($this->table,array('sort_id'=>$ins_id),array('id'=>$ins_id)); //更新sort_id数据

		$this->backview->success($this->lang->line('item_put_success')
			,array('复制成功'=>"{$this->title}/index/$type_id/$page/$order/$each/$cond"));
	}
}