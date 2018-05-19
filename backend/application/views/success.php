<?php require_once("index.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $this->load->view("inc/header"); ?>
<script language="JavaScript" type="text/javascript">
$(function(){
	$(".form input:text:first").focus();
});
</script>
</head>
<body class="nobg">
<div id="wrap">
	<div class="box login"> <b class="b1"></b><b class="b2"></b><b class="b3"></b>
		<div class="bdiv">
			<div class="btop">
				<div class="right">
					<?=lang('site_title')?>
					&nbsp;</div>
				<h2>操作完成</h2>
			</div>
			<div class="bmain form">
				<p>&nbsp;</p>
				<h3>
					<?=$text?>
				</h3>
			</div>
			<div class="dialog-bottom">
				<?php if(empty($url)){?>
				<div class="dbright"> <a href="javascript:history.go(-1);" class="button"><span>&nbsp; 上一页 &nbsp;</span> </div>
				<?php }else{?>
				<div class="dbleft"> <a href="javascript:history.go(-1);" class="button"><span>&nbsp; 上一页 &nbsp;</span></a> </div>
				<div class="dbright">
					<?php if(!is_array($url)){?>
					<a href="<?=$url?>" class="button"><span>&nbsp; 下一步 &nbsp;</span></a>
					<?php }else foreach($url as $k=>$v){
				if(!strncasecmp($v,'ajax:',5)){
					$v=substr($v,5);
					$x='rel="ajax"';
				}else{
					$x='';
				}
				?>
					<a href="<?=$v?>" <?=$x?> class="button"><span>&nbsp;
					<?=$k?>
					&nbsp;</span></a>
					<?php }?>
				</div>
				<?php }?>
				<div class="clear"></div>
			</div>
		</div>
		<b class="b3b"></b><b class="b2b"></b><b class="b1b"></b> </div>
</div>
<!-- end wrap -->
</body>
</html>