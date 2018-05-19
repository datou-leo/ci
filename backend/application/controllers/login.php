<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

    var $rule_post = array(
        array(
            'field' => 'account',
            'label' => 'lang:account',
            'rules' => 'required|xss_clean'),
        array(
            'field' => 'password',
            'label' => 'lang:password',
            'rules' => 'required|md5|callback_account_check'),
        array(
            'field' => 'remember',
            'rules' => 'intval'),
    );

    function account_check($password){
        $account = $this->input->post('account');
        $this->db->select('account,login_count');
        $rs = $this->db->get_where('admin_user'
            ,array('account'=>$account,'password'=>$password));
        if ($rs->num_rows() < 1)
        {
            $this->_add_login_attempt();
            $this->form_validation->set_message('account_check', lang('login_incorrect'));
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    function Login()
    {
        parent::__construct();
        $this->load->config('admin/login');
        $this->load->language('admin/login');
    }

	public function index()
	{

        if($this->backauth->get_account())
        {
            return redirect();
        }
        if($this->userdata->get('admin_ban'))
        {
            return redirect('banned');
        }

        // has remembered account?
        $account = $this->input->cookie('admin_account');
        if(empty($_POST['account']) && !empty($account))
        {
            $_POST['account'] = $account;
            $this->load->vars('remember',1);
        }
        if(isset($_POST['remember']) && intval($_POST['remember']) == 1)
        {
            $this->load->vars('remember',1);
        }
        $this->load->view('login');
	}


    function post(){
        $this->load->library('form_validation',$this->rule_post);
        if(!$this->form_validation->run())
        {
            return $this->index();
        }
        $form = $this->input->post();

        //verify account
        $this->load->database();
        $this->db->select('account,login_count');
        $rs = $this->db->get_where('admin_user'
            ,array('account'=>$form['account'],'password'=>$form['password']));
//        if($rs->num_rows() < 1)
//        {
//            $this->_add_login_attempt();
//            $this->form_validation->set_error('account','login_incorrect');
//            return $this->index();
//        }
        $rs = $rs->row_array();

        //proceed login
        $this->_clear_login_attempt();
        $this->db->update($this->backauth->auth_table_user,array(
            'login_ip'    => $this->input->ip_address(),
            'login_count' => intval($rs['login_count'])+1,
            'timelast'    => time(),
        ),array('account'=>$rs['account']));
        $this->backauth->set_account($rs['account']);
        $this->input->set_cookie('admin_account',$rs['account'],set_value('remember')?3600*24:'');
        redirect();
    }


    function delete()
    {
        $this->userdata->delete('admin_account');
        redirect('login');
    }

    function _add_login_attempt()
    {
        $ip = $this->input->ip_address();
        $this->load->database();
        $this->db->select('attempt,ban,timeline,timelast');
        $r = $this->db->get_where('admin_ban',array('ip'=>$ip));

        //first time input? then insert log
        if($r->num_rows() < 1)
        {
            $r = array(
                'ip'       => $ip,
                'ban'      => 0,
                'attempt'  => 1,
                'timelast' => time(),
            );
            $this->db->insert('admin_ban',$r);
            return;
        }
        $r = $r->row_array();

        //input timeout reset attempt
        if(time()-$r['timelast'] > 1800)
        {
            $this->db->update('admin_ban',array(
                'attempt'=>1,
                'timelast'=>time()),
                array('ip'=>$ip));
            return;
        }

        //within banned?
        if($r['ban']>0 && time() > $r['timeline'])
        {
            $this->userdata->set('admin_ban',1,$this->config->item('login_ban_minute')*60);
            redirect('banned');
            return;
        }

        //add input attempt
        if(++$r['attempt'] < $this->config->item('login_attempt_max'))
        {
            $this->db->update('admin_ban',array(
                'attempt'=>$r['attempt'],
                'timelast'=>time()),
                array('ip'=>$ip));
            $this->load->vars('attempt',$r['attempt']);
            return;
        }

        //exceed attempt times, banned
        $this->userdata->set('admin_ban',1,$this->config->item('login_ban_minute')*60);
        $r = array(
            'reason'   => $this->lang->line('login_attempt_ban_reason'),
            'timeline' => time()+$this->config->item('login_ban_minute')*60,
            'ban'      => 1,
        );
        $this->db->update('admin_ban',$r,array('ip'=>$ip));
        redirect('banned');
    }

    function _clear_login_attempt()
    {
        $this->load->database();
        $this->db->delete('admin_ban',array('ip'=>$this->input->ip_address()));
    }
}

