<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_menu_model extends Model{

	function Admin_menu_model()
	{
		parent::Model();
		$this->table_name('admin_act');
	}
	
	function get($id, $select = '*')
	{
		$this->db->select($select);
		$q = $this->db->get_where($this->_table,array("id"=>$id));
		return($q->num_rows()>0?$q->row():false);
	}
	
	function get_prev($sort_id, $where = array(), $select = '*')
	{
		$this->db->select($select);
		//$this->db->where("id>",$sort_id,false);
		$this->db->order_by("path","asc");
		$q = $this->db->get_where($this->_table,$where);
		if($q->num_rows() < 1)
		{
			return false;
		}
		$q = $q->result();
		foreach($q as $k => $r)
		{
			if($r->id == $sort_id)
			{
				if(!isset($q[$k-1]))
				{
					return false;
				}
				return $q[$k-1];
			}
		}
		return false;
	}
	
	function get_next($sort_id, $where = array(),$select = '*')
	{
		$this->db->select($select);
		//$this->db->where("id>",$sort_id,false);
		$this->db->order_by("path","asc");
		$q = $this->db->get_where($this->_table,$where);
		if($q->num_rows() < 1)
		{
			return false;
		}
		$q = $q->result();
		foreach($q as $k=>$r)
		{
			if($r->id == $sort_id)
			{
				if(!isset($q[$k+1]))
				{
					return false;
				}
				return $q[$k+1];
			}
		}
		return false;
	}
	
	function swap_item($rs, $target)
	{
		$rs_id = $rs->id;
		$target_id = $target->id;
		unset($rs->id,$target->id);
		unset($rs->path,$target->path);
		unset($rs->depth,$target->depth);
		unset($rs->parent_id,$target->parent_id);
		$ret1 = $this->db->update($this->_table,$rs,array('id'=>$target_id));
		$ret2 = $this->db->update($this->_table,$target,array('id'=>$rs_id));
		if($ret1 && $ret2)
		{
			return true;
		}
		//header('content-type:text/html;charset=utf-8');echo '<pre>';print_r($this->db->queries);echo '</pre>';exit;
		return false;
	}
}