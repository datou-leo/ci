<?php

class CI_Category{
	var $table           = 'undefined';
	var $parent_type     = 'Parent Type';
	var $child_mark      = '&nbsp;|-&nbsp;';
	var $child_space     = '&nbsp;&nbsp;';
	
	var $field_id        = 'id';
	var $field_parent_id = 'parent_id';
	var $field_type_id   = 'type_id';
	var $field_title     = 'title';
	var $field_path      = 'path';
	var $field_depth     = 'depth';
	
	var $db;
	var $max_depth       = -1;
	
	function CI_Category($config = null)
	{
		if(is_array($config))
			foreach ($config as $k => $v)
				if(isset($this->$k)) $this->$k = $v;
		
		$CI = &get_instance();
		$CI->load->database();
		$this->db = &$CI->db;
		
		log_message('debug','CI_Category class initialized');
	}
	
	function _ready_table($table)
	{
		if(empty($table))$table = $this->table;
		return $table;
	}
	
	function table_name($table = '')
	{
		if(!empty($table))
		{
			$this->table = $table;
		}
		return $this->table;
	}
	
	function get_all($table = '')
	{
		return getTreeData($this->_ready_table($table),0,1);
	}
	
	function get($id = 0, $table = '')
	{
		$q = $this->db->get_where($this->_ready_table($table),
			array($this->field_id=>$id));
		if($q->num_rows() < 1) return false;
		return $q->row();
	}
	
	function get_by_type_id($id = 0, $table = '')
	{
		$q = $this->db->get_where($this->_ready_table($table),
			array($this->field_type_id=>$id));
		if($q->num_rows() < 1) return false;
		return $q->row();
	}
	
	function get_by_parent_id($id = 0, $table = '')
	{
		return getTreeData($this->_ready_table($table),$id,1);
	}
	
	//取所属全部分类（最高3级）
	function get_types_by_id($id = 0, $table = '')
	{
		$q = $this->db->get_where($this->_ready_table($table),
			array($this->field_id=>$id));
		$q = $q->row();
		$types = array();
		if($q)
		{
			$types = array($id);
			if($q->depth == 0)
			{
				$cate = getTreeData($this->_ready_table($table),$id,1);
				if($cate)
				{
					foreach($cate as $k => $v)
					{
						$types[$k+1] = $v->type_id;
					}
					foreach($cate as $k=>$v)
					{
						$cateitem = $this->db->get_where($this->_ready_table($table),array($this->field_parent_id=>$v->type_id))->result();
						if($cateitem)
						{
							foreach($cateitem as $r)
							{
								array_unshift($types, $r->type_id);
							}
						}
					}
				}
			} elseif($q->depth == 1) {
				$cate = getTreeData($this->_ready_table($table),$id,1);
				if($cate)
				{
					foreach($cate as $k => $v)
					{
						$types[$k+1] = $v->type_id;
					}
				}
			}
		}
		return $types;
	}
	
	function filter_parent($tree, $id_or_title)
	{
		$a = array();
		if(isset($tree[0]))
		{
			$a[0] = $tree[0];
		}
		$found = false;
		$key   = is_int($id_or_title)?'k':'v';
		foreach($tree as $k => $v)
		{
			if(!$found)
			{
				if($$key == $id_or_title)
				{
					$found = true;
				}
			} else {//add child node
				if(strpos($v,$this->child_mark) === false)
				{
					return $a;
				}
				$a[$k] = $v;
			}
		}
		return $a;
	}
	
	function type_tree($table = '')
	{
		return $this->_tree($table,$this->field_type_id,false);
	}
	
	function type_tree_for_edit($table = '')
	{
		return $this->_tree($table,$this->field_type_id,true);
	}
	
	function id_tree($table = '')
	{
		return $this->_tree($table,$this->field_id,false);
	}
	
	function id_tree_for_edit($table = '')
	{
		return $this->_tree($table,$this->field_id,true);
	}
	
	function _tree($table, $field, $for_edit)
	{
		$menu = $this->get_all($table);
		if(is_array($menu) && count($menu) > 0)
		{
			$a = $for_edit?array($this->parent_type):array();
			foreach($menu as $k => $v)
			{
				if(intval($v->{$this->field_parent_id}) > 0)
				{
					$a[$v->{$field}] = str_repeat($this->child_space,$v->{$this->field_depth}-1).$this->child_mark.$v->{$this->field_title};
				} else {
					$a[$v->{$field}] = $v->{$this->field_title};
				}
			}
			//print_r($a);exit;
			return $a;
		}
		
		$menu = $for_edit?array($this->parent_type):array();
		return $menu;
	}
	
	function post($set,$table = '')
	{
		$table = $this->_ready_table($table);
		
		$set[$this->field_parent_id] = intval($set[$this->field_parent_id]);
		unset($set['move_des']);
		if(!$this->db->insert($table,$set))
		{
			return 1;
		}
		$new_id = $this->db->insert_id();
		
		if($set[$this->field_parent_id] > 0)
		{
			$q = $this->db->get_where($table,array($this->field_id=>$set[$this->field_parent_id]));
			if($q->num_rows() < 1)
			{
				return 2;
			}
			$parent = $q->row_array();
			$new = array($this->field_path=>$parent[$this->field_path].','.$new_id);
			$new[$this->field_parent_id] = $parent[$this->field_id];
			$new[$this->field_depth] = count(explode(',',$new[$this->field_path]))-1;
		} else {
			$new=array($this->field_path=>$new_id);
			$new[$this->field_depth]=0;
		}
		$new[$this->field_type_id] = $new_id;
		$new['order_id'] = $new_id;
		if(!$this->db->update($table,$new,array($this->field_id=>$new_id)))
		{
			return 3;
		}
		return 0;
	}
	
	function put($set, $id, $table = '')
	{
		$table = $this->_ready_table($table);
		
		$id = intval($id);
		$arr_types = getTreeData($table,$id,1);//找子类
		$set[$this->field_parent_id] = intval($set[$this->field_parent_id]);
		if($set[$this->field_parent_id] == $id)
		{
			return 3;
		} else {
			//判断是否移动到子类
			if($arr_types)
			{
				$is_err = 0;
				foreach($arr_types as $r)
				{
					if($set[$this->field_parent_id] == $r->id)
					{
						$is_err = 1;
						break;
					}
				}
				if($is_err == 1)
				{
					return 3;
				}
			}
		}
		$move_des = $set['move_des'];
		unset($set['move_des']);
		if($set[$this->field_parent_id] > 0)
		{
			$q = $this->db->get_where($table,array($this->field_id=>$set[$this->field_parent_id]));
			if($q->num_rows() < 1)
			{
				return 2;
			}
			$parent = $q->row_array();
			if($move_des == 1)
			{
				//移动至子类
				$set[$this->field_path]  = $parent[$this->field_path].','.$id;
				$set[$this->field_depth] = count(explode(',',$set[$this->field_path]))-1;
			} else {
				//移动至目标类别前后
				if($parent[$this->field_parent_id] > 0)
				{
					$q2 = $this->db->get_where($table,array($this->field_id=>$parent[$this->field_parent_id]));
					if($q2->num_rows() < 1)
					{
						return 2;
					}
					$rs_parent = $q2->row_array();
					$set[$this->field_parent_id] = $parent[$this->field_parent_id];
					$set[$this->field_path] = $rs_parent[$this->field_path].','.$id;
					$set[$this->field_depth] = count(explode(',',$set[$this->field_path]))-1;
				} else {
					$set[$this->field_parent_id] = 0;
					$set[$this->field_path]      = $id;
					$set[$this->field_depth]     = 0;
				}
			}
		} else {
			if($move_des == 2 || $move_des == 3)
			{
				//独立类别只能为子类
				return 4;
			} else {
				$set[$this->field_path]  = $id;
				$set[$this->field_depth] = 0;
			}
		}
		$is_update_err = 0;
		if(!$this->db->update($table,$set,array($this->field_id=>$id)))
		{
			$is_update_err = 1;
		} else {
			if($move_des == 2 || $move_des == 3)
			{
				//移动至目标类别前后需更改排序
				$where = array($this->field_parent_id=>$set[$this->field_parent_id], $this->field_depth=>$set[$this->field_depth]);
				$this->db->order_by('order_id','asc');
				$arr_parents = $this->db->get_where($table,$where)->result();
				$arr_order = array();
				if($arr_parents)
				{
					foreach($arr_parents as $r)
					{
						if($r->id == $id)
						{
							continue;
						}
						if($r->id == $parent[$this->field_id])
						{
							if($move_des==2)
							{
								$arr_order[] = $id;
								$arr_order[] = $r->id;
							} else {
								$arr_order[] = $r->id;
								$arr_order[] = $id;
							}
						} else {
							$arr_order[] = $r->id;
						}
					}
				}
				if($arr_order)
				{
					$arr_ids = $arr_order;
					sort($arr_ids);
					$num = sizeof($arr_order);
					for($i = 0; $i < $num; $i++)
					{
						$order['order_id'] = $arr_ids[$i];
						$this->db->update($table,$order,array($this->field_id=>$arr_order[$i]));
					}
				}
			}
			//移动子类
			if($arr_types)
			{
				foreach ($arr_types as $r)
				{
					$rs_cparent = $q = $this->db->get_where($table,array($this->field_id=>$r->{$this->field_parent_id}))->row();
					$child[$this->field_path] = $rs_cparent->{$this->field_path}.','.$r->id;
					$child[$this->field_depth] = count(explode(',',$child[$this->field_path]))-1;
					if(!$this->db->update($table,$child,array($this->field_id=>$r->id)))
					{
						$is_update_err = 1;
						break;
					}
				}
			}
		}
		if($is_update_err == 1)
		{
			return 1;
		}
		return 0;
	}
	
	function delete($id, $table = '')
	{
		$table = $this->_ready_table($table);
		
		$id        = intval($id);
		$arr_types = getTreeData($table,$id,1);//找子类
		$is_delerr = 0;
		if($arr_types)
		{
			foreach($arr_types as $r)
			{
				if(!$this->db->delete($table,array($this->field_id=>$r->{$this->field_id})))
				{
					$is_delerr = 1;
					break;
				}
			}
		}
		if($is_delerr == 1)
		{
			return 1;
		}
		if(!$this->db->delete($table,array($this->field_id=>$id)))
		{
			return 1;
		}
		return 0;
	}
	
	function get_max_depth($depth = 0, $table = '')
	{
		if($depth > 0)
		{
			return $depth;
		}
		$table = $this->_ready_table($table);
		if($this->max_depth < 0)
		{
			$this->db->select_max('depth');
			$q = $this->db->get($table);
			$this->max_depth = intval($q->row('depth'))+1;
		}
		return $this->max_depth;
	}
	
	//多级产品搜索
	function split_type($max_depth = 0, $table = '')
	{
		$table     = $this->_ready_table($table);
		$max_depth = $this->get_max_depth($max_depth,$table);
		$types     = array();
		$id_lookup = array(0=>0);
		$q = getTreeData($table,0,2);
		foreach($q as $k => $r)
		{
			$id_lookup[$r['id']] = $r['type_id'];
		}
		for($i = 0; $i < $max_depth; $i++)
		{
			$types[$i] = array();
			foreach(array_keys($q) as $k)
			{
				$r = &$q[$k];
				$r['depth']     = intval($r['depth']);
				$r['parent_id'] = intval($r['parent_id']);
				if($r['depth'] != $i || (!isset($id_lookup[$r['parent_id']]) && $r['depth'] > 0)) continue;
				$parent_type    = $id_lookup[$r['parent_id']];
				$types[$i][$parent_type][$r['type_id']] = &$r;
			}
		}
		return $types;
	}
	
	//获得该分类和他的所有子分类的type_id
	function get_match_type($type_id, &$types, $max_depth = 0, $table = '')
	{
		$table     = $this->_ready_table($table);
		$max_depth = $this->get_max_depth($max_depth,$table);
		$type_in   = array($type_id);
		//echo ($max_depth);exit;
		for($i = 0; $i < $max_depth; $i++)
		{
			foreach($types[$i] as $k => $r)
			{
				if($k != $type_id) continue;
				$type_in = array_merge($type_in,array_keys($r));
				
				if($i == 1)
				{
					foreach($r as $key => $val)
					{
						//print_r($types[$i+1][$val['id']]);
						if(isset($types[$i+1][$val['id']]))
						{
							//print_r($types[$i+1][$val['id']]);exit;
							$type_in = array_merge($type_in,array_keys($types[$i+1][$val['id']]));
						}
					}
				}
			}
		}
		return array_flip(array_flip($type_in));
	}
	
}