<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_user extends CI_Controller
{
	var $view = 'admin_user';
	var $caption;
	var $title;
	var $iframe;
	var $system_account = array('administrator');

	var $rule_post = array(
		array(
			'field' => 'account',
			'label' => '帐号',
			'rules' => 'trim|required|callback_check_account'),
		array(
			'field' => 'password',
			'label' => '密码',
			'rules' => 'trim|required'),
		array(
			'field' => 'passconf',
	        'label' => '重复密码',
	        'rules' => 'trim|required|matches[password]'),
		array(
			'field' => 'role',
	        'label' => '管理权限',
	        'rules' => 'trim|callback_check_role'),
	);

	var $rule_put = array(
		array(
			'field' => 'password',
			'label' => '密码',
			'rules' => 'trim'),
		array(
			'field' => 'passconf',
	        'label' => '重复密码',
	        'rules' => 'trim|matches[password]'),
		array(
			'field' => 'role',
	        'label' => '管理权限',
	        'rules' => 'trim'),
	);

	function check_account($s)
	{
		if($this->my_model->get($s))
		{
			$this->form_validation->set_message("check_account","这个帐号已经有了，请换一个");
			return false;
		}
		return true;
	}

	function check_role($s)
	{
		if(!isset($this->backauth->auth_caption[$s]))
		{
			$this->form_validation->set_message("check_role","请选择权限");
			return false;
		}
		return true;
	}

	function Admin_user()
	{
		parent::__construct();
		list($this->caption,$nav,$sub)=$this->backview->menu_caption_index();
		$this->load->vars(array(
			'auth_caption' => $this->backauth->auth_caption,
			'title'        => $this->title = strtolower(get_class()),
			'caption'      => $this->caption,
			'nav_index'    => $nav,
			'sub_index'    => $sub));
		$this->load->model('admin_user_model','my_model');
	}

	function index()
	{
		$this->load->library('pager');
		list($type_id,$page,$order,$each,$cond,$keyword)
			 = $this->pager->type_segment(3,4,5,6,'timeline:desc');
		$table_name = $this->my_model->table_name();
		$where = array();
		$this->pager->init(array(
			'table'        => $table_name,
			'where'        => $where,
			'search_field' => 'account',
			'link'         => site_url("$this->title/index/$type_id/{page}/$order/$each/$cond"),
		));
		list($page_link,$data,$page,$total) = $this->pager->create_link();
		$this->load->view($this->view,get_defined_vars());
	}

	function create()
	{
		$this->load->library('category');
		$id_tree   = $this->category->id_tree('admin_act');
		$arr_types = $this->_get_tree_prop();
		if($id_tree)
		{
			foreach ($id_tree as $k => $v)
			{
				if(isset($arr_types[$k]) && $arr_types[$k]['is_use'] == 2)
				{
					unset($id_tree[$k]);
				}
			}
		}
		$auth_caption = $this->backauth->auth_caption;
		$auth_power   = $this->backauth->auth_power;

		$this->backview->view("ajax/{$this->view}_create",get_defined_vars());
	}

	function post()
	{
		$backward='create';
		$this->load->library('form_validation',$this->rule_post);
		if(!$this->form_validation->run())
		{
			return $this->$backward();
		}
		$form = $this->input->post();
        unset($form['iframe']);
		$form['timeline']    = time();
		$form['create_ip']   = $this->input->ip_address();
		$form['login_count'] = 0;
		$form['password']    = md5($form['password']);
		unset($form['passconf']);

		//get auth
		if($form['role'] == 'admin')
		{
			$form['auth'] = '';
		} else {
			$a          = array();
			$arr_Type   = $this->_get_tree_prop();
			$auth_power = $this->backauth->auth_power;
			if($arr_Type)
			{
				foreach($arr_Type as $k => $v)
				{
					if($v['is_havechild'] == 0 && $v['is_use'] == 1)
					{
						foreach($auth_power as $key => $r)
						{
							$a[$k][$key] = 1;
						}
					}
				}
			}
			foreach($_POST as $k => $v)
			{
				if(!strncmp($k,'auth_',5))
				{
					$k = explode("_",$k);
					if(!isset($a[$k[1]][$k[2]]))
					{
						$a[$k[1]][$k[2]] = 1;
					}
				}
			}
			$form['auth'] = serialize($a);
		}
		if(!$this->my_model->post($form))
		{
			$this->form_validation->set_error('account','lang:item_put_failure');
			return $this->$backward();
		}
        $this->lang->load('common');
		$this->backview->success($this->lang->line('item_post_success')
			,array('继续添加'=>"ajax:{$this->title}/create"
				,'完成'=>"{$this->title}"));
	}

	function edit()
	{
		$this->load->library('pager');
		list($type_id,$id,$page,$order,$each,$cond,$keyword)
			 = $this->pager->type_edit_segment(3,4,5,6,'timeline:desc');

		$account = base64_decode(strtr($id,'|','/'));
		if(!strcasecmp($account,'administrator'))
		{
			$this->backview->is_iframe_post(1);
			return $this->backview->failure("不能编辑系统保留帐号"
				,"{$this->view}/index/$type_id/$page/$order/$cond/$each");
		}

		if(!($rs = $this->my_model->get($account)))
		{
			$this->backview->is_iframe_post(1);
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		//set auth into POST for better input form
		if(!empty($rs->auth))
		{
			$a = unserialize($rs->auth);
			foreach($a as $k_id => $r)
			{
				foreach($r as $k => $v)
				{
					$_POST['auth_'.$k_id.'_'.$k] = 1;
				}
			}
		}

		$this->load->library('category');
		$id_tree   = $this->category->id_tree('admin_act');
		$arr_types = $this->_get_tree_prop();
		if($id_tree)
		{
			foreach($id_tree as $k => $v)
			{
				if(isset($arr_types[$k]) && $arr_types[$k]['is_use'] == 2)
				{
					unset($id_tree[$k]);
				}
			}
		}
		$auth_caption = $this->backauth->auth_caption;
		$auth_power   = $this->backauth->auth_power;

		$this->backview->view("ajax/{$this->view}_edit",get_defined_vars());
	}

	function put(){
		$backward = 'edit';

		$this->load->library('pager');
		list($type_id,$id,$page,$order,$each,$cond,$keyword)
			 = $this->pager->type_edit_segment(3,4,5,6,'timeline:desc');

		$account = base64_decode(strtr($id,'|','/'));
		if(in_array($account,$this->system_account))
		{
			$this->backview->is_iframe_post(1);
			return $this->backview->failure("不能编辑系统保留帐号"
				,"{$this->view}/index/$type_id/$page/$order/$cond/$each");
		}

		$this->load->library('form_validation',$this->rule_put);
		if(!$this->form_validation->run())
		{
			return $this->$backward();
		}
		$form = $this->input->post();
		if(!empty($form['password']))
		{
			$form['password']=md5($form['password']);
		} else {
			unset($form['password']);
		}
		unset($form['passconf']);

		//get auth
		if($form['role'] == 'admin')
		{
			$form['auth'] = '';
		} else {
			$a          = array();
			$arr_Type   = $this->_get_tree_prop();
			$auth_power = $this->backauth->auth_power;
			if($arr_Type)
			{
				foreach($arr_Type as $k => $v)
				{
					if($v['is_havechild'] == 0 && $v['is_use'] == 1)
					{
						foreach($auth_power as $key => $r)
						{
							$a[$k][$key] = 1;
						}
					}
				}
			}
			foreach($_POST as $k => $v)
			{
				if(!strncmp($k,'auth_',5))
				{
					$k = explode("_",$k);
					if(!isset($a[$k[1]][$k[2]]))
					{
						$a[$k[1]][$k[2]] = 1;
					}
				}
			}
			$form['auth'] = serialize($a);
		}

		if(!$this->my_model->put($account,$form))
		{
			$this->form_validation->set_error('password','item_put_failure');
			return $this->$backward();
		}
        $this->lang->load('common');
		$this->backview->success($this->lang->line('item_put_success')
			,array('完成'=>"javascript:location.reload();"));
	}

	function delete()
	{
		$id      = $this->uri->segment(3,'');
		$account = base64_decode(strtr($id,'|','/'));
		if(in_array($account,$this->system_account))
		{
			return print '{"result":1}';
		}

		$this->my_model->delete($account);
		echo '{"result":0}';
	}

	function _get_tree_prop()
	{
		$this->load->database();
		$this->db->select('id,parent_id,is_use,is_fixed');
		$rs_types  = $this->db->get('admin_act')->result_array();
		$arr_types = array();
		if($rs_types)
		{
			$parent_ids = array();
			foreach($rs_types as $r)
			{
				$arr_types[$r['id']]=$r;
				if(!isset($parent_ids[$r['parent_id']]))
				{
					$parent_ids[$r['parent_id']] = 1;
				}
			}
			if($arr_types)
			{
				foreach($arr_types as $k => $v)
				{
					if(isset($parent_ids[$k]))
					{
						$arr_types[$k]['is_havechild'] = 1;
					} else {
						$arr_types[$k]['is_havechild'] = 0;
					}
					unset($arr_types[$k]['id']);
					unset($arr_types[$k]['parent_id']);
				}
			}
		}
		return $arr_types;
	}

	function access()
	{
		$this->load->library('pager');
		list($type_id,$page,$order,$each,$cond,$keyword)
			 = $this->pager->type_segment();
		$goto = "{$this->title}/index/$type_id/$page/$order/$each/$cond";

		if(empty($_POST['access']) || !method_exists($this,'_access_'.$_POST['access']))
		{
			return $this->backview->failure('未指定的操作，可能您点击了错误的链接',$goto);
		}

		if(!isset($_POST['checked']) || !is_array($_POST['checked'])||count($_POST['checked']) < 1)
		{
			return $this->backview->failure('请至少选择一个项目',$goto);
		}

		$this->{'_access_'.$_POST['access']}($_POST['checked'],$goto);
	}

	function _access_delete($ids,$goto = '')
	{
		foreach($ids as $id)
		{
			$account = base64_decode(strtr($id,'|','/'));
			if(in_array($account,$this->system_account))
			{
				return $this->backview->failure("不能删除系统保留帐号");
			}
			if(!$this->my_model->delete($id))
			{
				return $this->backview->failure($this->lang->line('item_delete_failure'));
			}
		}

		return $this->backview->success($this->lang->line('access_delete_success')
			,array('转到列表'=>$goto));
	}
}