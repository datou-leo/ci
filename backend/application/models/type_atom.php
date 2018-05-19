<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Type_atom extends Model
{
	function Type_atom()
	{
		parent::Model();
		$this->load->database();
	}
	
	function upload(&$form, $rs = null, $key = 'userfile_list')
	{
		$b = $this->input->post('upload_clear');
		if(is_array($b))
		{
			$this->load->helper('upload_clear');
			upload_clear($b);
			foreach($b as $v)
			{
				if(isset($rs->thumb) && $rs->thumb == $v)
				{
					$form['thumb'] = '';
				}
				if(strpos($v,'_thumb.'))
				{
					$v = str_replace('_thumb.','.',$v);
				}
				if(isset($rs->photo) && $rs->photo == $v)
				{
					$form['photo'] = '';
				}
			}
		}
		$a = $this->input->post($key);
		if(is_array($a) && count($a) > 0)
		{
			foreach($a as $v)
			{
				$k = $this->_url_to_path($v);
				if(empty($k) || !is_file($k))
				{
					continue;
				}
				if(strpos($v,'_thumb'))
				{
					$form['thumb'] = $v;
				} else {
					$form['photo'] = $v;
				}
			}
		}
	}
	
	function _url_to_path($url)
	{
		$upurl = rtrim(config_item('upload.url'),'/\\').'/';
		$updir = rtrim(config_item('upload.dir'),'/\\').'/';
		if(empty($url) || is_int($url))
		{
			return '';
		}
		if(!strncasecmp($url,$upurl,strlen($upurl)))
		{
			$url = $updir.substr($url,strlen($upurl));
		}
		return $url;
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
	
	function get_next($sort_id, $where = array(), $select = '*')
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
		$rs_id     = $rs->id;
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
		return false;
	}
}