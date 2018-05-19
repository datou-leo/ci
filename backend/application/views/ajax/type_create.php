<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	$(".form input:first").focus();
	dialog_size(450);
}
</script>
<?=form_open_multipart("$title/post/$parent_id")?>

<div class="dialog-title">
	<h2>
		<?=admin_create_caption($caption)?>
	</h2>
	<span><a href="javascript:close_dialog();">关闭 [X]</a></span> </div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
	<p>分类名称<br />
		<?=form_new_input('title','','class="text m"')?>
	</p>
	<p>类别<br />
		<?=form_dropdown('parent_id',$id_tree,$parent_id)?>
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
