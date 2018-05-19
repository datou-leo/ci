<?php require_once("index.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script language="JavaScript" type="text/javascript">
var nav_index=<?=$nav_index?>,sub_index=<?=$sub_index?>;
</script>
<?php $this->load->view("inc/header"); ?>
<?=admin_set_search("$title/search/$type_id")?>
<script language="JavaScript" type="text/javascript">
(function($){
$(function(){
	$("input[rel=index]").focus(function(){
		$(this).select();
	}).change(function(){
		var id=parseInt($(this).attr("id").substring(5)),val=$(this).attr("value");
		$(this).parent().find("img,font").remove();
		$(this).parent().append("<img src='<?=base_url()?>css/loading.gif' style='width:16px;height:16px;' align='absmiddle' />");
		$.getJSON("news.php?jsChangeIndex/"+id+"/"+val,function(json){
			var o=$("#index"+json.id).parent();
			o.find("font,img").remove();
			if(json.result==0){
				o.append("<font color='green'><b>√</b></font>")
			}else{
				o.append("<font color='red'><b>Ｘ</b></font>")
			}
			//window.location.href='news.php';
			window.location.history.back();
		});
		return;
	});
});
})(jQuery);
</script>
</head>
<body>
<div id="wrap">
	<?=$this->load->view("inc/navi")?>
	<div id="container">
		<div id="side">
			<?=$this->load->view("inc/side")?>
		</div>
		<!-- end side -->
		<div id="wrapin">
			<div id="main">
				<?=form_open("$title/access/$type_id/$page/$order/$each/$cond")?>
				<div class="box"> <b class="b1"></b><b class="b2"></b><b class="b3"></b>
					<div class="bdiv">
						<div class="btop">
							<div class="page right">
								<?=$page_link?>
							</div>
							<h2><a href="<?=site_url("$title/index/$type_id")?>">
								<?=$caption?>
								</a><em><a href="<?=site_url("$title/create/$type_id")?>" rel="ajax"><img src="<?=base_url()?>css/i-add.gif" align="absmiddle" />添加</a></em></h2>
							<p>
								<?=$total?>
								个符合&nbsp;|&nbsp;&nbsp;
								<?=admin_each_page("$title/index/$type_id/$page/$order/{each}/$cond",$each)?>
								&nbsp;|&nbsp;&nbsp;
								<?=admin_type_filter("$title/index/{type_id}/1/$order/$each/$cond",$type_tree,$type_id)?>
							</p>
						</div>
						<div class="bmain">
							<table class="datable" border="0" cellpadding="0" cellspacing="0" height="100%">
								<thead>
									<tr>
										<th>上/下移</th>
										<th>图</th>
										<th>排序</th>
										<th>分类</th>
										<th>标题</th>
										<th>推荐</th>
										<th>显隐</th>
										<th>审核</th>
										<th>更新时间</th>
										<th>操作</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($data as $r){?>
									<tr rel="<?=$r->id?>" value="">
										<td><?=admin_moveup(site_url("$title/moveup/$type_id/{$r->id}/$page/$order/$each/$cond")).admin_movedown(site_url("$title/movedown/$type_id/{$r->id}/$page/$order/$each/$cond"))?></td>
										<td><?=admin_photo($r->photo,$r->thumb,$r->title)?></td>
										<td><input rel="index" id="index<?=$r->id;?>" type="text" value="<?=$r->sort_id;?>" onmouseover="this.focus()" onfocus="this.select()" style="width:35px;" title="即时更改,越大越靠前" class="text tip" /></td>
										<td><?=isset($type_tree[$r->type_id])?$type_tree[$r->type_id]:$r->type_id?></td>
										<td><a href="<?=site_url("$title/edit/$type_id/{$r->id}/$page/$order/$each/$cond")?>" title="点击编辑" rel="ajax">
											<?=strcut($r->title,22)?>
											</a></td>
										<td><?=admin_toggle(site_url("$title/toggle/$r->id/recommend"),$r->recommend)?></td>
										<td><?=admin_toggle(site_url("$title/toggle/$r->id/show"),$r->show)?></td>
										<td><?php echo ($r->auditing==1 ? '<img src="css/i-yes.gif" border="0">' : '<img src="'.base_url().'css/i-no.gif" border="0">')?></td>
										<td><?=date('Y-m-d',$r->timeline)?></td>
										<td><!-- <a href="<?=site_url("$title/copy/$type_id/{$r->id}/$page/$order/$each/$cond")?>">复制</a> |  --><a href="<?=site_url("$title/edit/$type_id/{$r->id}/$page/$order/$each/$cond")?>" rel="ajax">编辑</a> | <a rel="delete" href="javascript:js_delete(<?=$r->id?>,'<?=site_url("$title/delete/$r->id")?>');">删除</a></td>
									</tr>
									<?php }?>
								</tbody>
							</table>
						</div>
					</div>
					<b class="b3b"></b><b class="b2b"></b><b class="b1b"></b> </div>
				<div class="table-option">
					<ul>
						<li><a class="button" rel="check-all"><span>全选</span></a></li>
						<li><a class="button" rel="check-off"><span>不选</span></a></li>
						<li><a class="button" rel="delete-all"><span>删除</span></a></li>
						<li><a class="button" rel="auditing-all"><span>审核</span></a></li>
						<li><a class="button" rel="cancel_auditing-all"><span>取消审核</span></a></li>
						<li><a href="<?=site_url("$title/searchnews/$type_id")?>" class="button" rel="ajax"><span>搜索</span></a></li>
					</ul>
					<div class="page right">
						<?=$page_link?>
					</div>
					<div class="i10"></div>
				</div>
				<?=form_close()?>
			</div>
			<!-- end main -->
		</div>
		<!-- end warpin -->
	</div>
	<!-- end container -->
</div>
<!-- end wrap -->
</body>
</html>