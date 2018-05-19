<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php list($upload_js,$upload_htm)=admin_upload_pic_list(array(
	'title'=>'相关图片',
	'thumb'=>'210x155',
	'record'=>isset($rs)?$rs:null
))?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(860);
	$(".form input:first").focus();
	dialog_editor("#content");
	<?=$upload_js?>
}
</script>
<?=form_open_multipart("$title/post/$type_id")?>

<div class="dialog-title">
	<h2>添加信息</h2>
	<span><a href="javascript:close_dialog();">关闭 [X]</a></span> </div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
	<ul class="halfside">
		<li>标题<br />
			<?=form_new_input('title','','class="text m"')?>
		</li>
		<li>分类 / 状态<br />
			<?=form_dropdown('type_id',$type_tree,$type_id)?>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<label>
				<?=form_new_checkbox('recommend','1',false)?>
				推荐</label>
			&nbsp;&nbsp;
			<label>
				<?=form_new_checkbox('show','1',true)?>
				可见</label>
		</li>
		<i class="clear"></i>
		<li>作者<br />
			<?=form_new_input('author','datou','class="text m"')?>
		</li>
		<li>发布时间<br />
			<?=form_new_input('timeline',date('Y-m-d H:i:s'),'class="text mydatetime" readonly')?>
		</li>
		<i class="clear"></i>
		<li>摘自<br />
			<?=form_new_input('from','','class="text m"')?>
		</li>
		<li>链接<br />
			<?=form_new_input('url','','class="text m"')?>
		</li>
	</ul>
	<?=$upload_htm?>
	<p>内容<br />
		<?=form_textarea('content',set_value('content'),'id="content"')?>
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
