<?php

class JobController {

	public static function walk_tl() {
		$tm = JobController::_generate_manager();
		$tm->manage();
	}

	public static function tweet_hour() {
		$tm = JobController::_generate_manager();
		$tm->manageTrendHour();
	}

	public static function tweet_day() {
		$tm = JobController::_generate_manager();
		$tm->manageTrendDay();
	}

	public static function regist_word($word) {
		$tm = JobController::_generate_manager();
		$tm->regist_word($word);
	}

	private static function _generate_manager() {
		var_dump(class_exists('TwitterOAuth'));
		$c = new TwitterOAuth(AP_CONSUMER_KEY, AP_CONSUMER_SECRET, AP_ACCESS_TOKEN, AP_ACCESS_TOKEN_SCRET);
		return new TrendManager($c, tw_owner_name, tw_list_name, mem_json_filename);
	}

}
