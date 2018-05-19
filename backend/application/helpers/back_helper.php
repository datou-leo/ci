<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function admin_yes_no($value, $yes = 1)
{
	if($value == $yes)
	{
		return '<img src="'.base_url().'css/i-yes.gif" />';
	}
	return '<img src="'.base_url().'css/i-no.gif" />';
}

function admin_toggle($url, $value, $yes = 1)
{
	$uid = rand();
	return '<a id="'.$uid.'" href="javascript:js_toggle(\''.$uid.'\',\''.$url.'\');">'.admin_yes_no($value,$yes).'</a>';
}

function admin_photo($photo, $thumb = null, $title = '')
{
	if(empty($photo))
	{
		return '(无)';
	}
	if(empty($thumb))
	{
		$thumb = $photo;
	}
	return '<a href="'.$photo.'" rel="popup" title="'.htmlspecialchars($title).'"><img src="'.$thumb.'" width="50" height="50" /></a>';
}

function admin_moveup($url, $img = 'css/i-up.gif')
{
	return '<a href="'.$url.'"><img src="'.base_url().$img.'" /></a>';
}

function admin_movedown($url)
{
	return admin_moveup($url,"css/i-down.gif");
}

function admin_create_caption($s)
{
	return(substr($s,0,strlen('添加'))!='添加'?'添加'.$s:$s);
}

function admin_edit_caption($s){
	return(substr($s,0,strlen('编辑'))!='编辑'?'编辑'.$s:$s);
}

function admin_encode_segment($s)
{
	$CI = &get_instance();
	$CI->load->helper('js');
	$s = js_escape($s);
	return strtr(base64_encode($s),'/','|');
}

function admin_decode_segment($s)
{
	$CI = &get_instance();
	$CI->load->helper('js');
	$s = strtr($s,'|','/');
	$s = @base64_decode($s);
	return js_unescape($s);
}

function admin_set_search($uri, $to_site_url = true)
{
	if($to_site_url)
	{
		$uri = site_url($uri);
	}
	$CI = &get_instance();
    $q  = &$CI->load->vars();
	$kw = (isset($q['keyword'])?$q['keyword']:"");
	return <<<EOT
<script language="JavaScript" type="text/javascript">
$(function(){
	set_search_form('$uri','$kw');
});
</script>
EOT;
}

function admin_upload_pic_list($opt = array())
{
	static $def = array(
		'max_upload'=>1,'title'=>'Upload','tag'=>'userfile',
		'thumb'=>'100x100','record'=>null,
		'column'=>'pic','column_thumb'=>'thumb','max_column'=>6
		);
	//var_dump($opt+$def);
	extract($opt+$def,EXTR_SKIP);
	$CI = &get_instance();
	$CI->load->library('userdata');
	$ssid = $CI->userdata->get('session_id');
	$js = <<<EOT
dialog_upload("#$tag",$max_upload,"pic","$ssid","$thumb");
EOT;
	$ret = <<<EOT
<fieldset><legend>$title - <a href="http://get.adobe.com/cn/flashplayer/" target="_blank">需要使用Adobe Flash &raquo; 点击获取</a></legend><input type="file" name="Filedata" id="$tag" /><i class="i5"></i><ul id="{$tag}_state">
EOT;
	$end = '</ul></fieldset>';
	$q   = $CI->input->post($tag.'_list');
	$no_upload = ((!is_array($q) || count($q) < 1)?true:false);
	if(is_null($record) && $no_upload)
	{
		return array($js,$ret.$end);
	}
	$photo = $thumb = array();
	$from_post = true;
	if(is_object($record))
	{
		$record = get_object_vars($record);
	}
	if(isset($record['photo']) && $no_upload)
	{
		$from_post = false;
		$q = array($record['photo']);
		if(!empty($record['thumb']))
		{
			$q[] = $record['thumb'];
		}
	} else if(isset($record[$column.'1']) && $no_upload) {
		$from_post = false;
		$q = array();
		for($i = 1; $i <= $max_column; $i++)
		{
			if(!empty($record[$column.$i]))
			{
				$q[] = $record[$column.$i];
			}
			if(!empty($record[$column_thumb.$i]))
			{
				$q[] = $record[$column_thumb.$i];
			}
		}
	}
	if(is_array($q) && count($q) > 0)
	{
		foreach($q as $r)
		{
			if(strpos($r,'_thumb'))
			{
				$thumb[str_replace('_thumb','',$r)] = $r;
			} else {
				$photo[$r] = 1;
			}
		}
		//var_dump($photo);var_dump($thumb);
		foreach($photo as $pic => $v)
		{
			$tmb  = (isset($thumb[$pic])?$thumb[$pic]:$pic);
			$tmbx = admin_encode_segment($tmb);
            $url = site_url("uploadify/delete/".$tmbx);
			$ret .= <<<EOT
<li><img src="$tmb" /><em><a href="$pic" rel="popup">查看</a><a href="$url" rel="delete_img">删除</a></em></li>
EOT;
			if($from_post)
			{
				$ret .= '<input type="hidden" name="'.$tag.'_list[]" value="'.$tmb.'" style="display:none;" /><input type="hidden" name="'.$tag.'_list[]" value="'.$pic.'" style="display:none;" />';
			}
		}
	}
	//echo htmlspecialchars($ret.$end);
	return array($js,$ret.$end);
}

function admin_upload_doc_list($opt = array())
{
	static $def = array(
		'max_upload'=>1,'title'=>'Upload','tag'=>'userdoc','record'=>null,
		);
	//var_dump($opt+$def);
	extract($opt+$def,EXTR_SKIP);
	$CI = &get_instance();
	$CI->load->library('userdata');
	$ssid = $CI->userdata->get('session_id');
	$js   = <<<EOT
dialog_upload("#$tag",$max_upload,"doc","$ssid");
EOT;
	$ret  = <<<EOT
<fieldset><legend>$title - <a href="http://get.adobe.com/cn/flashplayer/" target="_blank">需要使用Adobe Flash &raquo; 点击获取</a></legend><input type="file" name="Filedata" id="$tag" /><i class="i5"></i><ul id="{$tag}_state">
EOT;
	$end = '</ul></fieldset>';
	$q   = $CI->input->post($tag.'_list');
	if(is_null($record) && (!is_array($q) || count($q) < 1))
	{
		return array($js,$ret.$end);
	}
	if(is_object($record))
	{
		$record = get_object_vars($record);
	}
	$from_post = true;
	if(isset($record['file']))
	{
		$from_post = false;
		$q = array($record['file']);
	}
	if(is_array($q) && count($q) > 0)
	{
		foreach($q as $file)
		{
			$filex = admin_encode_segment($file);
			$ret  .= <<<EOT
<li><img src="css/doc.gif" /><em><a href="$file" target="_blank">下载</a><a href="index.php?uploadify/delete/$filex" rel="delete_img">删除</a></em></li>
EOT;
			if($from_post)
			{
				$ret .= '<input type="hidden" name="'.$tag.'_list[]" value="'.$file.'" style="display:none;" />';
			}
		}
	}
	return array($js,$ret.$end);
}

function admin_upload_flv_list($opt = array())
{
	static $def = array(
		'max_upload'=>1,'title'=>'Upload','tag'=>'userflv','record'=>null,
		);
	extract($opt+$def,EXTR_SKIP);
	$CI = &get_instance();
	$CI->load->library('userdata');
	$ssid = $CI->userdata->get('session_id');
	$js   = <<<EOT
dialog_upload("#$tag",$max_upload,"flv","$ssid");
EOT;
	$ret  = <<<EOT
<fieldset><legend>$title - <a href="http://get.adobe.com/cn/flashplayer/" target="_blank">需要使用Adobe Flash &raquo; 点击获取</a></legend><input type="file" name="Filedata" id="$tag" /><i class="i5"></i><ul id="{$tag}_state">
EOT;
	$end  = '</ul></fieldset>';
	$q    = $CI->input->post($tag.'_list');
	if(is_null($record) && (!is_array($q) || count($q) < 1))
	{
		return array($js,$ret.$end);
	}
	if(is_object($record))
	{
		$record=  get_object_vars($record);
	}
	$from_post = true;
	if(isset($record['video']))
	{
		$from_post = false;
		$q = array($record['video']);
	}
	if(is_array($q) && count($q) > 0)
	{
		foreach($q as $file)
		{
			$filex = admin_encode_segment($file);
			$ret  .= <<<EOT
<li><img src="css/flv.gif" /><em><a href="flv.php?$filex" target="_blank">播放</a><a href="index.php?uploadify/delete/$filex" rel="delete_img">删除</a></em></li>
EOT;
			if($from_post)
			{
				$ret .= '<input type="hidden" name="'.$tag.'_list[]" value="'.$file.'" style="display:none;" />';
			}
		}
	}
	return array($js,$ret.$end);
}

function admin_each_page($url, $each = 25)
{
	$a   = array();
	$url = site_url($url);
	foreach(array(10,25,50) as $v)
	{
		$a[] = '<a href="'.str_replace('{each}',$v,$url).'">'
			.($v!=$each?$v:'<strong>'.$v.'</strong>').'个</a>';
	}
	return '每页'.join('&raquo;&nbsp;',$a);
}

function admin_type_filter($url, $value_array, $selected = -1, $all_type = "全部分类")
{
	$CI = &get_instance();
	$CI->load->helper('form');
	if(!empty($all_type))
	{
		$value_array[-1] = $all_type;
		ksort($value_array);
	}
	$url = site_url($url);
	$a   = array();
	foreach($value_array as $k => $v)
	{
		if(strpos($v,'|-') !== false)
		{
			continue;
		}
		if($k == $selected)
		{
			$v = '<strong>'.$v.'</strong>';
		}
		$a[] = '<a href="'.str_replace('{type_id}',$k,$url).'">'.$v.'</a>';
	}
	return join('&nbsp;&raquo;&nbsp;',$a);
}