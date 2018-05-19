<?php require_once("index.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script language="JavaScript" type="text/javascript">
var nav_index=<?=$nav_index?>,sub_index=<?=$sub_index?>;
</script>
<?php $this->load->view("inc/header"); ?>
<script type="text/javascript" src="<?=base_url()?>js/area.js"></script>
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
								</a><em>
								<?=$total?>
								个符合&nbsp;&nbsp;<a href="<?=site_url("$title/create/$type_id")?>" rel="ajax"><img src="../css/i-add.gif" align="absmiddle" />添加</a></em></h2>
							<p>每页&nbsp;<a href="<?=site_url("$title/index/$type_id/$page/$order/10/$cond")?>">10个</a>&nbsp;&raquo;&nbsp;<a href="<?=site_url("$title/index/$type_id/$page/$order/25/$cond")?>">25个</a>&nbsp;&raquo;&nbsp;<a href="<?=site_url("$title/index/$type_id/$page/$order/50/$cond")?>">50个</a></p>
						</div>
						<div class="bmain">
							<table class="datable" border="0" cellpadding="0" cellspacing="0" height="100%">
								<thead>
									<tr>
										<th>帐号</th>
										<th>权限</th>
										<th>创建日期</th>
										<th>创建IP</th>
										<th>上次登录</th>
										<th>登录IP</th>
										<th>登录次数</th>
										<th>操作</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($data as $r){
		//系统开发人员帐号隐藏
		if($r->account=='administrator') continue;
		$r->id=strtr(base64_encode($r->account),'/','|');
		?>
									<tr rel="<?=$r->id?>" value="">
										<td><a href="<?=site_url("$title/edit/$type_id/{$r->id}/$page/$order/$each/$cond")?>" title="点击编辑" rel="ajax">
											<?=$r->account?>
											</a></td>
										<td><?=isset($auth_caption[$r->role])?$auth_caption[$r->role]:'(未知)'?></td>
										<td><?=date('Y-m-d',$r->timeline)?></td>
										<td><?=$r->create_ip?></td>
										<td><?=date('Y-m-d',$r->timelast)?></td>
										<td><?=$r->login_ip?></td>
										<td><?=$r->login_count?></td>
										<td><a href="<?=site_url("$title/edit/$type_id/{$r->id}/$page/$order/$each/$cond")?>" rel="ajax">编辑</a> | <a rel="delete" href="javascript:js_delete('<?=$r->id?>','<?=site_url("$title/delete/$r->id")?>');">删除</a></td>
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