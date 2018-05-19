<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//wide-char=1,ascii=0.5
function strcut($src, $cutlength, $dot = '…')
{
	$ret = '';
	$i = $n = $ulen = 0;
	$strlen=strlen($src);
	while(($n < $cutlength) && ($i <= $strlen))
	{
		$temp   = substr($src,$i,1);
		$ascnum = ord($temp);
		if($ascnum >= 224){
			$ret = $ret.substr($src,$i,3);
			$i+=3;$n++;
		} else if($ascnum >= 192) {
			$ret = $ret.substr($src,$i,2);
			$i+=2;$n++;
		} else if($ascnum >= 65 && $ascnum <= 90) {
			$ret = $ret.substr($src,$i,1);
			$i++;$n++;
		} else {
			$ret = $ret.substr($src,$i,1);
			$i++;$n+=0.5;
		}
	}
	//if($strlen>$cutlength)
	if(strcmp($src,$ret)) $ret .= $dot;
	return $ret;
}
function htmlchars($string)
{
	$string = preg_replace("/\s(?=\s)/", '', trim(strip_tags($string)));
	return str_replace("&nbsp;","",$string);
}
function myEscape($str)
{
	preg_match_all("/[\xc2-\xdf][\x80-\xbf]+|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}|[\x01-\x7f]+/e",$str,$r);
	$str = $r[0];
	$l   = count($str);
	for($i = 0; $i < $l; $i++){
		$value = ord($str[$i][0]);
		if($value < 223)
		{
			$str[$i] = rawurlencode(utf8_decode($str[$i]));
			//先将utf8编码转换为ISO-8859-1编码的单字节字符，urlencode单字节字符.
			//utf8_decode()的作用相当于iconv("UTF-8","CP1252",$v)。
		//}else{
		//	$str[$i]="%u".strtoupper(bin2hex(iconv("UTF-8","UCS-2",$str[$i])));
		//}

		} else if(DIRECTORY_SEPARATOR != '/') {
        	//red hat和一些linux服务器要注释掉下面一行，否则js getcookiex乱码
        	$str[$i] = "%u".strtoupper(bin2hex(iconv("UTF-8","UCS-2",$str[$i])));
        }
	}
	return join("",$str);
}

function myUnescape($str)
{
	$ret = '';
	$len = strlen($str);
	for($i = 0; $i < $len; $i++){
		if($str[$i] == '%' && $str[$i+1] == 'u')
		{
			$val = hexdec(substr($str,$i+2,4));
			if($val < 0x7f) $ret .= chr($val);
			elseif($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
			else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
			$i+=5;
		} elseif($str[$i] == '%') {
			$ret .= urldecode(substr($str,$i,3));
			$i   += 2;
		}else $ret .= $str[$i];
	}
	return $ret;
}

//符合 uri segment 标准的64编码
//在 javascript 端请用 $.base64.decodex() 解码
function myBase64Encode($s, $in_charset = 'UTF-8')
{
	if(strtoupper($in_charset) != 'UTF-8')
		$s=iconv($in_charset,'UTF-8',$s);
	return str_replace('/','|',base64_encode($s));
}

//符合 uri segment 标准的64解码
//在 javascript 端请用 $.base64.encodex() 编码
function myBase64Decode($s, $out_charset = 'UTF-8')
{
	$s = base64_decode(str_replace('|','/',$s));
	if(strtoupper($out_charset) != 'UTF-8')
		$s = iconv('UTF-8',$out_charset,$s);
	return $s;
}

//获取树形数据
function getTreeData($table, $parent_id, $data_type)
{
	$CI = &get_instance();
	$CI->load->database();
	$CI->db->where(array('parent_id'=>$parent_id));
	$CI->db->order_by('order_id','asc');
	$q  = $CI->db->get($table);
	$rs_data   = array();
	$arr_child = array();
	if($data_type == 1)
	{
		$rs_type = $q->result();
		if($rs_type)
		{
			foreach($rs_type as $r)
			{
				$rs_data[] = $r;
				$rs_child  = getTreeData($table,$r->id,$data_type);
				if($rs_child)
				{
					$arr_child[$r->id] = 1;
					$rs_data = array_merge($rs_data,$rs_child);
				} else {
					$arr_child[$r->id] = 0;
				}
			}
		}
		if($rs_data)
		{
			foreach ($rs_data as $k => $r)
			{
				if(isset($arr_child[$r->id]))
				{
					$rs_data[$k]->is_havechild = $arr_child[$r->id];
				}
			}
		}
	} else {
		$rs_type = $q->result_array();
		if($rs_type)
		{
			foreach($rs_type as $r)
			{
				$rs_data[] = $r;
				$rs_child  = getTreeData($table,$r['id'],$data_type);
				if($rs_child)
				{
					$arr_child[$r['id']] = 1;
					$rs_data = array_merge($rs_data,$rs_child);
				} else {
					$arr_child[$r['id']] = 0;
				}
			}
		}
		if($rs_data)
		{
			foreach($rs_data as $k => $r)
			{
				if(isset($arr_child[$r['id']]))
				{
					$rs_data[$k]['is_havechild'] = $arr_child[$r['id']];
				}
			}
		}
	}
	return $rs_data;
}
//获取真实IP
function getIP()
{
    static $realip;
    if (isset($_SERVER))
    {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR"))
        {
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    return $realip;
}

/**
 * 获取 IP  地理位置
 * 淘宝IP接口
 * @Return: array
 */
function getCity($ip)
{
	$url = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
	$ip  = json_decode(file_get_contents($url));
	if((string)$ip->code == '1')
	{
	  return false;
	}
	$data = (array)$ip->data;
	return $data;
}

function get_info()
{
	$url = "http://125.69.126.66:9999/mixc/loginSite?cardNo=1160000835&password=250041";
	$datas = json_decode(file_get_contents($url));
	// print_r($datas);
	return $datas;
}

function sendMail($html, $subject, $email)
{
	header('Content-type:text/html;charset=utf-8');
	include '../vendor/PHPMailer/class.phpmailer.php';

	$mail = new PHPMailer(true);
	
	$mail->IsSMTP();
	$mail->SMTPDebug  = 0;
	$mail->SMTPAuth   = true;
	$mail->Host       = "smtp.163.com";
	$mail->Port       = 25; 
	$mail->CharSet    = "UTF-8";
	$mail->Encoding   = "base64";
	$mail->Username   = "xxxxxx@163.com";
	$mail->Password   = "xxxxxxx";
	$mail->SetFrom('xxxxxx@163.com', 'datou'); // 发送者邮箱
	$mail->AddAddress($email, 'datou');
	
	$mail->Subject = $subject;
	$mail->MsgHTML($html);
	$mail->IsHTML(true);
	$mail->Send();
}

if ( ! function_exists('set_edit_value'))
{
    function set_edit_value($key,&$array){
        $val=set_value($key);
        if(!$val&&!isset($_POST[$key])){
            $val=(is_object($array))?$array->$key:$array[$key];
        }
        return $val;
    }
}

if ( ! function_exists('form_new_input'))
{
    function form_new_input($name,$noSetValue='',$extra=''){
        return form_input($name,set_value($name)?set_value($name):$noSetValue,$extra);
    }
}

if ( ! function_exists('form_edit_input'))
{
    function form_edit_input($name,&$data,$extra=''){
        return form_input($name,set_edit_value($name,$data),$extra);
    }
}

if ( ! function_exists('form_new_password'))
{
    function form_new_password($name,$noSetValue='',$extra=''){
        return form_password($name,set_value($name)?set_value($name):$noSetValue,$extra);
    }
}

if ( ! function_exists('form_edit_password'))
{
    function form_edit_password($name,&$data,$extra=''){
        return form_password($name,set_edit_value($name,$data),$extra);
    }
}

if ( ! function_exists('form_new_radio'))
{
    function form_new_radio($data='',$value='',$checked=FALSE,$extra=''){
        if(isset($_POST[$data])){
            $checked=(set_value($data)==$value)?true:false;
        }
        return form_radio($data,$value,$checked,$extra);
    }
}

if ( ! function_exists('form_edit_radio'))
{
    function form_edit_radio($data='',$value='',&$rs,$extra=''){
        $checked=(set_edit_value($data,$rs)==$value)?true:false;
        return form_radio($data,$value,$checked,$extra);
    }
}

if ( ! function_exists('form_new_checkbox'))
{
    function form_new_checkbox($data='',$value='',$checked=FALSE,$extra=''){
        if(isset($_POST[$data])){
            $checked=(set_value($data)==$value)?true:false;
        }
        return form_checkbox($data,$value,$checked,$extra);
    }
}

if ( ! function_exists('form_edit_checkbox'))
{
    function form_edit_checkbox($data='',$value='',&$rs,$extra=''){
        $checked=(set_edit_value($data,$rs)==$value)?true:false;
        return form_checkbox($data,$value,$checked,$extra);
    }
}

function convert_charset($str, $in_charset, $out_charset = 'UTF-8')
{
    if(function_exists('iconv'))
    {
        return @iconv($in_charset, $out_charset, $str);
    }
    if(function_exists('mb_convert_encoding'))
    {
        return @mb_convert_encoding($str, $out_charset, $in_charset);
    }
    return false;
}

function dirmake($dir, $perm = 0777)
{
    if(file_exists($dir)) return true;
    $t = strtr($dir,array('\\'=>'/','//'=>'/'));
    if(strrpos($t,'.')>strrpos($t,'/')) $t = dirname($t);
    $t = rtrim($t,'/');
    $a = array();
    while(($n = strrpos($t,'/')) && !is_dir($t))
    {
        $a[] = substr($t,$n+1);
        $t   = substr($t,0,$n);
    }
    while($d = array_pop($a))
    {
        $t .= '/'.$d;
        if(!@mkdir($t,$perm)) return false;
        if(!@chmod($t,$perm)) return false; //赋予全部权限
    }
    return true;
}