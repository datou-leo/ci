<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Uploadify extends CI_Controller{
    /*
    function index(){
        $this->load->helper('js');

        //Decode JSON returned by /js/uploadify/upload.php
        $file = $this->input->post('filearray');

        echo $file;

        //$data['json'] = js_decode($file);
        //$this->load->view('upload/uploadify',$data);
    }
    */

    function upload(){
        $this->load->library('userdata');

        if(!$this->userdata->get('admin_account')){
            $sid=$this->uri->segment(3,'');
            if(empty($sid)){
                return print 'Authorise required';
            }
            $this->userdata->reset_by_id($sid);
        }

        if(!$this->userdata->get('admin_account')){
            return print 'No permission';
        }

        $this->load->library('upload');
        $upload_url=rtrim($this->config->item('upload.url'),'/\\').'/';
        $upload_dir=rtrim($this->config->item('upload.dir'),'/\\').'/';
        $upload_url.=date('Ym').'/';
        $upload_dir.=date('Ym').'/';

        dirmake($upload_dir);

        $config=array(
            'upload_path'=>$upload_dir,
            'allowed_types'=>'jpg|jpeg|gif|png|zip|rar|doc|docx|ppt|pptx|xls|xlsx|pdf|flv|swf',
            //'max_size'=>$this->config->item('upload.max.kb'),
            'encrypt_name'=>true
        );
        $this->upload->initialize($config);

        if(!$this->upload->do_upload('Filedata')){
            $image=$this->upload->data();
            @unlink($image['full_path']);
            return print $this->upload->display_errors('','');
        }
        $image=$this->upload->data();

        //create thumb?
        $thumb=$this->uri->segment(4,'');
        if(!empty($thumb)){
            $thumb=explode('x',strtolower($thumb));
            $config=array(
                'source_image'=>$upload_dir.$image['file_name'],
                'create_thumb'=>true,
                'maintain_ratio'=>true,
                'fill_border'=>'white',
                'width'=>intval($thumb[0]),
            );
            if(isset($thumb[1])&&intval($thumb[1])>0){
                $config['height']=intval($thumb[1]);
            }
            $this->load->library('image_lib');
            $this->image_lib->initialize($config);
            if(!$this->image_lib->resize()){
                @unlink($image['full_path']);
                return print $this->image_lib->display_errors('','');
            }
            return print $upload_url.$image['file_name']
                .','.$upload_url.$image['raw_name'].'_thumb'.$image['file_ext'];
        }

        return print $upload_url.$image['file_name'];
    }

    function delete(){
        if(!$this->userdata->get('admin_account')){
            return print 'No permission';
            exit;
        }

        $this->load->helper('back');
        $pic=$this->uri->segment(3,'');
        if(empty($pic)){
            return print '{"result":1}';
        }
        $pic=admin_decode_segment($pic);

        //need to check security of head string like "/upload/"
        if(!strpos(' '.$pic,'/upload/')){
            return print '{"result":2}';
        }

        $this->load->helper('upload_clear');
        if(strpos($pic,'_thumb.')){
            $pic=array($pic,str_replace('_thumb.','.',$pic));
        }else{
            $pic=explode('.',$pic);
            $t=array_pop($pic);
            $pic=array(join('.',$pic).'.'.$t,join('.',$pic).'_thumb.'.$t);
        }
        //file_put_contents(BASEPATH.'test.txt',serialize($pic));
        upload_clear($pic);
        return print '{"result":0}';
    }
}