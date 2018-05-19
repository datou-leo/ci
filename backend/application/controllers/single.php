<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Single extends CI_Controller
{
	var $caption;
	var $title;
	var $iframe;

	var $view  = 'single';
	var $table = 'single';

	var $rule_put = array(
//		array(
//			'field' => 'title',
//			'label' => '标题',
//			'rules' => 'max_length[255]'),
		array(
			'field' => 'content',
			'label' => '内容',
			'rules' => 'trim'),
	);

	function Single()
	{
		parent::__construct();
		list($this->caption,$nav,$sub) = $this->backview->menu_caption_index();
		$this->load->vars(array(
			'title'     => $this->title = strtolower(get_class()),
			'caption'   => $this->caption,
			'nav_index' => $nav,
			'sub_index' => $sub));

		$this->load->model('single_atom','my_model');
		$this->load->model('single_flow','my_flow');
		$this->my_model->table_name($this->table);
		$this->my_flow->set_atom($this->my_model);
		$this->my_flow->set_ctrl($this);
	}

	function index()
	{
		$vars = $this->my_flow->edit();
		if(is_array($vars))
		{
			return $this->backview->view("ajax/{$this->view}",$vars);
		}
		if(1 == $vars)
		{
			$this->backview->is_iframe_post(1);
			$this->backview->failure($this->lang->line('invalid_item_id'));
		}
	}

	function put()
	{
        $id=$this->uri->segment(3,-1);

        $res = 0;
        if(!($rs=$this->my_model->get($id))){
            $res = 2;
        }

        $this->load->library('form_validation',$this-> rule_put);
        if($this->form_validation->run() == false){
            $res = 1;
        }

        $form=$this->input->post();
        unset($form['iframe']);
        if(!$this->my_model->put($id,$form)){
            $res = 3;
        }


		switch($res){
			case 1: case 3:
				return $this->index();
			case 2:
                $this->lang->load('common');
				return $this->backview->failure($this->lang->line('invalid_item_id'));
		}
        $this->lang->load('common');
		$this->backview->success($this->lang->line('single_put_success'));
	}
}