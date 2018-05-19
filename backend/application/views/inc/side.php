<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="menu">
	<ul>
		<?php
	$a=array_keys($menu[0]);
	$k=$a[$nav_index-1];
	if(isset($menu[0][$k])){
		$a=array_keys($menu[$k]);
		$k=$a[$sub_index-1];
		$r=$menu_data[$k];
		$v=$r[1];
		if($r[4]&&trim($v)!='#')$v=site_url($v);
		$t=($r[6]?'rel="ajax"':'');
		echo '<a href="'.$v.'" target="'.$r[5].'" '.$t.' class="active">'.$r[0].'</a>';
		if(isset($menu[$k])){
			foreach($menu[$k] as $r){
				$v=$r[1];
				if($r[4]&&trim($v)!='#')$v=site_url($v);
				$t=($r[6]?'rel="ajax"':'');
				echo '<li><a href="'.$v.'" target="'.$r[5].'" '.$t.'>'.$r[0].'</a></li>';
			}
		}
	}
	unset($a,$v,$k,$t);
?>
		<!--<span>其它语言网站链接</span>
	<li><a href="<?=site_url('[/en]welcome')?>" target="_blank">浏览英文站前台</a></li>
	<li><a href="<?=site_url('[/en/backend]welcome')?>" target="_blank">浏览英文站后台</a></li>--> 
		<span>我的帐号</span>
		<li><a href="<?=site_url('password')?>" rel="ajax">
			<?=$admin_account?>
			</a></li>
		<li><a href="../" target="_blank">浏览前台</a></li>
		<li><a href="<?=site_url('login/delete')?>">退出</a></li>
		<a href="<?=site_url('shortcut')?>" rel="ajax">我的链接 [+]</a>
		<?php
	if(isset($my_shortcut)){
		foreach($my_shortcut as $k=>$r){
			if($r['internal']&&trim($r['url'])!='#')$r['url']=site_url($r['url']);
			$t=($r['ajax']?'rel="ajax"':'');
			echo '<li><a href="'.$r['url'].'" target="'.$r['target'].'" '.$t.'>'.$r['title'].'</a></li>';
		}
		unset($my_shortcut);
	}else{
		echo '<li><a href="'.site_url('shortcut').'" rel="ajax">添加</a></li>';
	}
?>
	</ul>
</div>
<!-- end menu -->
<div id="copyright">
	<p>技术支持: datou
	<p> 
</div>
<!-- end copyright -->