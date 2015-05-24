<?php
/* @var $new_trends stdclass[] */
/* @var $ranks_list array */
$cc = 0;
?>
<div id="content" class="row">
	<div class="col m8">
		<div class="row">
			<?php foreach ($ranks_list as $v) { ?>
			<div class="col m6">
				<?php
				$chunks = array_chunk($v, TREND_HOUR_WORD_NUM_VIEW);
				$ranks = $chunks[0];
				require('./views/parts_rank_div.php');
				?>
			</div>
			<?php
			if ($cc % 2 == 1) {
			?>
		</div>
		<div class="row">
			<?php
			}
			$cc++;
			}
			?>
		</div>
	</div>
	<div class="col m4">
		<div class="card">
			トレンド先導者<br/>
			ハッシュタグトレンド<br/>
			トレンドURL<br/>
			トレンド顔文字<br/>
			comming soon
		</div>
	</div>
</div>