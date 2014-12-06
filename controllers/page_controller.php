<?php

class PageController {

	/**
	 *
	 * @var \TrendModel
	 */
    public $trendDAO;

    public function __construct() {
        $this->trendDAO = new TrendModel();
    }

    public function showIndex() {
        $title = '電大トレンド君 on Web';
		$trendDAO = new TrendModel();

        require_once('./views/head.php');
        require_once('./views/body_wrapper_head.php');
        require_once('./views/toppage_eyecatch.php');
        $ranks_list = $this->trendDAO->load_logs_recent(TREND_HOUR_CARD_NUM_VIEW);
        require_once('./views/toppage_main.php');
        require_once('./views/body_wrapper_foot.php');
        require_once('./views/foot.php');
    }

    public function showLog($datehour_str) {
        $datehour = numtodatehour($datehour_str);
        $title = $datehour . '電大トレンド君 on Web';

        require_once('./views/head.php');
        require_once('./views/body_wrapper_head.php');
        require_once('./views/header_navbar.php');
        $ranks = $this->trendDAO->load_logs($datehour, TREND_HOUR_WORD_NUM_VIEW_DESC);
        require_once('./views/logpage_main.php');
        require_once('./views/body_wrapper_foot.php');
        require_once('./views/foot.php');
    }

}
