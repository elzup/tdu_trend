<?php
/* @var $new_trends stdclass[] */
/* @var $datehour string */
/* @var $ranks stdclass[] */
$t = date('H時のトレンド[y年m月d日]', strtotime($datehour));
?>
<div id="content" class="row">
    <h1 class="green-text"><?= $t ?></h1>
    <div class="col m8 s12">
        <?php 
        $is_wide = TRUE;
        require('./views/parts_rank_div.php');
        ?>
    </div>
    <div class="col m4 s12">
        <div class="card">
            トレンド先導者<br />
            ハッシュタグトレンド<br />
            トレンドURL<br />
            トレンド顔文字<br />
            comming soon
        </div>
    </div>
</div>
