<?php

class JobController {

	public function walk_tl() {
        $this->checkAuth();
		$tm = JobController::_generate_manager();
		$tm->manage();
	}

	public function tweet_hour() {
        $this->checkAuth();
		echo '<pre>';
		$tm = JobController::_generate_manager();
		$tm->manage();
		$tm->manageTrendHour();
	}

	public function tweet_day() {
        $this->checkAuth();
		echo '<pre>';
		$tm = JobController::_generate_manager();
		$tm->manage();
		$tm->manageTrendHour();
		$tm->manageTrendDay();
	}

    private function checkAuth() {
        if (ENV != ENV_DEVELOP && @$_GET['key'] != SECRET_KEY) {
                die('invalid access');
        }
    }

	public function regist_word($word) {
		$tm = JobController::_generate_manager();
		$tm->regist_word($word);
	}

	private static function _generate_manager() {
		$c = new TwitterOAuth(AP_CONSUMER_KEY, AP_CONSUMER_SECRET, AP_ACCESS_TOKEN, AP_ACCESS_TOKEN_SCRET);
		return new TrendManager($c, tw_owner_name, tw_list_name, mem_json_filename);
	}

}
