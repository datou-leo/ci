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
	<?=form_open('login/post')?>
	<div class="box login"> <b class="b1"></b><b class="b2"></b><b class="b3"></b>
		<div class="bdiv">
			<div class="btop">
				<h2>
					<?=lang('site_title')?>
				</h2>
				<em>多次输入错误，将被禁止登录一段时间，请注意</em> </div>
			<?=validation_errors('<div class="warning">','</div>')?>

           	<div class="bmain form">
				<p>管理帐号<br />
					<input type="text" name="account" class="text m" value="<?=set_value('account')?>" autocomplete="off">
				</p>
				<p>管理密码<br />
					<input type="password" name="password" class="text m">
				</p>
			</div>
			<div class="dialog-bottom">
				<div class="dbleft">
					<label>
						<input type="checkbox" name="remember" value="1" <?=isset($remember)?'checked':''?> />
						记住管理帐号</label>
				</div>
				<div class="dbright"> <span class="submit">
					<input type="submit" value="登录" />
					</span> </div>
				<div class="clear"></div>
			</div>
		</div>
		<b class="b3b"></b><b class="b2b"></b><b class="b1b"></b> </div>
	<?=form_close()?>
</div>
<!-- end wrap -->
</body>
</html>