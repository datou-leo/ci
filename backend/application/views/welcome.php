<?php require_once 'index.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $this->load->view("inc/header"); ?>
<script language="JavaScript" type="text/javascript">
$(function(){
});
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
				<div class="box left"> <b class="b1"></b><b class="b2"></b><b class="b3"></b>
					<div class="bdiv">
						<div class="btop">
							<h2>平台<em>Platform</em></h2>
							<em>网站运行环境摘要</em> </div>
						<?php if(isset($default_databkup_notice)){?>
						<?php if(1==$default_databkup_notice){?>
						<div class="warning">您已经一周没有备份过数据文件了，建议现在<a href="<?=site_url('backup')?>" rel="ajax">备份</a></div>
						<?php }?>
						<?php if(2==$default_databkup_notice){?>
						<div class="warning">您还没有创建数据备份，建议现在<a href="<?=site_url('backup')?>" rel="ajax">备份</a></div>
						<?php }?>
						<?php }?>
						<?php if(isset($databkup_timestamp)){?>
						<div class="notice">上次<a href="<?=site_url('backup')?>" rel="ajax">数据备份</a>时间：
							<?=date('Y-m-d H:i:s',$databkup_timestamp)?>
						</div>
						<?php }?>
						<div class="bmain box-wrap">
							<p>网站名称：<a href="../" target="_blank">
                                <?php $this->lang->load('common'); ?>
								<?=lang("site_title")?>
								</a></p>
							<p>平台版本：V
								<?=CI_VERSION?>
							</p>
							<p>引擎类型：PHP v
								<?=phpversion()?>
							</p>
							<p>数据库：
								<?=ucfirst($this->db->platform()).' v'.$this->db->version()?>
							</p>
							<?php if(!isset($default_databkup_notice)&&!isset($databkup_timestamp))echo '<p>&nbsp;</p>'?>
							<p class="more"><a href="<?=site_url('setup')?>" rel="ajax">开始设置&raquo;</a></p>
						</div>
					</div>
					<b class="b3b"></b><b class="b2b"></b><b class="b1b"></b> </div>
				<div class="box right"> <b class="b1"></b><b class="b2"></b><b class="b3"></b>
					<div class="bdiv">
						<div class="btop">
							<h2>管理员<em>Administrator</em></h2>
							<em>您当前登录帐号的信息</em> </div>
						<?php if(isset($default_password_notice)){?>
						<div class="warning">您的密码为系统初始密码，请尽快<a href="<?=site_url('password')?>" rel="ajax">修改</a></div>
						<?php }?>
						<div class="bmain box-wrap">
							<p>当前帐号：
								<?=$admin->account?>
							</p>
							<p>帐号权限：
								<?=$admin->role?>
							</p>
							<p>登录IP：
								<?=$admin->login_ip?>
							</p>
							<p>登录次数：
								<?=$admin->login_count?>
							</p>
							<?php if(!isset($default_password_notice))echo '<p>&nbsp;</p>'?>
							<p class="more"><a href="<?=site_url('password')?>" rel="ajax">修改密码&raquo;</a> 
						</div>
					</div>
					<b class="b3b"></b><b class="b2b"></b><b class="b1b"></b> </div>
				<div class="clear"></div>
				<div class="box left"> <b class="b1"></b><b class="b2"></b><b class="b3"></b>
					<div class="bdiv">
						<div class="btop">
							<h2>网站<em>Server</em></h2>
							<em>网站信息摘要</em> </div>
						<div class="bmain box-wrap" style="height:116px;">
							<p>网站域名：
								<?=$server_name?>
							</p>
							<p>网站IP：
								<?=$server_ip?>
							</p>
							<p>当前编码：
								<?=$sys_charset?>
							</p>
							<p>缓存类型：
								<?=$server_htmlize?'静态化':'页面'?>
							</p>
							<p>网站日志：
								<?=admin_yes_no($server_log)?>
							</p>
						</div>
					</div>
					<b class="b3b"></b><b class="b2b"></b><b class="b1b"></b> </div>
				<div class="box right"> <b class="b1"></b><b class="b2"></b><b class="b3"></b>
					<div class="bdiv">
						<div class="btop">
							<h2>服务器<em>Server</em></h2>
							<em>服务器信息摘要</em> </div>
						<div class="bmain box-wrap" style="height:116px;">
							<p>时区设置：
								<?=$server_timezone?>
							</p>
							<p>上传上限：
								<?=$sys_max_upload?>
							</p>
							<p>安全模式：
								<?=admin_yes_no($sys_safe_mode)?>
							</p>
							<p>socket支持：
								<?=admin_yes_no($sys_socket)?>
							</p>
							<p>zlib支持：
								<?=admin_yes_no($sys_zlib)?>
							</p>
						</div>
					</div>
					<b class="b3b"></b><b class="b2b"></b><b class="b1b"></b> </div>
				<div class="clear"></div>
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