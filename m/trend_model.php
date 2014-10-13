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

	public function loadTopWords() {
		$result = $this->convertArrayPointFormat(DB::getTable(db_table_name_3, null, null, 50, 'count', true));
		return $result;
	}

	public function get_special_words() {
		$sql = 'SELECT * FROM ' . DB_TN_SPECIALS;
		$stmt = $this->prepare($sql);
		$stmt->execute();
		return $this->stmt_to_row($stmt);
	}

	private function stmt_to_row($stmt) {
		$rows = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$rows[] = $row;
		}
		return $rows;
	}

}
