<?php
/* @var $new_trends stdclass[] */
/* @var $ranks_list array */
?>
<div id="content" class="pure-g-r">
    <div id="mainContent" class="pure-u-2-3">
        <div class="pure-g-r">
            <?php foreach ($ranks_list as $v) { ?>
            <div class="pure-u-1-2">
                <?php
                $chunks = array_chunk($v, TREND_HOUR_WORD_NUM_VIEW);
                $ranks = $chunks[0];
                require('./views/parts_rank_div.php');
                ?>
            </div>
            <?php } ?>
        </div>
    </div>
    <div id="subContent" class="pure-u-1-3">
        トレンド先導者
        ハッシュタグトレンド
        トレンドURL
        トレンド顔文字
    </div>
</div>