<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Single_flow extends Model{
	var $atom;
	var $ctrl;

	function Single_flow(){
		parent::Model();
	}

	function set_atom(&$atom){
		$this->atom=&$atom;
	}

	function set_ctrl(&$ctrl){
		$this->ctrl=&$ctrl;
	}

	function edit($option=array()){
		$id=$this->uri->segment(3,-1);

		if(!($rs=$this->atom->get($id))){
			return 1;
		}

		return get_defined_vars();
	}

	function put($option=array()){
		$id=$this->uri->segment(3,-1);

		if(!($rs=$this->atom->get($id))){
			return 2;
		}

		$this->load->library('form_validation',$option['rule']);
		if(!$this->form_validation->run()){
			return 1;
		}
		$form=$this->form_validation->to_array();

		if(!$this->atom->put($id,$form)){
			return 3;
		}

		return 0;
	}
}