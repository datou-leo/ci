<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
<?php list($upload_js,$upload_htm)=admin_upload_pic_list(array(
	'title'=>'导航图片(86px*166px)',
  	'thumb'=>'',
	'record'=>isset($rs)?$rs:null
	))?>


function page_load(){
	$(".form input:first").focus();
	<?=$upload_js?>
}
</script>
<?=form_open("$title/put/$id")?>

<div class="dialog-title">
	<h2>编辑
		<?=$caption?>
	</h2>
	<span><a href="javascript:close_dialog();">关闭 [X]</a></span> </div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
	<ul class="halfside">
		<li>标题<br />
			<?=form_edit_input('title',$rs,'class="text"')?>
		</li>
		<li>类别<br />
			<?=form_dropdown('parent_id',$id_tree,set_edit_value('parent_id',$rs))?>
		</li>
		<i class="clear"></i>
		<li>后台链接<br />
			<?=form_edit_input('url',$rs,'class="text"')?>
		</li>
		<li>链接类型 / 显隐<br />
			<label>
				<?=form_edit_radio('internal',1,$rs)?>
				站内</label>
			&nbsp;&nbsp;
			<label>
				<?=form_edit_radio('internal',0,$rs)?>
				外链</label>
			&nbsp;&nbsp;
			<label>
				<?=form_edit_checkbox('ajax',1,$rs)?>
				Ajax</label>
		</li>
		<i class="clear"></i>
		<li>位置<br />
			<?php $arr_location=array(1=>'子类',2=>'类别之前',3=>'类别之后'); ?>
			<?=form_dropdown('move_des',$arr_location,1)?>
		</li>
	</ul>
	<i class="clear"></i> </div>
<div class="dialog-bottom">
	<div class="dbleft"> <a href="javascript:close_dialog();" class="button"><span>&nbsp; 取消 &nbsp;</span></a> </div>
	<div class="dbright"> <span class="submit">
		<input type="submit" value="保存信息" />
		</span> </div>
	<i class="clear"></i> </div>
<?=form_close()?>
