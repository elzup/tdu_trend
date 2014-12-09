<?php
/* @var $ranks stdclass[] */
/* @var $is_wide boolean */
if (!isset($is_wide)) {
    $is_wide = FALSE;
}
$datehour = $ranks[0]->datehour;
if ($is_wide) {
    $rank_title = date(FORMAT_RANKS_TITLE_DATEHOUR, strtotime($datehour)) . 'のトレンド';
} else {
    $rank_title = '<span class="main">' . date("H時", strtotime($datehour)) . 'のトレンド</span><span class="min-date">' . date(FORMAT_RANKS_TITLE_DATE, strtotime($datehour)) . '</span>';
}
$page_link = SITE_ROOT . URL_LOG . datehourtonum($datehour);
?>

<div class="card">
    <div class="card-content green-text">
        <span class="card-title"><a href="<?= $page_link ?>"><?= $rank_title ?></a></span>
        <table class="pure-table pure-table-bordered rank-table">
            <tbody>
                <?php foreach ($ranks as $i => $rank): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= $rank->word ?></td>
                        <td><?= createRateTextFromPoint($rank->point) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-action">
        <a href="<?= $page_link ?>">もっと見る</a>
    </div>
</div>
