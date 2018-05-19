<?php

class Backtype_controller extends CI_Controller
{
	var $caption;
	var $title;
	var $iframe;
	
	var $view         = 'type';
	var $table        = '';
	
	var $search_field = 'title';
	
	var $model_name   = 'type_atom';
	
	function Backtype_controller()
	{
		parent::__construct();
		$this->on_load();
		
		list($this->caption,$nav,$sub) = $this->backview->menu_caption_index();
		if(empty($this->title))
		{
			$this->title = strtolower(get_class($this));
		}
		$this->load->vars(array(
			'title'=>$this->title,
			'caption'=>$this->caption,
			'nav_index'=>$nav,
			'sub_index'=>$sub));
		
		$this->load->library('category');
		$this->load->model($this->model_name,'model');
		if(!empty($this->table)){
			$this->model->table_name($this->table);
			$this->category->table_name($this->table);
		}
	}
	
	function _resolve_keyword($cond)
	{
		$if = array();
		$keyword = '';
		if(!empty($cond) && 'null' != $cond)
		{
			$cond = @$this->uri->decode($cond);
			$cond = @unserialize($cond);
			if(is_array($cond))
			{
				$if = $cond;
			}
		}
		if(isset($if['keyword']))
		{
			$keyword = $if['keyword'];
		}
		return array($if,$keyword);
	}
	
	function _resolve_like_condition(&$like, $search_field = 'title')
	{
		//解决了AR条件AND和OR没有括号区别，导致搜索到错误的结果
		if(count($like) > 0 && count($search_field) > 0)
		{
			if(is_string($search_field))
			{
				$search_field = explode(',',$search_field);
			}
			foreach($search_field as $k => $key)
			{
				if(strpos($key,'.'))
				{
					$search_field[$k] = $this->db->_protect_identifiers($key,true,false,true);
				} else {
					$search_field[$k] = $this->db->protect_identifiers($key);
				}
			}
			foreach($like as $val)
			{
				$a   = array();
				$val = $this->db->escape_like_str($val);
				foreach($search_field as $key)
				{
					$a[] = "$key LIKE '%{$val}%'";
				}
				$a = join(' OR ',$a);
				$this->db->where("($a)",'',false);
			}
		}
	}
	
	function _hook($method, $def = array())
	{
		$opt = $this->$method();
		if(!is_array($opt))
		{
			$opt = array();
		}
		if(!is_array($def))
		{
			$def = array();
		}
		return $opt+$def;
	}
	
	function index()
	{
		extract($this->_hook('on_index',array(
			'search_field'=>'title',
			'where'=>array(),
		)),EXTR_SKIP);
			
		$cond = $this->uri->segment(3,'null');
		list($if,$keyword) = $this->_resolve_keyword($cond);
		if(!isset($option['search_field']))
		{
			$option['search_field'] = 'title';
		}
		$this->_resolve_like_condition($if,$option['search_field']);
		$data  = $this->category->get_all();
		$total = count($data);
		
		$this->load->view($this->view,get_defined_vars());
	}
	
	function search()
	{
		extract($this->_hook('on_search',array(
			'link_method'=>'index',
			)),EXTR_SKIP);
		
		$keyword = '';
		$cond    = 'null';
		$if = array('keyword'=>'');
		if(!empty($_POST['keyword']))
		{
			$keyword = trim($_POST['keyword']);
			if(strcmp($keyword,'搜索内容'))
			{
				$keyword=  trim(strtr($keyword,array(','=>' ','|'=>' ')));
				if(!empty($keyword))
				{
					$if['keyword'] = $keyword;
					$cond = $this->uri->encode(serialize($if));
				}
			}
		}
		
		redirect("{$this->title}/$link_method/$cond");
	}
	
	function create()
	{
		extract($this->_hook('on_create',array(
			'parent_id'=>$this->uri->segment(3,0),
			)),EXTR_SKIP);
			
		$id_tree = $this->category->id_tree_for_edit();
		
		$this->backview->view("ajax/{$this->view}_create",get_defined_vars());
	}
	
	function post()
	{
		extract($this->_hook('on_post',array(
			'parent_id'=>$this->uri->segment(3,0),
			'rule'=>array(),
			'form'=>array(),
			'backward'=>'create',
			'link_method'=>'index',
			)),EXTR_SKIP);
				
		$this->load->library('form_validation',$rule);
		if(!$this->form_validation->run())
		{
			return $this->$backward();
		}
		$form = $this->input->post()+$form;
        unset($form['iframe']);
		if(isset($form['timeline']))
		{
			$form['timeline'] = strtotime($form['timeline']);
		} else {
			$form['timeline'] = time();
		}
		
		$this->model->upload($form);
		
		$this->db->select_max("id","max_id");
		$max_id = $this->db->get_where($this->table);
		$form['type_id'] = $max_id=intval($max_id->row("max_id"))+1;
		$types_id = $this->uri->segment(4,0);
		
		$this->on_insert($form);
		
		$result = $this->category->post($form);
		switch($result)
		{
			case 1:
				$this->form_validation->set_error('title','数据创建失败，请重试');
				break;
			case 2:
				$this->form_validation->set_error('parent_id','类别信息读取错误');
				break;
			case 3:
				$this->form_validation->set_error('parent_id','数据更新失败，请重试');
				break;
		}
		if($result != 0)
		{
			return $this->$backward();
		}
		$this->lang->load('common');
		$this->backview->success($this->lang->line('item_post_success')
			,array('继续添加'=>"ajax:{$this->title}/create/$parent_id/$types_id"
				,'完成'=>"{$this->title}/$link_method/$types_id"));
	}
	
	function edit()
	{
		$id = $this->uri->segment(3,0);
		
		if(!($rs = $this->category->get($id)))
		{
			$this->backview->is_iframe_post(1);
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		
		$rs->timeline = date('Y-m-d H:i:s',$rs->timeline);
		
		$this->on_edit($rs);
		
		$id_tree = $this->category->id_tree_for_edit();
		
		$this->backview->view("ajax/{$this->view}_edit",get_defined_vars());
	}
	
	function put()
	{
		extract($this->_hook('on_put',array(
			'id'=>$this->uri->segment(3,0),
			'rule'=>array(),
			'form'=>array(),
			'backward'=>'edit',
			)),EXTR_SKIP);
		
		if(!($rs = $this->category->get($id)))
		{
			$this->backview->is_iframe_post(1);
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		
		$this->load->library('form_validation',$rule);
		if(!$this->form_validation->run())
		{
			return $this->$backward();
		}
        $form = $this->input->post()+$form;
        unset($form['iframe']);
		if(isset($form['timeline']))
		{
			$form['timeline'] = strtotime($form['timeline']);
		}

		$this->model->upload($form,$rs);
		
		$this->on_update($form);
		
		$result = $this->category->put($form,$id);
		switch($result){
			case 1:
				$this->form_validation->set_error('title','数据创建失败，请重试');
				break;
			case 2:
				$this->form_validation->set_error('parent_id','类别信息读取错误');
				break;
			case 3:
				$this->form_validation->set_error('parent_id','类别不可以是自己或自己的子类');
				break;
			case 4:
				$this->form_validation->set_error('move_des','独立类别不可选择类别之前或类别之后');
				break;
		}
		if($result != 0)
		{
			return $this->$backward();
		}
		$this->lang->load('common');
		$this->backview->success($this->lang->line('item_put_success')
			,array('完成'=>"javascript:location.reload();"));
	}
	
	function delete()
	{
		$id = $this->uri->segment(3,-1);
		if($this->category->delete($id))
		{
			return print '{"result":1}';
		}
		$this->on_delete($id);
		echo '{"result":0}';
	}
	
	function toggle()
	{
		extract($this->_hook('on_toggle',array(
			'id_segment'=>3,
			'column_segment'=>4
			)),EXTR_SKIP);
			
		$this->load->helper('js');
		
		$id  = $this->uri->segment($id_segment,-1);
		$col = $this->uri->segment($column_segment,'');
		
		if(empty($col) || !preg_match('/^[a-z0-9_]+$/iD',$col))
		{
			return print js_encode(array('result'=>1));
		}
		if(isset($value))
		{
			$val = $this->model->toggle($id,$col,$value);
		} else {
			$val = $this->model->toggle($id,$col);
		}
		if($val === false)
		{
			return print js_encode(array('result'=>2));
		}
		print js_encode(array('result'=>0,'text'=>admin_yes_no($val)));
	}
	
	function moveup()
	{
		extract($this->_hook('on_moveup',array(
			'id'=>$this->uri->segment(3,-1),
			'link_method'=>'index',
			)),EXTR_SKIP);
		
		if($id < 0)
		{
			return $this->backview->failure('未指定项目');
		}
		if(!($rs = $this->category->get($id)))
		{
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		if(!($target = $this->model->get_prev($rs->id)))
		{
			return $this->backview->failure('已经移动到最上面了');
		}
		if(!$this->model->swap_item($rs,$target))
		{
			return $this->backview->failure('移动失败，请重试');
		}
		return redirect("{$this->title}/$link_method");
	}
	
	function movedown()
	{
		extract($this->_hook('on_movedown',array(
			'id'=>$this->uri->segment(3,-1),
			'link_method'=>'index',
			)),EXTR_SKIP);
		
		if($id < 0)
		{
			return $this->backview->failure('未指定项目');
		}
		if(!($rs = $this->category->get($id)))
		{
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		if(!($target = $this->model->get_next($rs->id)))
		{
			return $this->backview->failure('已经移动到最下面了');
		}
		if(!$this->model->swap_item($rs,$target))
		{
			return $this->backview->failure('移动失败，请重试');
		}
		return redirect("{$this->title}/$link_method");
	}
	
	function access()
	{
		extract($this->_hook('on_access',array(
			'link_method'=>'index',
			)),EXTR_SKIP);
		
		$goto = "{$this->title}/$link_method";
		
		if(empty($_POST['access']) || !method_exists($this,'_access_'.$_POST['access']))
		{
			return $this->backview->failure('未指定的操作，可能您点击了错误的链接',$goto);
		}
		if(!isset($_POST['checked']) || !is_array($_POST['checked']) || count($_POST['checked']) < 1)
		{
			return $this->backview->failure('请至少选择一个项目',$goto);
		}
		
		$this->{'_access_'.$_POST['access']}($_POST['checked'],$goto);
	}
	
	function _access_delete($ids, $goto = '')
	{
		$this->on_access_delete($ids);
		foreach($ids as $id)
		{
			if($this->category->delete($id))
			{
				return $this->backview->failure($this->lang->line('item_delete_failure'));
			}
			$this->on_delete($id);
		}
		return $this->backview->success($this->lang->line('access_delete_success')
			,array('转到列表'=>$goto));
	}
	
	function on_load(){}
	function on_index(){}
	function on_search(){}
	function on_create(){}
	function on_post(){}
	function on_insert(&$form){}
	function on_edit(&$rs){}
	function on_update(&$form){}
	function on_delete($id){}
	function on_toggle(){}
	function on_moveup(){}
	function on_movedown(){}
	function on_access(){}
	function on_access_delete(&$ids){}
}