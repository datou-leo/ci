<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_user_model extends Model
{
	function Admin_user_model()
	{
		parent::Model();
		$this->load->database();
		$this->table_name('admin_user');
	}
	
	function get($account, $select = '*')
	{
		$this->db->select($select);
		$q = $this->db->get_where($this->_table,array("account"=>$account));
		return($q->num_rows()>0?$q->row():false);
	}
	
	function post($form)
	{
		if(!$this->db->insert($this->_table,$form))
		{
			return false;
		}
		return true;
	}
	
	function put($account, $form)
	{
		return($this->db->update($this->_table,$form,array("account"=>$account))?true:false);
	}
	
	function delete($account)
	{
		return($this->db->delete($this->_table,array("account"=>$account))?true:false);
	}
}