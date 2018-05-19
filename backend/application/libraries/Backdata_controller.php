<?php

class Backdata_controller extends CI_Controller
{
	var $caption;
	var $title;
	var $iframe;
	
	var $view         = '';
	var $table        = '';
	var $table_type   = '';
	
	var $search_field = 'title';
	
	var $model_name   = 'dataset_atom';
	
	function Backdata_controller()
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
		
		$this->load->model($this->model_name,'model');
		if(!empty($this->table))
		{
			$this->model->table_name($this->table);
		}
		
		if(!empty($this->table_type))
		{
			$this->load->library('category');
			$this->category->table_name($this->table_type);
		}
		
		$this->load->library('pager');
	}
	
	function _segment()
	{
		list($type_id,$page,$order,$each,$cond,$keyword) = $this->pager->type_segment();
		return get_defined_vars();
	}
	
	function _edit_segment()
	{
		list($type_id,$id,$page,$order,$each,$cond,$keyword)=$this->pager->type_edit_segment();
		return get_defined_vars();
	}

	function jsChangeIndex()
	{
		$id    = $this->uri->segment(3,0);
		$index = $this->uri->segment(4,0);
		
		$this->load->helper('js');
		if(!$this->model->put($id,array('sort_id'=>intval($index))))
		{
			return print js_encode(array('result'=>1,'id'=>$id));
		} else {
			return print js_encode(array('result'=>0,'id'=>$id));
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

	// index.php?ctrl/func/type_id/page/order/each/cond
	
	function index()
	{
		extract($this->_segment(),EXTR_SKIP);
		extract($this->_hook('on_index',array(
			'search_field'=>'title',
			'link_method'=>'index',
			'type_column'=>'type_id',
			'where'=>array(),
			)),EXTR_SKIP);
		
		if(isset($type_id) && $type_id > -1)
		{
			if(empty($this->table_type))
			{
				$where[$type_column] = $type_id;
			}else{//多级产品搜索
				$types   = $this->category->split_type();
				$type_in = $this->category->get_match_type($type_id,$types);
			}
		}
		
		if(isset($type_in))
		{//多级产品搜索
			$this->db->where_in($type_column,$type_in);
		}
		//新的搜索 2012-03-02 更新
		
		if($cond)
		{

            $result=base64_decode(strtr($cond,'|','/'));
            $result=convert_charset($result,'UTF-8','UTF-8');
			$result = @unserialize($result);
			//var_dump($result);exit;
			//if(isset($result['title']) && $result['title']){
			//	$this->db->where("title",$result['title']);
			//}
			if(isset($result['expire']) && $result['expire'])
			{
				$this->db->where('FROM_UNIXTIME(timeline,"%Y-%m-%d") >=', $result['expire']);
			}
			if(isset($result['timeline']) && $result['timeline'])
			{
				$this->db->where('FROM_UNIXTIME(timeline,"%Y-%m-%d") <=', $result['timeline']);
			}
			if(isset($result['title']) && $result['title'])
			{
				$this->db->like("title",$result['title']);
			}
		}
		//新的搜索 2012-03-02 更新
		$this->pager->init(array(
			'table'=>$this->table,
			'where'=>$where,
			'search_field'=>$search_field,
			'link'=>site_url("{$this->title}/$link_method/$type_id/{page}/$order/$each/$cond"),
		));
		if(isset($type_in))
		{//多级产品搜索
			$this->db->where_in($type_column,$type_in);
		}
		
		//新的搜索 2012-03-02 更新
		if($cond)
		{
            $result=base64_decode(strtr($cond,'|','/'));
            $result=convert_charset($result,'UTF-8','UTF-8');
			$result =@ unserialize($result);
			//if(isset($result['title']) && $result['title']){
			//	$this->db->where("title",$result['title']);
			//}
			if(isset($result['expire']) && $result['expire'])
			{
				$this->db->where('FROM_UNIXTIME(timeline,"%Y-%m-%d") >=', $result['expire']);
			}
			if(isset($result['timeline']) && $result['timeline'])
			{
				$this->db->where('FROM_UNIXTIME(timeline,"%Y-%m-%d") <=', $result['timeline']);
			}
			if(isset($result['title']) && $result['title'])
			{
				$this->db->like("title",$result['title']);
			}
		}
		//新的搜索 2012-03-02 更新
		list($page_link,$data,$page,$total) = $this->pager->create_link();
		
		if(!empty($this->table_type))
		{
			$type_tree=$this->category->type_tree();
		}
		
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
				$keyword = trim(strtr($keyword,array(','=>' ','|'=>' ')));
				if(!empty($keyword))
				{
					$if['keyword'] = $keyword;
					$cond = $this->uri->encode(serialize($if));
				}
			}
		}
		
		if(!isset($type_id))
		{
			$type_id = $this->uri->segment(3,-1);
		}
		redirect("{$this->title}/$link_method/$type_id/1/null/25/$cond");
	}
	
	function create()
	{
		extract($this->_hook('on_create',array(
			'type_id'=>$this->uri->segment(3,-1),
			)),EXTR_SKIP);
			
		if(!empty($this->table_type))
		{
			$type_tree = $this->category->type_tree();
		}
		
		$this->backview->view("ajax/{$this->view}_create",get_defined_vars());
	}
	
	function post()
	{
		extract($this->_hook('on_post',array(
			'type_id'=>$this->uri->segment(3,-1),
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
		if(!isset($form['show']))
		{
			$form['show'] = 1;
		}
		if(!isset($form['type_id']))
		{
			$form['type_id'] = $type_id;
		}
		if(isset($form['timeline']))
		{
			$form['timeline'] = strtotime($form['timeline']);
		} else {
			$form['timeline'] = time();
		}
		
		$this->model->upload($form);
        unset($form['userfile_list']);
        unset($form['upload_clear']);
		if(method_exists($this->model,'upfile'))
		{
			$this->model->upfile($form);
		}
		//if($this->input->post('userflv_list')&&method_exists($this->model,'upflv')){
			//$this->model->upflv($form);
		//}
		
		if(method_exists($this->model,'upflv'))
		{
			$this->model->upflv($form);
		}
		
		$this->on_insert($form);
		
		if(!$this->model->post($form))
		{
			$this->form_validation->set_error('title','lang:item_put_failure');
			return $this->$backward();
		}
		$this->lang->load('common');
		$this->backview->success($this->lang->line('item_post_success')
			,array('继续添加'=>"ajax:{$this->title}/create/$type_id"
				,'完成'=>"{$this->title}/$link_method/$type_id"));
	}
	
	function edit(){
		extract($this->_edit_segment());
		
		if(!($rs = $this->model->get($id)))
		{
			$this->backview->is_iframe_post(1);
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		
		$rs->timeline=date('Y-m-d H:i:s',$rs->timeline);
		
		$this->on_edit($rs);
		
		if(!empty($this->table_type))
		{
			$type_tree = $this->category->type_tree();
		}
		
		$this->backview->view("ajax/{$this->view}_edit",get_defined_vars());
	}
	
	function put()
	{
		extract($this->_hook('on_put',array(
			'type_id'=>$this->uri->segment(3,-1),
			'rule'=>array(),
			'form'=>array(),
			'backward'=>'edit',
			)),EXTR_SKIP);
		
		extract($this->_edit_segment());
		
		if(!($rs = $this->model->get($id)))
		{
			$this->backview->is_iframe_post(1);
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		
		$this->load->library('form_validation',$rule);
		if(!$this->form_validation->run()){
			return $this->$backward();
		}
        $form = $this->input->post()+$form;
        unset($form['iframe']);
		if(isset($form['timeline']))
		{
			$form['timeline'] = strtotime($form['timeline']);
		}

		$this->model->upload($form,$rs);
        unset($form['userfile_list']);
        unset($form['upload_clear']);
		if(method_exists($this->model,'upfile'))
		{
			$this->model->upfile($form,$rs);
		}
		//if($this->input->post('userflv_list')&&method_exists($this->model,'upflv')){
			//$this->model->upflv($form,$rs);
		//}
		if(method_exists($this->model,'upflv'))
		{
			$this->model->upflv($form,$rs);
		}
		
		$this->on_update($form,$rs);
		
		if(!$this->model->put($id,$form))
		{
			$this->form_validation->set_error('title','item_put_failure');
			return $this->$backward();
		}
		$this->lang->load('common');
		$this->backview->success($this->lang->line('item_put_success')
			,array('完成'=>"javascript:location.reload();"));
	}
	
	function delete()
	{
		$id = $this->uri->segment(3,-1);
		if(!$this->model->delete($id))
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
			$val=$this->model->toggle($id,$col);
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
			'where'=>array("type_id"=>$this->uri->segment(3,-1)),
			'link_method'=>'index',
			)),EXTR_SKIP);
		
		extract($this->_edit_segment());
		
		if(!($rs = $this->model->get($id,"id,sort_id")))
		{
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		if(isset($where['type_id']) && $where['type_id'] < 1)
		{
			unset($where['type_id']);
		}
		if(!($q2 = $this->model->get_prev($rs->sort_id,$where,'id,sort_id')))
		{
			return $this->backview->failure('已经移动到最上面了');
		}
		if(!$this->model->swap_sort_id($rs,$q2))
		{
			return $this->backview->failure('移动失败，请重试');
		}
		
		redirect("{$this->title}/$link_method/$type_id/$page/$order/$each/$cond");
	}
	
	function movedown()
	{
		extract($this->_hook('on_movedown',array(
			'where'=>array("type_id"=>$this->uri->segment(3,-1)),
			'link_method'=>'index',
			)),EXTR_SKIP);
		
		extract($this->_edit_segment());
		
		if(!($rs = $this->model->get($id,"id,sort_id")))
		{
			return $this->backview->failure($this->lang->line('item_not_found'));
		}
		if(isset($where['type_id']) && $where['type_id'] < 1)
		{
			unset($where['type_id']);
		}
		if(!($q2 = $this->model->get_next($rs->sort_id,$where,'id,sort_id')))
		{
			return $this->backview->failure('已经移动到最下面了');
		}
		if(!$this->model->swap_sort_id($rs,$q2))
		{
			return $this->backview->failure('移动失败，请重试');
		}
		
		redirect("{$this->title}/$link_method/$type_id/$page/$order/$each/$cond");
	}
	
	function access()
	{
		extract($this->_hook('on_access',array(
			'link_method'=>'index',
			)),EXTR_SKIP);
		extract($this->_segment());
		
		$goto = "{$this->title}/$link_method/$type_id/$page/$order/$each/$cond";
		
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
			if(!$this->model->delete($id))
			{
				return $this->backview->failure($this->lang->line('item_delete_failure'));
			}
			$this->on_delete($id);
		}
		return $this->backview->success($this->lang->line('access_delete_success')
			,array('转到列表'=>$goto));
	}

	function _access_auditing($ids, $goto = '')
	{
		$this->on_access_auditing($ids);
		foreach($ids as $id)
		{
			if(!$this->model->put($id,array('auditing'=>1)))
			{
				return $this->backview->failure($this->lang->line('item_auditing_failure'));
			}
			//$this->on_auditing($id);
		}
		if($this->title == "servicestation"){
			//$this->service_cache(); //生成缓存文件
		}
		return $this->backview->success($this->lang->line('access_auditing_success')
			,array('转到列表'=>$goto));
		
	}
	function _access_cancel_auditing($ids, $goto = '')
	{
		$this->on_access_cancel_auditing($ids);
		foreach($ids as $id)
		{
			if(!$this->model->put($id,array('auditing'=>0)))
			{
				return $this->backview->failure($this->lang->line('item_cancel_auditing_failure'));
			}
			//$this->on_auditing($id);
		}
		
		if($this->title == "servicestation")
		{
			//$this->service_cache(); //生成缓存文件
		}
		return $this->backview->success($this->lang->line('access_cancel_auditing_success')
			,array('转到列表'=>$goto));
		
	}
	
	function on_load(){}
	function on_index(){}
	function on_search(){}
	function on_create(){}
	function on_post(){}
	function on_insert(&$form){}
	function on_edit(&$rs){}
	function on_update(&$form,$rs=null){}
	function on_delete($id){}
	function on_toggle(){}
	function on_moveup(){}
	function on_movedown(){}
	function on_access(){}
	function on_access_delete(&$ids){}
	function on_access_auditing(&$ids){}
	function on_access_cancel_auditing(&$ids){}
}