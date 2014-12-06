<?php

class PageController {

    public function showIndex() {
        $title = '電大トレンド君 on Web';
		$trendDAO = new TrendModel();
        $now_time = date(MYSQL_TIMESTAMP_DATEHOUR);
        $now_time = '2014-12-06 14:00:00';
        $new_trends = $trendDAO->load_logs($now_time);

        require_once('./views/head.php');
        require_once('./views/body_wrapper_head.php');
        require_once('./views/toppage_eyecatch.php');
        require_once('./views/toppage_main.php');
        require_once('./views/body_wrapper_foot.php');
        require_once('./views/foot.php');
    }
}
