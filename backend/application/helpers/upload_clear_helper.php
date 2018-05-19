<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function upload_clear($urls)
{
	if(is_object($urls))
	{
		$urls = get_object_vars($urls);
	} else if(!is_array($urls)) {
		$urls = array($urls);
	}
	$upurl = rtrim(config_item('upload.url'),'/\\').'/';
	$updir = rtrim(config_item('upload.dir'),'/\\').'/';
	foreach($urls as $url)
	{
		if(empty($url) || is_int($url)) continue;
		if(!strncasecmp($url,$upurl,strlen($upurl)))
		{
			$url = $updir.substr($url,strlen($upurl));
		}
		//echo $url.'<br />';
		if(is_file($url)) @unlink($url);
	}
	//exit;
}
