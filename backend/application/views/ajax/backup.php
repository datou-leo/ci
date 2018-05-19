<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	$("#backup_form").attr("target","_self").submit(function(){
		close_dialog();
	});
    $("input[type=radio][name=way][value=part]").click(function(){
        if($(this).attr("checked")==true){
            $("input[type=radio][name=place][value=server]").attr('checked',true);
            $("input[type=radio][name=place][value=download]").attr('disabled',true);
        }
    });

    $("input[type=radio][name=way][value=full]").click(function(){
        if($(this).attr("checked")==true){
            if($("input[type=radio][name=place][value=download]").attr('disabled')==true){
                $("input[type=radio][name=place][value=download]").attr('disabled',false);
            }
        }
    });

}
</script>
<?=form_open("$title/post",'id="backup_form"')?>

<div class="dialog-title">
	<h2>
		<?=$caption?>
	</h2>
	<span><a href="javascript:close_dialog();">关闭 [X]</a></span> </div>
<div class="dialog-note">
	<p>服务器数据备份目录是 /backend/backup</p>
	<p>对于数据量很大的数据库，建议使用分卷备份</p>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
	<ul class="halfside">
		<li>数据库名称<br />
			<?=$database_name?>
		</li>
		<li>备份方式<br />
			<label>
				<?=form_new_radio('way','full',true)?>
				完整备份</label>
			&nbsp;
			<label>
				<?=form_new_radio('way','part',false)?>
				分卷备份</label>
		</li>
		<li>保存位置<br />
			<label>
				<?=form_new_radio('place','server',true)?>
				到服务器</label>
			&nbsp;
			<label>
				<?=form_new_radio('place','download',false)?>
				下载备份文件</label>
		</li>
		<li>其他设置<br />
			<label>
				<?=form_new_checkbox('compress','1',false)?>
				压缩备份文件</label>
		</li>
	</ul>
</div>
<div class="dialog-bottom">
	<div class="dbleft"> <a href="javascript:close_dialog();" class="button"><span>&nbsp; 取消 &nbsp;</span></a> </div>
	<div class="dbright"> <span class="submit">
		<input type="submit" value="开始备份" />
		</span> </div>
	<div class="clear"></div>
</div>
<?=form_close()?>
