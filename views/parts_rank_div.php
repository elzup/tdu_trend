<?php
/* @var $ranks stdclass[] */
$datehour = $ranks[0]->datehour;
$rank_title = date(FORMAT_RANKS_TITLE_DATE, strtotime($datehour)) . 'のトレンド';
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