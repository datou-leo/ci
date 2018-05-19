<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
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
				<div class="right">建议用鼠标右键保存文件&nbsp;</div>
				<h2>备份完成</h2>
			</div>
			<div class="bmain form">
				<p>点击下载分卷文件</p>
				<?=join('',$filelist)?>
			</div>
			<div class="dialog-bottom">
				<div class="dbright"> <a href="javascript:close_dialog();" class="button"><span>&nbsp; &nbsp;完成&nbsp; &nbsp;</span></a> </div>
				<div class="dbleft"> <a href="javascript:close_dialog();" class="button"><span>&nbsp; 关闭 &nbsp;</span></a> </div>
				<div class="clear"></div>
			</div>
		</div>
		<b class="b3b"></b><b class="b2b"></b><b class="b1b"></b> </div>
</div>
<!-- end wrap -->
</body>
</html>