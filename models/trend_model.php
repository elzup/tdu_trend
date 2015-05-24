<?php

class TrendModel
{

	public function __construct() {
	}

	// ----------------- DB Manage Wrap ----------------- //

	public function regist_word($word) {
		$cache = ORM::for_table(DB_TN_CACHES);
		$cache->set(array(DB_CN_CACHES_WORD => $word->word, DB_CN_CACHES_TWITTER_ID => $word->twitter_id, DB_CN_CACHES_TIMESTAMP => date(MYSQL_TIMESTAMP, $word->timestamp),));
		$cache->save();
		return $cache->id();
	}

	public function regist_words(array $words) {
		$ids = array();
		foreach ($words as $word) {
			$ids[] = $this->regist_word($word);
		}
		return $ids;
	}

	public function regist_procede_word($word) {
		return $this->insert_procede_word($word);
	}

	private function insert_procede_word($word) {
		return $this->insert_special_word($word, 'p');
	}

	private function insert_special_word($word, $type) {
		$special = ORM::for_table(DB_TN_SPECIALS);
		$special->set(array(DB_CN_SPECIALS_WORD => $word, DB_CN_SPECIALS_TYPE => $type,));
		$special->save();
		return $special->id();
	}

	public function insert_logs($words) {
		$dh = date(MYSQL_TIMESTAMP_DATEHOUR);
		foreach ($words as $word => $point) {
			$this->insert_log($word, $point, $dh);
		}
	}

	public function insert_memorys($words) {
		$date = date(MYSQL_TIMESTAMP_DATE);
		foreach ($words as $word => $point) {
			$this->insert_memory($word, $point, $date);
		}
	}

	private function insert_log($word, $point, $datehour) {
		$log = ORM::for_table(DB_TN_LOGS)->create();
		$log->set(array(DB_CN_LOGS_WORD => $word, DB_CN_LOGS_POINT => $point, DB_CN_LOGS_DATEHOUR => $datehour,));
		$log->save();
		return $log->id();
	}

	private function insert_memory($word, $count, $date) {
		$memory = ORM::for_table(DB_TN_MEMORYS);
		$memory->set(array(DB_CN_MEMORYS_WORD => $word, DB_CN_MEMORYS_COUNT => $count, DB_CN_MEMORYS_DATE => $date,));
		$memory->save();
		return $memory->id();
	}

	public function load_logs_recent($num) {
		$recent = date(MYSQL_TIMESTAMP_DATEHOUR, time() - 60 * 60 * ($num - 1));
		if (ENV == ENVIRONMENT_DEV) {
			// デバッグ時は多めに取る
			$recent = date(MYSQL_TIMESTAMP_DATEHOUR, time() - 60 * 60 * ($num - 1) - 24 * 60 * 50 * 4);
		}
		$logs = ORM::for_table(DB_TN_LOGS)->where_gte(DB_CN_LOGS_DATEHOUR, $recent)->order_by_desc(DB_CN_LOGS_DATEHOUR)->order_by_desc(DB_CN_LOGS_POINT)->find_many();
		if (count($logs) == 0) {
			return NULL;
		}
		$res = array();
		// TODO: 動くか？, check dump
		foreach ($logs as $rec) {
			if (!isset($res[$rec->datehour])) {
				$res[$rec->datehour] = array();
			}
			if (count($res[$rec->datehour]) > TREND_HOUR_WORD_NUM_VIEW) {
				continue;
			}
			$res[$rec->datehour][] = $rec;
		}
		return $res;
	}

	public function load_logs($time, $limit = NULL) {
		$sql = ORM::for_table(DB_TN_LOGS)->where(DB_CN_LOGS_DATEHOUR, $time)->order_by_desc(DB_CN_LOGS_POINT);
		if (isset($limit)) {
			$sql->limit($limit);
		}
		return $sql->find_many();
	}

	public function load_caches() {
		$words = $this->select_cache_top();
		$this->delete_caches_all();
		return $words;
	}

	public function load_logs_yesterday() {
		$yesterday = date(MYSQL_TIMESTAMP_DATE, time() - (60 * 60 * 24));
		return $this->select_logs_date($yesterday);
	}

	public function select_logs_date($date, $num = TREND_DAY_WORD_NUM) {
		$stmt = $this->query('SELECT ' . DB_CN_LOGS_WORD . ', sum(' . DB_CN_LOGS_POINT . ') as point_sum FROM `' . DB_TN_LOGS . '` WHERE ' . DB_CN_LOGS_DATEHOUR . ' between \'' . $date . ' 00:00:00\' and \'' . $date . ' 23:59:59\' group by ' . DB_CN_LOGS_WORD . ' ORDER BY sum(point) DESC LIMIT ' . $num);
		$logs = ORM::for_table(DB_TN_LOGS)->select(DB_CN_LOGS_WORD, '')->select('sum(' . DB_CN_LOGS_POINT . ')', 'point_sum')->where_raw('(' . DB_CN_LOGS_DATEHOUR . ' BETWEEN ? AND ?)', array("{$date} 00:00:00", "{$date} 23:59:59"))->group_by(DB_CN_LOGS_WORD)->order_by_desc('sum(point)')->limit(($num))->find_many();
		return $logs;
	}

	public function delete_caches_all() {
		$this->query('delete FROM ' . DB_TN_CACHES);
	}

	public function select_cache_top($limit = TOP_LIMIT) {
		$stmt = $this->query('SELECT * FROM ' . DB_TN_CACHES . ' WHERE ' . DB_CN_CACHES_WORD . ' in ( )');
		$caches = ORM::for_table(DB_TN_CACHES)->where(DB_CN_CACHES_WORD)->raw_query('in (select ' . DB_CN_CACHES_WORD . ' from( select ' . DB_CN_CACHES_WORD . ' from `tt_caches` group by ' . DB_CN_CACHES_WORD . ' order by count(' . DB_CN_CACHES_WORD . ') DESC limit ? ) as t)', array($limit))->find_many();
		return $caches;
	}

	public function select_cache_all() {
		return ORM::for_table(DB_TN_CACHES)->find_many();
	}

	public function get_special_words() {
		return ORM::for_table(DB_TN_SPECIALS)->find_many();
		//        return $this->stmt_to_row($stmt);
	}

	public function check_trendy($words) {

	}

	/**
	 * 単語リストがそれぞれ何度連続でトレンド入りしたのかをチェックする
	 * @param string[] $words
	 * @return int[]
	 */
	public function check_chains($words) {
		$h = 1;
		$chains = array();
		foreach ($words as $word => $p) {
			$chains[$word] = 0;
		}
		//		echo "---\n";
		//		echo json_encode($words);
		while (TRUE) {
			$time = date(MYSQL_TIMESTAMP_DATEHOUR, strtotime('-' . $h . 'hour'));
			$logs = $this->load_logs($time);
			//			echo json_encode($logs);
			$k = FALSE;
			foreach ($words as $word => $p) {
				if (($h - 1) != $chains[$word]) {
					continue;
				}
				foreach ($logs as $i => $log) {
					if ($log->word != $word) {
						continue;
					}
					$chains[$word]++;
					break;
				}
				$k = TRUE;
			}
			if (!$k) {
				break;
			}
			$h++;
		}
		//		echo json_encode($chains);
		//		echo "\n---\n";
		return $chains;
	}

	public function count_memory($word) {
		$date_after7 = date(MYSQL_TIMESTAMP_DATE, strtotime('-7day'));
		$res = ORM::for_table(DB_TN_MEMORYS)->select('SELECT sum(`' . DB_CN_MEMORYS_COUNT . '`)', 'sum')->where(DB_CN_MEMORYS_WORD, $word)->where_gt(DB_CN_MEMORYS_DATE, $date_after7)->find_one();

		return $res;
	}
}
