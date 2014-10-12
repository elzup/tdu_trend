<?php

class JobController {

    public function walk_tl()
    {
		$tm = $this->_generate_manager();
		$tm->manage();
    }
    public function tweet_hour()
    {
		$tm = $this->_generate_manager();
		$tm->manageTrendHour();
    }
    public function tweet_day()
    {
		$tm = $this->_generate_manager();
		$tm->manageTrendDay();
    }
	private function _generate_manager() {
		$c = new TwitterOAuth(ap_consumer_key, ap_consumer_secret, ap_access_token, ap_access_token_scret);
		return new TrendManager($c, tw_owner_name, tw_list_name, mem_json_filename);
	}

}
