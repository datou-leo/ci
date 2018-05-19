<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	$(".form input:first").focus();
	dialog_size(450);
}
</script>
<?=form_open_multipart("$title/put/$id")?>

<div class="dialog-title">
	<h2>
		<?=admin_edit_caption($caption)?>
	</h2>
	<span><a href="javascript:close_dialog();">关闭 [X]</a></span> </div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
	<p>分类名称<br />
		<?=form_edit_input('title',$rs,'class="text m"')?>
	</p>
	<ul class="halfside">
		<li>类别<br />
			<?=form_hidden('type_id',$rs->type_id)?>
			<?=form_dropdown('parent_id',$id_tree,set_edit_value('parent_id',$rs))?>
		</li>
		<li>位置<br />
			<?php $arr_location=array(1=>'子类',2=>'类别之前',3=>'类别之后'); ?>
			<?=form_dropdown('move_des',$arr_location,1)?>
		</li>
		<li></li>
	</ul>
</div>
<div class="dialog-bottom">
	<div class="dbleft"> <a href="javascript:close_dialog();" class="button"><span>&nbsp; 取消 &nbsp;</span></a> </div>
	<div class="dbright"> <span class="submit">
		<input type="submit" value="保存信息" />
		</span> </div>
	<div class="clear"></div>
</div>
<?=form_close()?>
