<?php

class CI_Backview
{
	var $db;
	var $_ci;
	
	var $_table_menu = 'admin_act';
	var $menu        = array();
	var $menu_title  = array();
	var $menu_url    = array();
	var $menu_data   = array();
	
	function CI_Backview($config = array())
	{
		$this->_ci = &get_instance();
		if(is_array($config) && count($config) > 0)
		{
			$this->init($config);
		}
		
		$this->_ci->load->vars(array(
			'admin_account'=>$this->_ci->backauth->get_account(),
			'nav_index'=>1,
			'sub_index'=>1,
		));
		
		$this->ready_menu();
	}
	
	function init($config)
	{
		foreach($config as $k => $v)
		{
			if(isset($this->$k)){
				$this->$k = $v;
			}
		}
	}
	
	function ready_database()
	{
		if(!is_object($this->db))
		{
			$this->_ci->load->database();
			$this->db = &$this->_ci->db;
		}
	}
	
	function ready_menu()
	{
		$auth = &$this->_ci->backauth;
		$account = $auth->get_account();
		$this->menu = $this->menu_data = $this->menu_title = $this->menu_url = array();
		if(!empty($account))
		{
			$q = getTreeData($this->_table_menu,0,2);
			$arr_parent = array();
			$arr_leaf   = array();
			$this->_get_auth_parent($q,$auth,$arr_parent,$arr_leaf);
			foreach($q as $r)
			{
				if($r['is_havechild'] == 0)
				{
					if(!isset($arr_leaf[$r['id']]))
					{
						continue;
					}
				} else {
					if(!isset($arr_parent[$r['id']]))
					{
						continue;
					}
				}
				$r['depth'] = intval($r['depth']);
				$this->menu_data[$r['id']] = array(
					$r['title'],
					$r['url'],
					$r['depth'],
					$r['parent_id'],
					$r['internal'],
					$r['target'],
					$r['ajax'],
					$r['is_havechild'],
				);
				if(!isset($this->menu[$r['id']]))
				{
					$this->menu[$r['id']] = array();
				}
				if($r['depth'] < 1)
				{
					$this->menu[0][$r['id']] = &$this->menu_data[$r['id']];
				} else if(isset($this->menu[$r['parent_id']])) {
					$this->menu[$r['parent_id']][$r['id']] = &$this->menu_data[$r['id']];
				}
			}
			
			$in = $this->_ci->backauth->get_shortcut();
			if(!empty($in))
			{
				$q = getTreeData($this->_table_menu,0,2);
				$arr_short = array();
				if($q)
				{
					foreach($q as $r)
					{
						if(strpos(','.$in.',',','.$r['id'].',') !== false)
						{
							$arr_short[] = $r;
						}
					}
				}
				$this->_ci->load->vars('my_shortcut',$arr_short);
			}
		}
		$this->_ci->load->vars('menu',$this->menu);
		$this->_ci->load->vars('menu_data',$this->menu_data);
	}
	
	function _get_auth_parent($types, $auth, &$arr_parent, &$arr_leaf)
	{
		if($types)
		{
			$arr_types = array();
			foreach($types as $r)
			{
				if($r['is_havechild'] == 0)
				{
					if($auth->can_access_menu($r['id']))
					{
						if(!isset($arr_parent[$r['parent_id']])){
							$arr_parent[$r['parent_id']] = 1;
						}
						$arr_leaf[$r['id']] = 1;
					}
				} else {
					if($r['parent_id'] > 0)
					{
						$arr_types[$r['id']] = $r['parent_id'];
					}
				}
			}
			if($arr_types)
			{
				foreach($arr_types as $k => $v)
				{
					if(isset($arr_parent[$k]))
					{
						$arr_parent[$v] = 1;
					}
				}
			}
		}
	}
	
	function menu_caption_index()
	{
		$this->ready_database();
		$seg3  = $this->_ci->uri->segment(3,-1);
		$class = $this->_ci->router->fetch_class();
			
		if($seg3 > 0)
		{
			$url = "$class/index/$seg3";
			$this->db->or_where("url",$class);
		} else {
			$url = "$class";
			$this->db->or_where("url",$class."/index");
		}
		$this->db->or_where("url",$url);
		$this->db->limit(1);
		$q = $this->db->get($this->_table_menu);
		if($q->num_rows() < 1)
		{
			return false;
		}
		
		$r = $q->row_array();
		$a = explode(',',$r['path']);
		$nav = $sub = 1;
		if(isset($this->menu[0][$a[0]]))
		{
			$nav = array_search($a[0],array_keys($this->menu[0]))+1;
		}
		if(isset($a[1]) && isset($this->menu[$a[0]]))
		{
			$sub = array_search($a[1],array_keys($this->menu[$a[0]]))+1;
		}
		return array($r['title'],$nav,$sub);
	}
	
	function view($view, $vars = null)
	{
		if($this->is_iframe_post())
		{
			$data = str_replace(array("
","\r\n","\n","\r"),"",$this->_ci->load->view($view,$vars,true));
			$data = str_replace("</script","<\/script",addslashes($data));
			header('content-type:text/html;charset='.$this->_ci->config->item('charset'));
			exit('<script language="JavaScript" type="text/javascript">parent.show_dialog(unescape("'.$data.'"));parent.update_dialog();</script>');
		}
		$this->_ci->load->view($view,$vars);
	}
	
	function message($text, $url = -1, $view = 'message')
	{
		@$js = "history.go($url)";
		if($this->is_iframe_post())
		{
			$view = 'ajax/'.$view;
			$js   = "close_dialog()";
		}
		if(!is_array($url))
		{
			if($this->is_iframe_post())
			{
				if($url == -1) $url = "";
			}
			if(is_numeric($url) && $url < 0)
			{
				$url = "javascript:$js;";
			} else if(!empty($url) && strncasecmp($url,'http://',7)) {
				$url = site_url($url);
			}
		} else foreach($url as $k => $v) {
			if(!strncasecmp($v,'ajax:',5))
			{
				$url[$k] = 'ajax:'.site_url(substr($v,5));
			} else if(is_numeric($v)) {
				$url[$k] = "javascript:$js;";
			} else if(!empty($v)&&strncasecmp($v,'http://',7)
				 && strncasecmp($v,'javascript:',11)) {
				$url[$k] = site_url($v);
			}
		}
		$this->view($view,array('url'=>$url,'text'=>$text));
	}
	
	function success($text, $url = -1)
	{
		$this->message($text,$url,'success');
	}
	
	function failure($text, $url = -1)
	{
		$this->message($text,$url,'failure');
	}
	
	function is_iframe_post($flag = null)
	{
		if(!is_null($flag)){
			$_POST['iframe'] = ($flag?1:0);
		}
		if(intval($this->_ci->input->post("iframe")) != 1)
		{
			return false;
		}
		return true;
	}
}