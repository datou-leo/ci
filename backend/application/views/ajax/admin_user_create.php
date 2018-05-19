<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(900);
	$(".form input:first").focus();
}
function check_all(k_id){
	if($("#all_"+k_id).attr('checked')){
		<?php foreach($auth_power as $k=>$v){ ?>
		$("#auth_"+k_id+"_<?php echo $k; ?>").attr('checked',true);
		<?php } ?>
	}
}
function check_auth(k_id,oper){
	if(oper=="index"){
		if(!$("#auth_"+k_id+"_"+oper).attr('checked')){
			$("#auth_"+k_id+"_post").attr('checked',false);
			$("#auth_"+k_id+"_put").attr('checked',false);
			$("#auth_"+k_id+"_delete").attr('checked',false);
		}
	}else{
		if($("#auth_"+k_id+"_"+oper).attr('checked')){
			$("#auth_"+k_id+"_index").attr('checked',true);
			if(oper=="put"){
				$("#auth_"+k_id+"_post").attr('checked',true);
			}
		}else{
			if(oper=="put"){
				$("#auth_"+k_id+"_post").attr('checked',false);
			}
		}
	}
	if(!$("#auth_"+k_id+"_"+oper).attr('checked')){
		$("#all_"+k_id).attr('checked',false);
	}
}
</script>
<?=form_open("$title/post")?>

<div class="dialog-title">
	<h2>
		<?=admin_create_caption($caption)?>
	</h2>
	<span><a href="javascript:close_dialog();">关闭 [X]</a></span> </div>
<div class="dialog-note">
	<p>权限选择“超级管理员”，无需勾选后台栏目的指派权限，默认拥有以下所有栏目的权限</p>
</div>
<?=validation_errors('<div class="warning">','</div>')?>
<div class="form">
	<ul class="halfside">
		<li>帐号<br />
			<?=form_new_input('account','','class="text m"')?>
		</li>
		<li>权限<br />
			<?php foreach($auth_caption as $k=>$v){
			echo form_new_radio('role',$k,'','id="role_'.$k.'"');
			echo '<label for="role_'.$k.'">'.$v.'</label>&nbsp; &nbsp;';
			}?>
		</li>
		<i class="clear"></i>
		<li>密码<br />
			<?=form_new_password('password','','class="text m"')?>
		</li>
		<li>重复密码<br />
			<?=form_new_password('passconf','','class="text m"')?>
		</li>
	</ul>
</div>
<table class="datable" border="0" cellpadding="0" cellspacing="0" height="100%">
	<thead>
		<tr>
			<th>后台栏目</th>
			<th>指派权限</th>
			<th>后台栏目</th>
			<th>指派权限</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$id_tree2=array();
		$num=count($id_tree);
		if($num%2==1){
			$n=intval($num/2)+1;
		}else{
			$n=intval($num/2);
		}
		$a=array_keys($id_tree);
		for($i=$n;$i<count($a);$i++){
			$id_tree2[$a[$i]]=$id_tree[$a[$i]];
			unset($id_tree[$a[$i]]);
		}
		$a=array_keys($id_tree2);
		$i=-1;
		foreach($id_tree as $k_id=>$r){$i++;?>
		<tr>
			<td><?php echo strpos($r,'|-')?$r:'<strong>'.$r.'</strong>'?></td>
			<td><?php if($arr_types[$k_id]['is_havechild']==0){
			$str_use='';
			if($arr_types[$k_id]['is_use']==1){
				$str_use=' disabled="disabled"';
			}
			echo form_checkbox('all_'.$k_id,1,false,'id="all_'.$k_id.'" onclick="check_all('.$k_id.');"'.$str_use); ?>
				<label for="<?php echo 'all_'.$k_id; ?>"><strong>全部</strong></label>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<?php foreach($auth_power as $k=>$v){
			$idname='auth_'.$k_id.'_'.$k;
			if($arr_types[$k_id]['is_use']==1){
				$_POST[$idname]=1;
			}
			$str_prop=' onclick="check_auth('.$k_id.',\''.$k.'\');"'.$str_use;
			$label_prop='';
			if($k=='post'){
				$str_prop.=' style="display:none;"';
				$label_prop=' style="display:none;"';
			}
			echo form_checkbox($idname,1,!empty($_POST[$idname]),'id="'.$idname.'"'.$str_prop);
			echo '<label for="'.$idname.'"'.$label_prop.'>'.$v.'</label>';
			if($label_prop==''){
				echo '&nbsp; &nbsp;';
			}
			} } ?></td>
			<?php if(isset($a[$i])){?>
			<td><?php echo strpos($id_tree2[$a[$i]],'|-')?$id_tree2[$a[$i]]:'<strong>'.$id_tree2[$a[$i]].'</strong>'?></td>
			<td><?php if($arr_types[$a[$i]]['is_havechild']==0){
			$str_use='';
			if($arr_types[$a[$i]]['is_use']==1){
				$str_use=' disabled="disabled"';
			}
			echo form_checkbox('all_'.$a[$i],1,false,'id="all_'.$a[$i].'" onclick="check_all('.$a[$i].');"'.$str_use); ?>
				<label for="<?php echo 'all_'.$a[$i]; ?>"><strong>全部</strong></label>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<?php foreach($auth_power as $k=>$v){
			$idname='auth_'.$a[$i].'_'.$k;
			if($arr_types[$a[$i]]['is_use']==1){
				$_POST[$idname]=1;
			}
			$str_prop=' onclick="check_auth('.$a[$i].',\''.$k.'\');"'.$str_use;
			$label_prop='';
			if($k=='post'){
				$str_prop.=' style="display:none;"';
				$label_prop=' style="display:none;"';
			}
			echo form_checkbox($idname,1,!empty($_POST[$idname]),'id="'.$idname.'"'.$str_prop);
			echo '<label for="'.$idname.'"'.$label_prop.'>'.$v.'</label>';
			if($label_prop==''){
				echo '&nbsp; &nbsp;';
			}
			} } ?></td>
			<?php }else{ ?>
			<td></td>
			<td></td>
			<?php }?>
		</tr>
		<?php }?>
	</tbody>
</table>
<div class="dialog-bottom">
	<div class="dbleft"> <a href="javascript:close_dialog();" class="button"><span>&nbsp; 取消 &nbsp;</span></a> </div>
	<div class="dbright"> <span class="submit">
		<input type="submit" value="保存信息" />
		</span> </div>
	<div class="clear"></div>
</div>
<?=form_close()?>
