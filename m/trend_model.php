<?php

class TrendModel extends \ActiveRecord {

	// ----------------- DB Manage Wrap ----------------- //

	private function pushPoint($table_name, $text, $count) {
		echo "$text => $count" . PHP_EOL;
		$parameter = array(
			'text' => $text,
			'count' => $count,
		);
		$parameter_dep['count'] = $count;

		DB::insert_add($table_name, $parameter, $parameter_dep);
	}

	private function registProcedeWord($word) {
		$result = DB::getData(db_table_name_spe, 'type', array('word' => $word));
		if (!empty($result)) {
			return;
		}
		$parameter = array(
			'word' => $word,
			'type' => 'p',
		);
		DB::insert(db_table_name_spe, $parameter);
	}

	private function loadTopWords() {
		$result = $this->convertArrayPointFormat(DB::getTable(db_table_name_3, null, null, 50, 'count', true));
		return $result;
	}

}
