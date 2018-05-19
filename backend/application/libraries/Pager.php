<?php
/**
 * pager class
 *
 * @author visvoy at gmail.com
 */
class CI_Pager{
	var $table='';
	var $where=array();
	var $like=array();
	var $keyword='';
	var $condition=array();
	var $search_field=array('title');
	var $order_by=array();

	var $db;
	var $uri;
	var $userdata;

	/**
	 * 数组总数
	 *
	 * @var integer
	 */
	var $total = 0;

	/**
	 * 当前页号
	 *
	 * @var integer
	 */
	var $page = 1;

	/**
	 * 为分页而跳过的记录数
	 *
	 * @var unknown_type
	 */
	var $skip = 0;

	/**
	 * 当前页显示多少数据（随页数而变动）
	 *
	 * @var int
	 */
	var $take = 0;

	/**
	 * 每页显示多少数据
	 *
	 * @var integer
	 */
	var $each = 25;

	/**
	 * 最大页号
	 *
	 * @var integer
	 */
	var $max_page = 1;

	/**
	 * 每次最多能显示的页号
	 *
	 * @var integer
	 */
	var $max_cursor = 10;

	/**
	 * 是否显示“上一页”“下一页”
	 *
	 * @var boolean
	 */
	var $show_prev_next = true;

	/**
	 * 链接地址（替换用）
	 *
	 * 替换 {page} 为 $this->page
	 *
	 * @var string
	 */
	var $link = '{page}';

	/**
	 * 通用页号开启样式，例如<span>
	 *
	 * @var string
	 */
	var $tag_open = '';

	/**
	 * 通用页号关闭样式，例如</span>
	 *
	 * @var string
	 */
	var $tag_close = '';

	/**
	 * “上一页”开启样式
	 *
	 * @var string
	 */
	var $prev_open = '';

	/**
	 * “上一页”关闭样式
	 *
	 * @var string
	 */
	var $prev_close = '';

	/**
	 * “下一页”开启样式
	 *
	 * @var string
	 */
	var $next_open = '';

	/**
	 * “下一页”关闭样式
	 *
	 * @var string
	 */
	var $next_close = '';

	/**
	 * “页号”开启样式
	 *
	 * @var string
	 */
	var $cursor_open = '';

	/**
	 * “页号”关闭样式
	 *
	 * @var string
	 */
	var $cursor_close = '';

	/**
	 * 是否激活当前页的链接（默认不激活）
	 *
	 * @var boolean
	 */
	var $active_current_link = false;

	/**
	 * “当前页号”的样式类
	 *
	 * @var string
	 */
	var $current_link_class = '';

	/**
	 * “当前页号”开启样式
	 *
	 * @var string
	 */
	var $current_open = '<strong>';

	/**
	 * “当前页号”关闭样式
	 *
	 * @var string
	 */
	var $current_close = '</strong>';

	/**
	 * “上一页”的替换文本，默认是<<
	 *
	 * @var string
	 */
	var $prev_text = '&lt;&lt;';

	/**
	 * “下一页”的替换文本，默认是>>
	 *
	 * @var string
	 */
	var $next_text = '&gt;&gt;';

	/**
	 * 页码显示类型
	 *
	 * @var string
	 */
	var $type='all.active';

	var $_type=array(
		'all.active'=>array(
			'tag_open'=>'',
			'tag_close'=>'',
			'current_open'=>'',
			'current_close'=>'',
			'show_prev_next'=>true,
			/*'prev_text'=>'&laquo;',
			'next_text'=>'&raquo;',*/
			'prev_text'=>'上一页',
			'next_text'=>'下一页',
			'active_current_link'=>true,
			'current_link_class'=>'active',
		),
	);

	// Constructor
	function CI_Pager($config = null){
		$this->init($config);
	}

	function init($config = null){
		if(is_array($config))
			foreach($config as $k => $v)
				if(isset($this->$k))$this->$k = $v;
		if(isset($this->type))
			foreach($this->_type[$this->type]as $k => $v)
				if(isset($this->$k))$this->$k = $v;
		if(!empty($this->keyword)){
			$a=explode(' ',$this->keyword);
			foreach($a as $v)
				if(!in_array($v,$this->like))
					$this->like[]=$v;
		}

		if($this->table){
			if(!is_object($this->db)){
				$CI=&get_instance();
				$CI->load->database();
				$this->db=&$CI->db;
			}
			if(is_array($this->where)&&count($this->where)>0)
				$this->db->where($this->where);
			if(is_array($this->like)&&count($this->like)>0)
				$this->_resolve_like_condition($this->like);
			$this->total=$this->db->count_all_results($this->table);
		}

		$this->_fix_parameter();
	}

	function _fix_parameter(){
		$this->page=intval($this->page);
		$this->take=$this->skip=0;
		// 修正 page/skip/take 的值，当其超出范围
		$this->each=($this->each>0)?$this->each:1;
		$this->take=$this->each;
		$this->max_page=ceil($this->total/$this->take);
		$this->max_page=($this->max_page<1)?1:$this->max_page;
		$this->page=($this->page<1)?1:$this->page;
		$this->page=($this->page>$this->max_page)
			?$this->max_page
			:$this->page;
		$this->skip=($this->page-1)*$this->take;
		$this->take=($this->total-$this->skip<$this->take)
			?($this->total-$this->skip)
			:$this->take;
	}

	function segment($page_n=3,$order_n=4,$each_n=5,$cond_n=6,$def_order='sort_id:desc'){
		$CI=&get_instance();
		if(!is_object($this->uri)){
			$this->uri=&$CI->uri;
			$this->db=&$CI->db;
		}

		$page=$this->uri->segment($page_n,1);
		$order=$this->uri->segment($order_n,'null');
		$cond=$this->uri->segment($cond_n,'null');
		$each=$this->uri->segment($each_n,'null');
		//fix params
		if('null'==$each&&is_object($CI->userdata)){
			$each=intval($CI->userdata->get('page_each'));
			if($each<1)$each=25;
		}
		if('null'==$order){
			$order=$def_order;
		}
		$order_by=explode(':',$order);
		$order_by=array($order_by[0]=>$order_by[1]);
		$this->order_by=$order_by;

		$this->page=$this->uri->segments[$page_n]=$page;
		$this->uri->segments[$order_n]=$order;
		$this->uri->segments[$cond_n]=$cond;
		$this->each=$this->uri->segments[$each_n]=$each;
		if(is_object($CI->userdata)){
			$CI->userdata->set('page_each',$each);
		}

		$this->_resolve_keyword($cond);

		return array($page,$order,$each,$cond,$this->keyword);
	}

	function type_segment($page_n=3,$order_n=4,$each_n=5,$cond_n=6,$def_order='sort_id:desc'){
		$a=$this->segment($page_n+1,$order_n+1,$each_n+1,$cond_n+1,$def_order);
		$type=$this->uri->segment($page_n,-1);
		$this->uri->segments[$page_n]=$type;
		array_unshift($a,$type);
		return $a;
	}

	function edit_segment($page_n=3,$order_n=4,$each_n=5,$cond_n=6,$def_order='sort_id:desc'){
		$a=$this->segment($page_n+1,$order_n+1,$each_n+1,$cond_n+1,$def_order);
		$id=$this->uri->segment($page_n,-1);
		$this->uri->segments[$page_n]=$id;
		array_unshift($a,$id);
		return $a;
	}

	function type_edit_segment($page_n=3,$order_n=4,$each_n=5,$cond_n=6,$def_order='sort_id:desc'){
		$a=$this->segment($page_n+2,$order_n+2,$each_n+2,$cond_n+2,$def_order);
		$type=$this->uri->segments[$page_n]=$this->uri->segment($page_n,-1);
		$id=$this->uri->segments[$page_n+1]=$this->uri->segment($page_n+1,-1);
		array_unshift($a,$id);
		array_unshift($a,$type);
		return $a;
	}

	// 总共12页，显示10页（包括首位页），出现奇偶时候，左边+1
	// 在首页：[1],2,3,4,5,6,7,8,9,...12
	// 在尾页：1...,4,5,6,7,8,9,10,11,[12]
	// 在其中：1...,2,3,4,5,[6],7,8,9,...12
	//		<< 1... ,3,4,5,6,[7],8,9,10,11, ...13 >>
	// 如果少于10页就全选吧
	function create_link($data_type = 'object'){
		// 计算应当显示的页号范围
		$this->each = ($this->each > 0) ? $this->each : 1;
		$this->max_page = ceil($this->total/$this->each);
		$this->max_page = ($this->max_page < 1) ? 1 : $this->max_page;
		$this->page = ($this->page < 1) ? 1 : $this->page;
		$this->page = ($this->page > $this->max_page) ? $this->max_page : $this->page;

		// 至少要显示3个页号（首页+当前页+尾页）
		$max = ($this->max_cursor > 2) ? $this->max_cursor : 3;
		$max = ($max > $this->max_page) ? $this->max_page : $max;

		$t = '';
		$s = &$this->tag_open;
		$e = &$this->tag_close;
		$ts = empty($this->cursor_open) ? $s : $this->cursor_open;
		$te = empty($this->cursor_close) ? $e : $this->cursor_close;
		$a = array();

		// 总页数少于设定的每次最多显示页号，那就全部显示
		if($max==$this->max_page){
			for($i=1;$i<=$max;$i++)$a[]=$i;
		}else{ // 总页数超过每次最多显示页号，首页+尾页+中间页=10页（每次显示=10）
			$i = floor($max / 2);
			$i = ($i < 1) ? 1 : $i;

			if ($this->page - $i < 1){
				// 左边不够的情况
				$start = 1;
				$end = $start + $max - 1;
			}elseif ($this->page + $i > $this->max_page){
				// 右边不够的情况
				$end = $this->max_page;
				$start = $end - $max + 1;
			}else{ // 左右两边都够的情况
				$start = $this->page - $i;
				$end = $start + $max - 1;
			}

			for($i=$start;$i<=$end;$i++)$a[]=$i;

			// 需要添加“首页”
			if($start>1)$a[0] = 1;

			// 需要添加“尾页”
			if(end($a)<$this->max_page)$a[count($a)-1]=$this->max_page;
		}

		// “上一页”
		if ($this->show_prev_next){
			$i = $this->page - 1;
			$i = ($i < 1) ? 1 : $i;
			$t .= empty($this->prev_open) ? $s : $this->prev_open;
			$t .= $this->_autolink($i, $this->prev_text,'_self',false);
			$t .= empty($this->prev_close) ? $e : $this->prev_close;
		}

		// start to end
		foreach ($a as $k => $i){
			$end = "$i";
			if ($i == 1 && isset($a[1]) && $a[1] != 2)
				$end .= "...";
			elseif ($i == $this->max_page && isset($a[$k - 1]) && $i - $a[$k - 1] != 1)
				$end = "...".$end;

			if ($i != $this->page)
				$t .= $ts.$this->_autolink($i, $end).$te;
			else{ // 当前页
				$t .= empty($this->current_open) ? $s : $this->current_open;
				/*
				if (!$this->active_current_link)
				{
					$t .= $end;
				}
				else // 当前页激活链接，需要欺骗一下 _link() 方法
				{
					$start = $this->page;
					$this->page = -1;
					$t .= $this->_autolink($i, $end);
					$this->page = $start;
				}
				*/
				$t .= $this->_autolink($i, $end);
				$t .= empty($this->current_close) ? $e : $this->current_close;
			}
		}

		// “下一页”
		if ($this->show_prev_next){
			$i = $this->page + 1;
			$i = ($i > $this->max_page) ? $this->max_page : $i;
			$t .= empty($this->next_open) ? $s : $this->next_open;
			$t .= $this->_autolink($i, $this->next_text,'_self',false);
			$t .= empty($this->next_close) ? $e : $this->next_close;
		}

		if($this->table){
			if(!is_object($this->db)){
				$CI=&get_instance();
				$this->db=&$CI->db;
			}
			if(is_array($this->where)&&count($this->where)>0)
				$this->db->where($this->where);
			if(is_array($this->like)&&count($this->like)>0)
				$this->_resolve_like_condition($this->like);
			if(is_string($this->order_by)&&strpos($this->order_by,',')){
				$this->db->order_by($this->order_by);
			}else foreach($this->order_by as $k=>$v){
				$this->db->order_by($k,$v);
			}
			$data=$this->db->get_where($this->table,null,$this->take,$this->skip);
			$data=$data->result($data_type);
		}else $data=array();

		return array($t,$data,$this->page,$this->total);
	}

	function _autolink($page, $text, $target = '_self', $activePrevNext=true){
		if ($page == $this->page && !$this->active_current_link)
			return $text;
		if($page==$this->page&&''!=$this->current_link_class&&$activePrevNext)
			$class=' class="'.$this->current_link_class.'"';
		else $class='';
		$page = str_replace('{page}', $page, $this->link);
		return '<a href="'.$page.'" target="'.$target.'" '.$class.'>'.$text.'</a>';
	}

	function _resolve_keyword($cond){
		if(!empty($cond)&&'null'!=$cond){
			$CI=&get_instance();
			$cond=@$CI->uri->decode($cond);
			$cond=@unserialize($cond);
			if(is_array($cond)){
				$this->condition=$cond;
			}
		}
		if(isset($this->condition['keyword'])){
			$this->keyword=$this->condition['keyword'];
		}
	}

	function _resolve_like_condition(&$like){
		if(!is_object($this->db)){
			$CI=&get_instance();
			$this->db=&$CI->db;
		}

		//解决了AR条件AND和OR没有括号区别，导致搜索到错误的结果
		if(count($like)>0&&count($this->search_field)>0){
			if(is_string($this->search_field)){
				$this->search_field=explode(',',$this->search_field);
			}
			foreach($this->search_field as $k=>$key){
				if(strpos($key,'.')){
					$this->search_field[$k]=$this->db->_protect_identifiers($key,true,false,true);
				}else{
					$this->search_field[$k]=$this->db->protect_identifiers($key);
				}
			}
			foreach($like as $val){
				$a=array();
				$val = $this->db->escape_like_str($val);
				foreach($this->search_field as $key){
					$a[]="$key LIKE '%{$val}%'";
				}
				$a=join(' OR ',$a);
				$this->db->where("($a)",'',false);
			}
		}
	}
}