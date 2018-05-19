<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	$(".form input:first").focus();
}
</script>


    <?=form_open('password/post')?>
	<div class="dialog-title">
		<h2>修改密码</h2>
		<span><a href="javascript:close_dialog();">关闭 [X]</a></span> </div>
	<div class="dialog-note">
		<p>新密码长度最少6个字符</p>
	</div>
	<?=validation_errors('<div class="warning">','</div>')?>
	<div class="form">
		<p>当前密码<br />
			<input type="password" name="oldpass" class="text x" />
		</p>
		<p>新密码<br />
			<input type="password" name="password" class="text x" />
		</p>
		<p>确认密码<br />
			<input type="password" name="passconf" class="text x" />
		</p>
	</div>
	<div class="dialog-bottom">
		<div class="dbleft"> <a href="javascript:close_dialog();" class="button"><span>&nbsp; 取消 &nbsp;</span></a> </div>
		<div class="dbright"> <span class="submit">
			<input type="submit" value="保存信息" />
			</span> </div>
		<div class="clear"></div>
	</div>
    <?=form_close()?>
