<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Userdata{
	var $session_id_ttl;

	var $_expire_second;

	function CI_Userdata($config=null){
		$CI=&get_instance();

		$this->_expire_second=$CI->config->item('sess_expiration');
		if(!$this->_expire_second){
			$this->_expire_second=7200;
		}
		ini_set('session.gc_maxlifetime',$this->_expire_second);
		$this->_startup();

		log_message('debug', "Userdata Class Initialized");
	}

	function reset(){
		// copy old session data, including its id
		$old_session_id = session_id();
		$old_session_data = $_SESSION;

		// regenerate session id and store it
		session_regenerate_id();
		$new_session_id = session_id();

		// switch to the old session and destroy its storage
		session_id($old_session_id);
		session_destroy();

		// switch back to the new session id and send the cookie
		session_id($new_session_id);
		session_start();

		// restore the old session data into the new session
		$_SESSION = $old_session_data;

		// update the session creation time
		$_SESSION[':reset:'] = time();

		// session_write_close() patch based on this thread
		// http://www.codeigniter.com/forums/viewthread/1624/
		// there is a question mark ?? as to side affects

		// end the current session and store session data.
		session_write_close();
	}
	
	function reset_by_id($id){
		session_destroy();
		session_id($id);
		$this->_startup();
	}

	function destory(){
		unset($_SESSION);
		if(isset($_COOKIE[session_name()])){
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
	}

	function get($item){
		if ($item == 'session_id'){
			return session_id();
		}
		$this->_check_expire($item);
		return ( ! isset($_SESSION[$item])) ? false : $_SESSION[$item];
	}
	
	function set($newdata = array(), $newval = '', $expire=0){
		if (is_string($newdata)){
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0){
			foreach ($newdata as $key => $val){
				$_SESSION[$key] = $val;
				if($expire>0){
					$_SESSION[':expire:'][$key]=time()+$expire;
				}
			}
		}
	}

	function delete($newdata = array()){
		if (is_string($newdata)){
			$newdata = array($newdata => '');
		}

		if (count($newdata) > 0){
			foreach ($newdata as $key => $val){
				unset($_SESSION[$key]);
			}
		}
	}

	function _check_expire($key){
		if(isset($_SESSION[':expire:'][$key])&&time()>$_SESSION[':expire:'][$key]){
			unset($_SESSION[':expire:'][$key]);
			$this->delete($key);
		}
	}

	function _startup(){
		session_start();

		$session_id_ttl = $this->_expire_second;

		if (is_numeric($session_id_ttl))
		{
			if ($session_id_ttl > 0)
			{
				$this->session_id_ttl = $this->_expire_second;
			}
			else
			{
				$this->session_id_ttl = (60*60*24*365*2);
			}
		}

		// check if session id needs regeneration
		if ( $this->_session_id_expired() )
		{
			// regenerate session id (session data stays the
			// same, but old session storage is destroyed)
			$this->reset();
		}
	}

	function _session_id_expired(){
		if ( !isset( $_SESSION[':reset:'] ) ){
			$_SESSION[':reset:'] = time();
			return false;
		}

		$expiry_time = time() - $this->session_id_ttl;

		if ( $_SESSION[':reset:'] <=  $expiry_time ){
			return true;
		}

		return false;
	}
}

// END CI_Userdata class

/* End of file Userdata.php */
/* Location: ./system/libraries/Userdata.php */