<?php

class TrendModel extends PDO {

	public function __construct() {
		$this->engine = DB_ENGINE;
		$this->host = DB_HOST;
		$this->database = DB_NAME;
		$this->user = DB_USER;
		$this->pass = DB_PASSWORD;
		$dns = $this->engine . ':dbname=' . $this->database . ";host=" . $this->host;
		parent::__construct($dns, $this->user, $this->pass);
	}

	// ----------------- DB Manage Wrap ----------------- //

	public function regist_words(array $words) {
		$sql = 'INSERT INTO ' . DB_TN_CACHES . ' (' . DB_CN_CACHES_WORD . ', ' . DB_CN_CACHES_TWITTER_ID . ', ' . DB_CN_CACHES_TIMESTAMP . ') VALUES ';
		$sql_values = array();
		for ($i = 0; $i < count($words); $i++) {
			$sql_values[] = "(:WORD$i, :TID$i, :TS$i)";
		}
		$sql .= implode(',', $sql_values);
		$stmt = $this->prepare($sql);
		foreach ($words as $i => $word) {
			$stmt->bindValue(":WORD$i", $word->word);
			$stmt->bindValue(":TID$i", $word->twitter_id);
			$stmt->bindValue(":TS$i", date(MYSQL_TIMESTAMP, $word->timestamp));
		}
		return $stmt->execute();
	}

	public function regist_procede_word($word) {
		return $this->insert_procede_word($word);
	}

	private function insert_procede_word($word) {
		return $this->insert_special_word($word, 'p');
	}

	private function insert_special_word($word, $type) {
		$stmt = $this->prepare('INSERT INTO ' . DB_TN_SPECIALS . ' (' . DB_CN_SPECIALS_WORD . ', ' . DB_CN_SPECIALS_TYPE . ') VALUES (:WORD, :TYPE)');
		$stmt->bindValue(':WORD', $word);
		$stmt->bindValue(':TYPE', $type);
		return $stmt->execute();
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
		$stmt = $this->prepare('INSERT INTO ' . DB_TN_LOGS . ' (' . DB_CN_LOGS_WORD . ', ' . DB_CN_LOGS_POINT . ', ' . DB_CN_LOGS_DATEHOUR . ') VALUES (:WORD, :POINT, :DATEHOUR)');
		$stmt->bindValue(':WORD', $word);
		$stmt->bindValue(':POINT', $point);
		$stmt->bindValue(':DATEHOUR', $datehour);
		return $stmt->execute();
	}

	private function insert_memory($word, $count, $date) {
		$stmt = $this->prepare('INSERT INTO ' . DB_TN_MEMORYS . ' (' . DB_CN_MEMORYS_WORD . ', ' . DB_CN_MEMORYS_COUNT . ', ' . DB_CN_MEMORYS_DATE . ') VALUES (:WORD, :COUNT, :DATE) ON DUPLICATE KEY UPDATE ' . DB_CN_MEMORYS_COUNT . ' = ' . DB_CN_MEMORYS_COUNT . ' + :COUNT');
		$stmt->bindValue(':WORD', $word);
		$stmt->bindValue(':COUNT', $count);
		$stmt->bindValue(':DATE', $date);
		return $stmt->execute();
	}

	public function load_caches() {
		$words = $this->select_cache_top();
		$this->delete_caches_all();
		return $words;
	}

	public function delete_caches_all() {
		$this->query('delete FROM ' . DB_TN_CACHES);
	}

	public function select_cache_top($limit = TOP_LIMIT) {
		$stmt = $this->query('SELECT * FROM ' . DB_TN_CACHES . ' WHERE ' . DB_CN_CACHES_WORD . ' in ( select ' . DB_CN_CACHES_WORD . ' from( select ' . DB_CN_CACHES_WORD . ' from `tt_caches` group by ' . DB_CN_CACHES_WORD . ' order by count(' . DB_CN_CACHES_WORD . ') DESC limit ' . $limit . ' ) as t)');
		return $stmt->fetchAll(PDO::FETCH_CLASS);
	}

	public function select_cache_all() {
		$stmt = $this->query('SELECT * FROM ' . DB_TN_CACHES);
		return $stmt->fetchAll();
	}

	public function get_special_words() {
		$sql = 'SELECT * FROM ' . DB_TN_SPECIALS;
		$stmt = $this->prepare($sql);
		$stmt->execute();
		return $this->stmt_to_row($stmt);
	}

	public function check_trendy($words) {
	}

	public function count_memory($word) {

	}

	private function stmt_to_row($stmt) {
		$rows = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$rows[] = $row;
		}
		return $rows;
	}

}
