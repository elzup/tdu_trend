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
        $datehour = date(MYSQL_TIMESTAMP_DATEHOUR);
        $datehour = '2014-12-05 16:00:00';
        $datehour = date(MYSQL_TIMESTAMP_DATEHOUR, time() - 60 * 60);
        $datehour2 = '2014-12-05 16:00:00';

        require_once('./views/head.php');
        require_once('./views/body_wrapper_head.php');
        require_once('./views/toppage_eyecatch.php');
        $ranks = $this->trendDAO->load_logs($datehour, TREND_HOUR_WORD_NUM_VIEW);
        $ranks_pre = $this->trendDAO->load_logs($datehour2, TREND_HOUR_WORD_NUM_VIEW);
        require_once('./views/toppage_main.php');
        require_once('./views/body_wrapper_foot.php');
        require_once('./views/foot.php');
    }

    public function showLog($datehour_str) {
        $datehour = numtodatehour($datehour_str);
        $title = $datehour . '電大トレンド君 on Web';
        $ranks = $this->trendDAO->load_logs($datehour, TREND_HOUR_WORD_NUM_VIEW_DESC);

        require_once('./views/head.php');
        require_once('./views/body_wrapper_head.php');
        require_once('./views/toppage_eyecatch.php');
        $ranks = $this->trendDAO->load_logs($datehour, TREND_HOUR_WORD_NUM_VIEW);
        require_once('./views/toppage_main.php');
        require_once('./views/body_wrapper_foot.php');
        require_once('./views/foot.php');
    }

}
