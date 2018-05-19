<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<script language="JavaScript" type="text/javascript">
function page_load(){
	dialog_size(500);
}
</script>
<div class="dialog-title">
	<h2>操作成功</h2>
	<span><a href="javascript:close_dialog();">关闭 [X]</a></span> </div>
<div class="form nobg">
	<p>&nbsp;</p>
	<h3>
		<?=$text?>
	</h3>
</div>
<div class="dialog-bottom">
	<?php if(empty($url)){?>
	<div class="dbright"> <a href="javascript:close_dialog();" class="button"><span>&nbsp; &nbsp;完成&nbsp; &nbsp;</span></a> </div>
	<?php }else{?>
	<div class="dbleft"> <a href="javascript:close_dialog();" class="button"><span>&nbsp; 关闭 &nbsp;</span></a> </div>
	<div class="dbright">
		<?php if(!is_array($url)){?>
		<a href="<?=$url?>" class="button"><span>&nbsp; 下一步 &nbsp;</span></a>
		<?php }else foreach($url as $k=>$v){
			if(!strncasecmp($v,'ajax:',5)){
				$v=substr($v,5);
				$x='rel="ajax"';
			}else{
				$x='';
			}
			?>
		<a href="<?=$v?>" <?=$x?> class="button"><span>&nbsp;
		<?=$k?>
		&nbsp;</span></a>
		<?php }?>
	</div>
	<?php }?>
	<div class="clear"></div>
</div>
