<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Article_Model extends CI_Model{

    //查询所有文章
    public function article_all_data()
    {
        return $this->db->select('id,title,author')->from('article')->order_by('id','desc')->get()->result();
    }

    //根据id查询文章
    public function article_detail_data($id)
    {
        return $this->db->select('id,title,author')->from('article')->where('id',$id)->order_by('id','desc')->get()->row();
    }
    //添加文章
    public function article_create_data($data)
    {
        $this->db->insert('article',$data);
        $this->lang->load('common');
        return array('status' => 201,'message' => $this->lang->line('item_post_success'));
    }
    //修改文章
    public function article_update_data($id,$data)
    {
        $this->db->where('id',$id)->update('article',$data);
        $this->lang->load('common');$this->lang->line('item_put_success');
        return array('status' => 200,'message' =>$this->lang->line('item_put_success'));
    }
    //删除文章
    public function article_delete_data($id)
    {
        $this->db->where('id',$id)->delete('article');
        $this->lang->load('common');
        return array('status' => 200,'message' => $this->lang->line('item_delete_success'));
    }
}