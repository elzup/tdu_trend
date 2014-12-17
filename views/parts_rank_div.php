<?php
/* @var $ranks stdclass[] */
/* @var $is_wide boolean */
if (!isset($is_wide)) {
    $is_wide = FALSE;
}
$datehour = $ranks[0]->datehour;

$page_link = SITE_ROOT . URL_LOG . datehourtonum($datehour);
?>

<div class="card card-rankbox">
    <div class="card-content green-text">
        <span class="card-title">
            <?php if (!$is_wide) { ?>
                <span class="min-date"><?= date(FORMAT_RANKS_TITLE_DATE, strtotime($datehour)) ?></span><br />
                <a href="<?= $page_link ?>">
                    <?= date("H時", strtotime($datehour)) ?>のトレンド
                </a>
            <?php } ?>
        </span>
        <table class="hoverable rank-table">
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
    <?php if (!$is_wide) { ?>
        <div class="card-action">
            <a class="btn more-btn" href="<?= $page_link ?>">More<i class="mdi-av-equalizer right"></i></a>
        </div>
    <?php } ?>
</div>
