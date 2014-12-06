<?php
/* @var $ranks stdclass[] */
$rank_title = date(FORMAT_RANKS_TITLE_DATE, strtotime($ranks[0]->datehour)) . 'のトレンド';
?>

<section class="rankbox">
    <h3><?= $rank_title ?></h3>
    <table class="pure-table pure-table-bordered rank-table">
        <tbody>
            <?php foreach ($ranks as $i => $rank): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= $rank->word ?></td>
                    <td><?= $rank->point ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>