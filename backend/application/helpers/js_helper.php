<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2010, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Javascript(JS) Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://actrut.com
 */

// ------------------------------------------------------------------------

if ( ! function_exists('js_escape'))
{
	function js_escape($str){
			preg_match_all("/[\xc2-\xdf][\x80-\xbf]+|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}|[\x01-\x7f]+/e",$str,$r);
	    $str = $r[0];
	    $l = count($str);
	    for($i=0; $i <$l; $i++){
	        $value = ord($str[$i][0]);
	        if($value < 223){
	            $str[$i] = rawurlencode(utf8_decode($str[$i]));
	        }else if(DIRECTORY_SEPARATOR!='/'){
	        	$str[$i] = "%u".strtoupper(bin2hex(convert_charset($str[$i],"UTF-8","UCS-2")));
	        }
	    }
	    return join("",$str);
	}
}

if ( ! function_exists('js_unescape'))
{
	function js_unescape($str){
		$ret='';
		$len=strlen($str);
		for($i=0;$i<$len;$i++){
			if($str[$i]=='%'&&$str[$i+1]=='u'){
				$val=hexdec(substr($str,$i+2,4));
				if($val<0x7f)$ret.=chr($val);
				elseif($val<0x800)$ret.=chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
				else $ret.=chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
				$i+=5;
			}elseif($str[$i]=='%'){
				$ret.=urldecode(substr($str,$i,3));
				$i+=2;
			}else $ret.=$str[$i];
		}
		return $ret;
	}
}

if ( ! function_exists('js_encode'))
{
	function js_encode($data) {
		if(2==func_num_args())
			return _js_format_scalar(strval(func_get_arg(1))).":".js_encode($data);
		if(is_object($data))$data=get_object_vars($data);
		if(is_scalar($data))return _js_format_scalar($data);
		if(empty($data))return '[]';
		$keys=array_keys($data);
		if(is_numeric(join('',$keys))){
			$data=array_map('js_encode',$data);
			return '['.join(',',$data).']';
		}
		$data=array_map('js_encode',array_values($data),$keys);
		return '{'.join(',',$data).'}';
	}
	function _js_format_scalar($value) {
		if(is_bool($value))$value = $value?'true':'false';
		else if (is_int($value))$value = (int)$value;
		else if (is_float($value))$value = (float)$value;
		else if (is_string($value)) {
			$value=addcslashes($value,"\n\r\"\/\\");
			if(DIRECTORY_SEPARATOR!='/'){
				//linux + utf8 web no need change to do below line 1, other wise bad code
				$value='"'.preg_replace_callback('|[^\x00-\x7F]+|','_js_slash',$value).'"';
			}else{
				$value='"'.$value.'"';
			}
		}else $value='null';
		return $value;
	}
	function _js_slash($data){
		if(is_array($data)){
			$chars=str_split(convert_charset($data[0],"UTF-8","UCS-2"),2);
			$chars=array_map('_js_slash',$chars);
			return join("",$chars);
		}
		$char1=dechex(ord($data{0}));
		$char2=dechex(ord($data{1}));
		return sprintf("\u%02s%02s",$char1,$char2);
	}
}

if ( ! function_exists('js_decode'))
{
	function js_decode($data) {
		static $strings,$count=0;
		if(is_string($data)){
			$data=trim($data);
			if($data{0}!='{'&&$data{0}!='[')return _js_unslash($data);
			$strings=array();
			$data=preg_replace_callback('/"([\s\S]*?(?<!\\\\)(?:\\\\\\\\)*)"/','js_decode',$data);
			//simply dangerous check
			//echo $data;
			$cleanData=str_ireplace(array('true','false','undefined','null','{','}','[',']',',',':','#'),'',$data);
			if(!is_numeric($cleanData)){
				//throw new Exception('Dangerous!The JSONString is dangerous!');
				return NULL;
			}
			$data=str_replace(
				array('{','[',']','}',':','null'),
				array('array(','array(',')',')','=>','NULL')
				,$data);
			$data=preg_replace_callback('/#\d+/','js_decode',$data);
			//prevent error like {123###} can not decode to an array
			@$data=eval("return $data;");
			$strings=$count=0;
			return $data;
		}
		if(count($data)>1) {//store the string
			$strings[]=_js_unslash(str_replace(array('$','\\/'),array('\\$','/'),$data[0]));
			return '#'.($count++);
		}
		//read stroed value
		$index=substr($data[0],1);
		return $strings[$index];
	}
	function _js_unslash($data){
		if(is_array($data)){
			return $data[1].convert_charset(chr(hexdec($data[2])).chr(hexdec($data[3])),"UCS-2","UTF-8");
		}
		return preg_replace_callback('/(?<!\\\\)((?:\\\\\\\\)*)\\\\u([a-f0-9]{2})([a-f0-9]{2})/i','_js_unslash',$data);
	}
}

