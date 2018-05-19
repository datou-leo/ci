<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="header">
	<div id="topbar">
		<?php foreach($menu[0] as $k=>$r){ ?>
		<em><a href="#" class="button-topbar"><span>
		<?=$r[0]?>
		</span></a></em>
		<?php } ?>
		<ul id="tb-left">
			<li class="logo"><a href="">
                    <?php $this->lang->load('common'); ?>
				<?=lang('site_title')?>
				</a></li>
		</ul>
		<!-- <ul id="tb-right"><li><form action="index.php?news/search/1" method="post" id="search_form"><input type="text" name="keyword" id="search_keyword" value="搜索内容" title="搜索内容" autocomplete="off" size="8" /></form></li></ul> -->
	</div>
	<!-- end topbar 注意：em和ul，ul和li之间不能有任何换行，否则ie6下面会出现一个空格，影响布局 -->
	<div id="navis">
		<?php foreach($menu[0] as $k=>$r){ ?>
		<ul class="navi">
			<?php
			if(isset($menu[$k])) foreach($menu[$k] as $kk=>$rr){
				if($rr[4]&&trim($rr[1])!='#' )$rr[1] = site_url($rr[1]);
				$t = ($rr[6]?'rel="ajax"':'');
			?>
			<li><a <?php if($rr[7]==1){ ?>href="javascript:;"<?php } else { ?>href="<?=$rr[1]?>" target="<?=$rr[5]?>" <?=$t?><?php } ?>><span>
				<?=$rr[0]?>
				</span></a>
				<?php if(isset($menu[$kk])&&count($menu[$kk])>0){ ?>
				<ul class="second_menu">
					<?php foreach($menu[$kk] as $kkk=>$rrr){
						if($rrr[4]&&trim($rrr[1])!='#') $rrr[1] = site_url($rrr[1]);
						$t = ($rrr[6]?'rel="ajax"':'');
					?>
					<!-- <li><a href="<?=$rrr[1]?>" target="<?=$rrr[5]?>" <?=$t?>>
						<?=$rrr[0]?>
						</a> -->
					<li><a <?php if($rrr[7]==1){ ?>href="javascript:;"<?php } else { ?>href="<?=$rrr[1]?>" target="<?=$rrr[5]?>" <?=$t?><?php } ?>>
						<?=$rrr[0]?>
						</a>
						<?php if(isset($menu[$kkk])&&count($menu[$kkk])>0){ ?>
						<ul>
							<?php foreach($menu[$kkk] as $kkkk=>$rrrr){
								if($rrr[4]&&trim($rrrr[1])!='#') $rrrr[1] = site_url($rrrr[1]);
								$t = ($rrrr[6]?'rel="ajax"':'');
							?>
							<!-- <li><a href="<?=$rrrr[1]?>" target="<?=$rrrr[5]?>" <?=$t?>>
								<?=$rrrr[0]?>
								</a> -->
							<li><a <?php if($rrrr[7]==1){ ?>href="javascript:;"<?php } else { ?>href="<?=$rrrr[1]?>" target="<?=$rrrr[5]?>" <?=$t?><?php } ?>>
								<?=$rrrr[0]?>
								</a>
								<?php if(isset($menu[$kkkk])&&count($menu[$kkkk])>0){ ?>
								<ul>
									<?php foreach($menu[$kkkk] as $kkkkk=>$rrrrr){
										if($rrr[4]&&trim($rrrrr[1])!='#') $rrrrr[1] = site_url($rrrrr[1]);
										$t = ($rrrrr[6]?'rel="ajax"':'');
									?>
									<li><a href="<?=$rrrrr[1]?>" target="<?=$rrrrr[5]?>" <?=$t?>>
										<?=$rrrrr[0]?>
										</a>
									</li>
									<?php } ?>
								</ul>
								<?php } ?>
							</li>
							<?php } ?>
						</ul>
						<?php } ?>
					</li>
					<?php } ?>
				</ul>
				<?php } ?>
			</li>
			<?php } ?>
		</ul>
		<?php } ?>
	</div>
	<!-- end of navis -->
</div>
<!-- end of header -->