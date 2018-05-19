<?php

class CI_Backauth{
	var $db;
	
	var $userdata;
	
	var $router;
	
	var $uri;
	
	var $status        = 'wait';
	
	var $user_account  = '';
	
	var $user_auth     = array();
	
	var $user_role     = '';
	
	var $user_shortcut = '';
	
	var $page_class    = '';
	
	var $page_method   = '';
	
	var $current_menu;
	
	var $auth_level = array(
		'admin'=>128,
		'user'=>64,
	);
	
	var $auth_caption = array(
		'admin'=>'Administrator',
		'user'=>'Standard User',
	);
	
	var $auth_power = array(
		'index'=>'View',
		'post'=>'Create',
		'put'=>'Update',
		'delete'=>'Delete',
	);
	
	var $auth_exclude = array(
		'login','banned','denied','uploadify',
	);
	
	var $auth_power_route = array(
		'index'=>'index',
		'search'=>'index',
		'create'=>'post',
		'post'=>'post',
		'edit'=>'put',
		'put'=>'put',
		'toggle'=>'put',
		'delete'=>'delete',
		'moveup'=>'put',
		'movedown'=>'put',
		'view'=>'index',
		'excel'=>'index',
		'jsChangeIndex'=>'put',
		'toggle_read'=>'put',
		'searchproduct'=>'index',
		'searchproductpost'=>'index',
		'searchnews'=>'index',
		'searchnewspost'=>'index',
		'child'=>'index',
		'child_search'=>'index',
		'child_create'=>'post',
		'child_post'=>'post',
		'child_edit'=>'put',
		'child_put'=>'put',
		'child_toggle'=>'put',
		'child_delete'=>'delete',
		'child_moveup'=>'put',
		'child_movedown'=>'put',
		'child_jsChangeIndex'=>'put',
	);
	
	var $auth_autorun         = true;
	
	var $auth_default         = 'login';
	
	var $auth_login           = 'login';
	
	var $auth_failure         = 'denied';
	
	var $auth_session_account = 'admin_account';
	
	var $auth_table_user      = 'admin_user';
	
	var $auth_table_act       = 'admin_act';
	
	function CI_Backauth($config = null)
	{
		if(is_array($config)
			)
		{
			foreach($config as $k => $v)
			{
				if(!strncmp($k,'auth_',5) && isset($this->$k))
				{
					$this->$k = $v;
				}
			}
		}
		
		$CI = &get_instance();
		
		$this->router = &$CI->router;
		$this->uri = &$CI->uri;
		
		$CI->load->library('userdata');
		$this->userdata=&$CI->userdata;
		
		$CI->load->database();
		$this->db = &$CI->db;
		
		log_message('debug', "Backauth Class Initialized");
		
		if($this->auth_autorun)
		{
			$this->run();
		}
	}
	
	function run()
	{
		// are we in exclude page?
		$class = strtolower($this->router->fetch_class());
		if(in_array($class,$this->auth_exclude))
		{
			return $this->access_success();
		}
		
		
		$this->_ready_user_auth();
		$this->_ready_page_auth();

		// no account in session? then forward to login
		if(empty($this->user_role) || count($this->user_auth) < 1)
		{
			$this->forward_login();
		}
		
		// try to find current menu id
		$this->current_menu = $menu = $this->get_menu_item();
		
		// for admin
		if('admin' == $this->user_role)
		{
			if($this->user_account != 'administrator' && $menu !== false)
			{
				$rs = $this->db->select('is_use')
					->get_where($this->auth_table_act,array('id'=>$menu['id']))
					->row();
				if($rs)
				{
					if($rs->is_use == 2)
					{
						return $this->forward_failure();
					}
				}
			}
			return $this->access_success();
		}

		// nothing required for this menu act, then passed
		if(false === $menu)
		{
			return $this->access_success();
		}
		
		//var_dump($this->user_auth);var_dump($menu);exit;
		
		// user haven't got any permission to access this menu, failure
		if(!isset($this->user_auth[$menu['id']]))
		{
			return $this->forward_failure();
		}

		// ready user auth list and current act
		$auth = $this->user_auth[$menu['id']];
		$act  = $this->page_method;
		//var_dump($this->user_auth);
		
		
		// special option for method="access"
		if('access' == $act || 'child_access' == $act)
		{
			if(empty($_POST['access']))
			{
				return $this->access_success();
			}
			$act=strtolower(trim($_POST['access']));
		}
		
		// any act routes?
		if(isset($this->auth_power_route[$act]))
		{
			$act = $this->auth_power_route[$act];
		}
		
		if($act == "cancel_auditing")
		{
			if(!isset($auth["auditing"]))
			{
				return $this->forward_failure();
			}
		} else {
			if($act != 'export')
			{
				if($act != 'searchpost')
				{
					if($act != 'import')
					{
						if($act != "change_resion")
						{
							if($act != "upload")
							{
								if(!isset($auth[$act]))
								{
										//echo $act;exit();
									return $this->forward_failure();
								}
							}
						}
					}
				}
			}
		}
		$this->access_success();
	}
	
	function ensure_role($role)
	{
		if($role != $this->user_role && 'admin' != $this->user_role)
		{
			$this->forward_failure();
		}
	}
	
	function ensore_auth($act)
	{
		if('admin' != $this->user_role)
		{
			if(!isset($this->user_auth[$this->current_menu['id']]))
			{
				return $this->forward_failure();
			}
			$auth = $this->user_auth[$this->current_menu['id']];
			if(!isset($auth[$act]))
			{
				return $this->forward_failure();
			}
		} else {
			if($this->user_account != 'administrator')
			{
				$rs = $this->db->select('is_use')
					->get_where($this->auth_table_act,array('id'=>$act))
					->row();
				if(!$rs)
				{
					return $this->forward_failure();
				} else {
					if($rs->is_use == 2)
					{
						return $this->forward_failure();
					}
				}
			}
		}
	}
	
	function can_access_menu($id)
	{
		if('admin' != $this->user_role&&!isset($this->user_auth[$id]))
		{
			return false;
		} else {
			if($this->user_account != 'administrator' && 'admin' == $this->user_role)
			{
				$rs = $this->db->select('is_use')
					->get_where($this->auth_table_act,array('id'=>$id))
					->row();
				if(!$rs)
				{
					return false;
				} else {
					if($rs->is_use == 2)
					{
						return false;
					}
				}
			}
		}
		return true;
	}
	
	function _ready_user_auth()
	{
		if(!($account = $this->get_account())){
			return;
		}
		
		$this->user_account = $account;
		$rs = $this->db->select('role,auth,shortcut')
			->get_where($this->auth_table_user,array('account'=>$account))
			->row_array();
		if(isset($rs['role']) || isset($rs['auth']))
		{
			$this->user_role = $rs['role'];
			$this->user_auth = @unserialize($rs['auth']);
		}
		$this->user_shortcut = trim($rs['shortcut']);
	}
	
	function _ready_page_auth()
	{
		$this->page_class  = $this->router->fetch_class();
		$this->page_method = $this->router->fetch_method();
	}
	
	function get_status()
	{
		return $this->status;
	}
	
	function get_account()
	{
		return $this->userdata->get($this->auth_session_account);
	}
	
	function set_account($val)
	{
		return $this->userdata->set($this->auth_session_account,$val);
	}
	
	function get_shortcut()
	{
		return $this->user_shortcut;
	}
	
	function get_menu_item($class = '', $method = '')
	{
		if(empty($class))
		{
			$class = $this->page_class;
		}
		if(empty($method))
		{
			$method = $this->page_method;
		}
		$seg3 = $this->uri->segment(3,-1);
		if($seg3 > 0)
		{
			$arr_oper = array('delete','excel','jsChangeIndex');
			if(in_array($method,$arr_oper) || strpos($method, 'child')!==false){
				$arr_table=array('admin_menu'=>'', 'uploadify'=>'');
				$table=isset($arr_table[$class])?$arr_table[$class]:$class;
				if($table != '' && strpos($table,'type') === false)
				{
					$this->db->select('id,type_id');
					$rs = $this->db->get_where($table,array('id'=>$seg3),1)->row();
					if($rs)
					{
						if(intval($rs->type_id) <= 0)
						{
							$url = $class;
						} else {
							$url = "$class/index/{$rs->type_id}";
						}
					} else {
						$url=$class;
					}
				} else {
					$url = $class;
				}
			} else {
				$url = "$class/index/$seg3";
				$this->db->or_where("url",$class);
			}
		} else {
			$url = $class;
		}
		$this->db->or_where("url",$url);
		$this->db->limit(1);
		$q = $this->db->get($this->auth_table_act);
		if($q->num_rows() < 1)
		{
			return false;
		}
		$q = $q->row_array();
		return $q;
	}
	
	function access_success()
	{
		$this->status = 'success';
	}
	
	function forward_login()
	{
		$this->status = 'login';
		$this->forward($this->auth_login);
	}
	
	function forward_default()
	{
		$this->status = 'default';
		$this->forward($this->auth_default);
	}
	
	function forward_failure()
	{
		$this->status = 'failure';
		$this->forward($this->auth_failure);
	}
	
	function forward($uri)
	{
		$CI = &get_instance();
		$CI->load->helper('url');
		redirect($uri);
		exit;
	}
}