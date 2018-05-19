<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH.'/libraries/Backtype_controller.php';
class Article_type extends Backtype_controller
{
	var $rule_post = array(
		array(
			'field' => 'title',
			'label' => '分类名称',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'intro',
			'label' => '分类简介',
			'rules' => 'trim'
		),
		array(
			'field' => 'type_id',
			'label' => '类型编号',
			'rules' => 'intval'
		),
		array(
			'field' => 'parent_id',
			'label' => '从属于',
			'rules' => 'intval'
		),
		array(
			'field' => 'move_des',
			'rules' => 'intval'
		)
	);

	function on_load()
	{
		$this->table = strtolower(get_class($this));
	}

	function on_post()
	{
		return array('rule'=>$this->rule_post);
	}

	function on_put()
	{
		return array('rule'=>$this->rule_post);
	}
}