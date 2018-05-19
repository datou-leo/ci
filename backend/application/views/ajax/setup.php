<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(680);
	$(".form input:first").focus();
}
</script>
<?=form_open("$title/post")?>

<div class="dialog-title">
	<h2>
		<?=admin_edit_caption($caption)?>
	</h2>
	<span><a href="javascript:close_dialog();">关闭 [X]</a></span> </div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
	<p>网站标题<br />
		<?=form_edit_input('title',$rs,'class="text x"')?>
	</p>
	<p>网站关键字<br />
		<?=form_textarea('keywords',set_edit_value('keywords',$rs),'style="height:60px;"')?>
	</p>
	<p>网站描述<br />
		<?=form_textarea('description',set_edit_value('description',$rs),'style="height:60px;"')?>
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
