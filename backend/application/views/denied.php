<?php require_once("index.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script language="JavaScript" type="text/javascript">
if(window.top.dialog_obj){
	window.top.location.href='<?=site_url('denied/index')?>';
}
</script>
<?php $this->load->view("inc/header"); ?>
</head>
<body class="nobg">
<div id="wrap">
	<div class="box login"> <b class="b1"></b><b class="b2"></b><b class="b3"></b>
		<div class="bdiv">
			<div class="btop">
				<h2>
                    <?php $this->lang->load('common'); ?>
					<?=lang('site_title')?>
					管理系统</h2>
			</div>
			<?=validation_errors('<div class="warning">','</div>')?>
			<div class="bmain form">
				<p></p>
				<h3>您的帐号权限不够，无法继续操作</h3>
				<p></p>
				<p></p>
				<p>如需技术支持，请联络 <a href="#" target="_blank">datou(qq:2323178881)</a> 获得详细信息</p>
			</div>
			<div class="dialog-bottom">
				<div class="dbleft"> <a href="<?=site_url('login/delete')?>" class="button"><span>退出登录</span></a> </div>
				<div class="dbright"> <a href="<?=site_url('welcome/index')?>" class="button"><span>管理首页</span></a> </div>
				<div class="clear"></div>
			</div>
		</div>
		<b class="b3b"></b><b class="b2b"></b><b class="b1b"></b> </div>
</div>
<!-- end wrap -->
</body>
</html>