<?php

class Memory {

	public $point_top_day;
	public $point_top;
	public $since_list;
	public $since_mention;
	public $count_hour;
	public $count_hourp;
	public $timestamp_tl;
	public $timestamp_post;
	public $timestamp_postday;
	public $mood;
	public $chain;

	public function __construct(stdClass $m) {
		$this->point_top_day = $m->point_top_day;
		$this->point_top = $m->point_top;
		$this->since_list = $m->since_list;
		$this->since_mention = $m->since_mention;
		$this->count_hour = $m->count_hour;
		$this->count_hourp = $m->count_hourp;
		$this->timestamp_tl = $m->timestamp_tl;
		$this->timestamp_post = $m->timestamp_post;
		$this->timestamp_postday = $m->timestamp_postday;
		$this->mood = new stdClass();
		$this->mood->zzz = $m->mood->zzz;
		$this->mood->www = $m->mood->www;
		$this->mood->train = $m->mood->train;
		$this->mood->temp = $m->mood->temp;
		$this->mood->speed = $m->mood->speed;
		$this->mood->zzz_pre = $m->mood->zzz_pre;
		$this->mood->www_pre = $m->mood->www_pre;
		$this->mood->train_pre = $m->mood->train_pre;
		$this->mood->temp_pre = $m->mood->temp_pre;
		$this->mood->speed_pre = $m->mood->speed_pre;
		$this->chain = $m->chain;
	}

	public function resetMood() {
		$zzz_pre = $this->mood->zzz;
		$www_pre = $this->mood->www;
		$train_pre = $this->mood->train;
		$temp_pre = $this->mood->temp;
		$speed_pre = $this->mood->speed;
		foreach ($this->mood as &$v)
			$v = 0;
		$this->mood->zzz_pre = (empty($zzz_pre) ? 0 : $zzz_pre);
		$this->mood->www_pre = (empty($www_pre) ? 0 : $www_pre);
		$this->mood->train_pre = (empty($train_pre) ? 0 : $train_pre);
		$this->mood->temp_pre = (empty($temp_pre) ? 0 : $temp_pre);
		$this->mood->speed_pre = (empty($speed_pre) ? 0 : $speed_pre);
	}

	public function getPph() {
		return $this->pp('speed');
	}

	public function getPpw() {
		return $this->pp('www');
	}

	public function getPpz() {
		return $this->pp('zzz');
	}

	public function getPpe() {
		return $this->pp('temp');
	}

	public function getPpt() {
		return $this->pp('trein');
	}

	private function pp($val) {
		$ip = 60 / date('i');
		$total = $this->mood->{$val . "_pre"} + $this->mood->{$val} * $ip;
		return $total / 2;
	}

}
