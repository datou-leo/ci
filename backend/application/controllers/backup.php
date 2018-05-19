<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Backup extends CI_Controller
{
	var $caption;
	var $title;
	var $iframe;
	var $view = 'backup';

	var $rule_post = array(
		array(
			'field' => 'way',
			'rules' => 'trim'),
		array(
			'field' => 'place',
			'rules' => 'trim'),
		array(
			'field' => 'compress',
			'rules' => 'intval'),
		);

	function Backup()
	{
		parent::__construct();
		list($this->caption,$nav,$sub) = $this->backview->menu_caption_index();
		$this->load->vars(array(
			'title'     => $this->title=strtolower(get_class()),
			'caption'   => $this->caption,
			'nav_index' => $nav,
			'sub_index' => $sub));
	}

	function index()
	{
		$this->load->database();
		if($this->db->dbdriver != 'mysql' && $this->db->dbdriver!='mysqli')
		{
			$this->backview->failure("只能备份MySQL和MySQLi类型的数据库");
		}
		$database_name = $this->db->database;

		$this->backview->view("ajax/{$this->view}",get_defined_vars());
	}

	function post()
	{
		$this->load->library('form_validation',$this->rule_post);
		if(!$this->form_validation->run())
		{
			return $this->index();
		}
		$rs = $this->input->post();
		if(empty($rs['way']))
		{
			$this->form_validation->set_error('way','请选择备份方式');
			return $this->index();
		}
		if(empty($rs['place']))
		{
			$this->form_validation->set_error('place','请选择保存位置');
			return $this->index();
		}
		//init fields
        if(array_key_exists('compress',$rs)){
            $rs['compress'] = intval($rs['compress']);
        }else{
            $rs['compress'] = 0;
        }


		$this->load->database();
		$this->load->library("mysql_backup",array(
            'host'         => $this->db->hostname,
            'port'         => $this->db->port?$this->db->port:'3306',
            'userName'     => $this->db->username,
            'userPassword' => $this->db->password,
            'database' => $this->db->database,
            'dbprefix'     => $this->db->dbprefix,
            'charset'      => $this->db->char_set,
            'path'         => FCPATH.'/backup',
            'isCompress'   => $rs['compress']?1:0, //是否开启gzip压缩
            'isDownload'   => $rs['place'] == 'download' ? 1 : 0,
            'isPart'       => $rs['way'] == 'part' ? 1 : 0,
		));
		$this->mysql_backup->setDBName($this->db->database);

		$this->mysql_backup->backup();
		if(!$this->mysql_backup->result)
		{
			return;
		}

		$this->load->database();
        $this->db->where(array('category'=>'admin','config'=>'backup'));
        $this->db->update('config',array('value'=>time()));

		if($rs['place'] == 'download')
		{
			if($rs['way'] == 'part')
			{
				//echo "备份完成，点击下载分卷文件<br /><br />";
				$filelist = array();
				foreach($this->mysql_backup->fileList as $k => $v)
				{
					$v = './backup/'.basename($v);
					$filelist[] = ($k+1).'. <a href="'.$v.'" target="_blank">'.$v.'</a><br /><br />';
				}
				return;
			}
		}

        $this->backview->is_iframe_post(0);
        $this->lang->load('common');//加载common_lang.php
        $this->backview->success($this->lang->line('item_put_success'));
	}

	function download()
	{
		$file = $this->uri->segment(3);
		$dir  = FCPATH.'/backup/';
		if(strpos($file,'/') !== false || strpos($file,'\\') !== false)
		{
			admin_failure("参数错误"
				,"{$this->title}/index/{$this->class_id}/1");
		}
		$fileName = $dir.$file;
		@ob_end_clean();
		header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Length: '.filesize($fileName));
		header('Content-Disposition: attachment; filename='.basename($fileName));
		readfile($fileName);
	}
}