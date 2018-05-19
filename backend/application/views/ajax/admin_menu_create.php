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
<?php $type_id = $this->uri->segment(3,0); ?>
<?=form_open("$title/post/$parent_id")?>

<div class="dialog-title">
	<h2>添加
		<?=$caption?>
	</h2>
	<span><a href="javascript:close_dialog();">关闭 [X]</a></span> </div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
	<ul class="halfside">
		<li>标题<br />
			<?=form_new_input('title','','class="text"')?>
		</li>
		<li>类别<br />
			<?=form_dropdown('parent_id',$id_tree,set_value('parent_id',$parent_id))?>
		</li>
		<i class="clear"></i>
		<li>后台链接<br />
			<?=form_new_input('url','','class="text"')?>
		</li>
		<li>链接类型 / 显隐<br />
			<label>
				<?=form_new_radio('internal',1,true)?>
				站内</label>
			&nbsp;&nbsp;
			<label>
				<?=form_new_radio('internal',0,false)?>
				外链</label>
			&nbsp;&nbsp;
			<label>
				<?=form_new_checkbox('ajax',1,false)?>
				Ajax</label>
		</li>
		<i class="clear"></i>
	</ul>
	<i class="clear"></i> </div>
<div class="dialog-bottom">
	<div class="dbleft"> <a href="javascript:close_dialog();" class="button"><span>&nbsp; 取消 &nbsp;</span></a> </div>
	<div class="dbright"> <span class="submit">
		<input type="submit" value="保存信息" />
		</span> </div>
	<i class="clear"></i> </div>
<?=form_close()?>
