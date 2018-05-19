<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php if($id == 1){ ?>
<?php list($upload_js,$upload_htm)=admin_upload_pic_list(array(
	'title'=>'相关图片(314px*482px)',
  'thumb'=>'',
	'record'=>isset($rs)?$rs:null
	))?>
<?php } else { ?>
<?php list($upload_js,$upload_htm)=admin_upload_pic_list(array(
  'title'=>'相关图片(826px*504px)',
  'thumb'=>'',
  'record'=>isset($rs)?$rs:null
  ))?>
<?php } ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(800);
	$(".form input:first").focus();
	dialog_editor("#content");
	<?=$upload_js?>
}
</script>
<?=form_open_multipart("$title/put/$id")?>

<div class="dialog-title">
	<h2>编辑"
		<?=$caption?>
		"的内容</h2>
	<span><a href="javascript:close_dialog();">关闭 [X]</a></span> </div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
	<p>
		<?=form_textarea('content',set_edit_value('content',$rs),'id="content"')?>
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
