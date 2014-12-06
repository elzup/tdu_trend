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
    $rank_title = '<span class="main">' . date("H時", strtotime($datehour)) . 'のトレンド</span><span class="date">' . date(FORMAT_RANKS_TITLE_DATE, strtotime($datehour)). '</span>';
}
?>

<section class="rankbox">
    <h3><a href="<?= SITE_ROOT . URL_LOG . datehourtonum($datehour)?>"><?= $rank_title ?></a></h3>
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
</section>