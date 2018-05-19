<?php require_once("index.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script language="JavaScript" type="text/javascript">
var nav_index=<?=$nav_index?>,sub_index=<?=$sub_index?>;
</script>
<?php $this->load->view("inc/header"); ?>
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
				<?=form_open("$title/access")?>
				<div class="box"> <b class="b1"></b><b class="b2"></b><b class="b3"></b>
					<div class="bdiv">
						<div class="btop">
							<h2><a href="<?=site_url("$title/index")?>">
								<?=$caption?>
								</a><em>
								<?=$total?>
								个符合&nbsp;&nbsp;<a href="<?=site_url("$title/create")?>" rel="ajax"><img src="../css/i-add.gif" align="absmiddle" />添加</a></em></h2>
							<p>该功能仅供开发人员使用，请勿更改以免影响网站后台使用</p>
						</div>
						<div class="bmain">
							<table class="datable" border="0" cellpadding="0" cellspacing="0" height="100%">
								<thead>
									<tr>
										<th>标题</th>
										<th>链接</th>
										<th>链接目标</th>
										<th>Ajax</th>
										<th>操作</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($data as $r){?>
									<tr rel="<?=$r->id?>" value="">
										<td><?php
										if($r->depth>0){
											$r->title=str_repeat('&nbsp;&nbsp;&nbsp;',$r->depth-1).'&nbsp;&nbsp;&nbsp;|-&nbsp;'.$r->title;
										}else{
											$r->title="<b>$r->title</b>";
										}
										echo $r->title;
										?></td>
										<td><?=$r->url?></td>
										<td><?php if($r->internal){ echo '_self'; } else { echo '_blank'; } ?></td>
										<td><?=admin_yes_no($r->ajax)?></td>
										<td><?php if($r->is_fixed!=1){ ?>
											<a href="<?=site_url("$title/create/{$r->id}")?>" rel="ajax">添加子类</a> | <a href="<?=site_url("$title/edit/{$r->id}")?>" rel="ajax">编辑</a> | <a rel="delete" href="<?=site_url("$title/delete/{$r->id}")?>">删除</a>
											<?php } ?></td>
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
					</ul>
					<div class="i10"></div>
				</div>
				<?=form_close()?>
			</div>
			<!-- end main --> 
		</div>
		<!-- end content --> 
	</div>
	<!-- end container --> 
</div>
<!-- end wrap -->
</body>
</html>