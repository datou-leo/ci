<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dataset_atom extends Model
{
	function Dataset_atom()
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
			foreach($b as $v)
			{//make sure photo and thumb cleared together
				if(strpos($v,'_thumb.'))
				{
					$v = str_replace('_thumb.','.',$v);
					if(!in_array($v,$b))
					{
						$b[] = $v;
					}
				}
			}
			upload_clear($b);
			foreach($b as $v)
			{
				if(isset($rs->thumb) && $rs->thumb == $v)
				{
					$form['thumb'] = '';
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
	
	function upfile(&$form, $rs = null, $key = 'userdoc_list')
	{
		$b = $this->input->post('upload_clear');
		if(is_array($b))
		{
			$this->load->helper('upload_clear');
			upload_clear($b);
			foreach($b as $v)
			{
				if(isset($rs->file) && $rs->file == $v)
				{
					$form['file'] = '';
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
				$form['file']=$v;
			}
		}
	}
	
	function upflv(&$form, $rs = null, $key = 'userflv_list')
	{
		$b = $this->input->post('upload_clear');
		if(is_array($b))
		{
			$this->load->helper('upload_clear');
			upload_clear($b);
			foreach($b as $v)
			{
				if(isset($rs->video) && $rs->video == $v)
				{
					$form['video'] = '';
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
				$form['video'] = $v;
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
		$this->db->where("sort_id>",$sort_id,false);
		$this->db->order_by("sort_id","asc");
		$q = $this->db->get_where($this->_table,$where,1);
		return($q->num_rows()>0?$q->row():false);
	}
	
	function get_next($sort_id, $where = array(),$select = '*')
	{
		$this->db->select($select);
		$this->db->where("sort_id<",$sort_id,false);
		$this->db->order_by("sort_id","desc");
		$q = $this->db->get_where($this->_table,$where,1);
		return($q->num_rows()>0?$q->row():false);
	}
	
	function swap_sort_id($r1, $r2)
	{
		if(is_object($r1))
		{
			$r1 = get_object_vars($r1);
		}
		if(is_object($r2))
		{
			$r2 = get_object_vars($r2);
		}
		if(!$this->db->update($this->_table
			,array("sort_id"=>$r2['sort_id']),array("id"=>$r1['id'])))
		{
			return false;
		}
		if(!$this->db->update($this->_table
			,array("sort_id"=>$r1['sort_id']),array("id"=>$r2['id'])))
		{
			return false;
		}
		return true;
	}
	
	function post($form)
	{
		if(!$this->db->insert($this->_table,$form))
		{
			return false;
		}
		if(!($ins=$this->db->insert_id()))
		{
			return false;
		}
		$this->db->update($this->_table,array('sort_id'=>$ins),array('id'=>$ins));
		return true;
	}
	
	function put($id, $form)
	{
		return($this->db->update($this->_table,$form,array("id"=>$id))?true:false);
	}
	
	function delete($id)
	{
		if(!($rs = $this->get($id)))
		{
			return false;
		}
		if(!$this->db->delete($this->_table,array("id"=>$id)))
		{
			return false;
		}
		if(isset($rs->photo) || isset($rs->thumb)
			 || isset($rs->file) || isset($rs->video))
		{
			$a = array();
			$this->load->helper('upload_clear');
			if(isset($rs->photo))
			{
				$a['photo'] = $rs->photo;
			}
			if(isset($rs->thumb))
			{
				$a['thumb'] = $rs->thumb;
			}
			if(isset($rs->file))
			{
				$a['file'] = $rs->file;
			}
			if(isset($rs->video))
			{
				$a['video'] = $rs->video;
			}
			upload_clear($a);
		}
		return true;
	}
	
	function toggle($id, $col, $value = null)
	{
		if(!($q=$this->get($id, $col)))
		{
			return false;
		}
		if(is_null($value))
		{
			$q->{$col} = 1 - $q->{$col};
		} else {
			$q->{$col} = $value;
		}
		if(!$this->put($id,$q))
		{
			return false;
		}
		return $q->{$col};
	}
}