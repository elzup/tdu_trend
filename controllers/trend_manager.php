<?php

class TrendManager
{

	protected $list_name;
	protected $list_tweet;
	protected $list_replay;
	protected $list_word_procede;
	protected $list_word_ng;
	protected $list_client_ng;
	protected $time_stamp_pre;
	protected $mem;
	// word => point
	protected $cache_word_list;
	protected $map_user;

	/**
	 *
	 * @var \TwitterModelTrend
	 */
	protected $twitter;

	/**
	 *
	 * @var \TrendModel
	 */
	protected $trendDAO;

	public function __construct(TwitterOAuth $connection, $owner_name, $list_name, $mem_json_filename) {
		$this->list_name = $list_name;
		$this->mem = array();
		$this->loadMemFile($mem_json_filename);
		//		print_r($this->mem);
		$this->time_stamp_pre = $this->mem->timestamp_tl;

		$this->trendDAO = new TrendModel();
		$this->twitter = new TwitterModelTrend($connection, $owner_name, $list_name, $mem_json_filename);
		$this->initializeSpecialWords();
	}

	protected function initializeSpecialWords() {
		$result = $this->trendDAO->get_special_words();
		foreach ($result as $reco) {
			if (empty($reco['word'])) {
				continue;
			}
			switch ($reco['type']) {
				case 'p':
					$this->list_word_procede[] = $reco['word'];
					break;
				case 'f':
					$this->list_word_ng[] = $reco['word'];
					break;
				case 'c':
					$this->list_client_ng[] = $reco['word'];
			}
		}
	}

	// ----------------- Main management method ----------------- //
	public function manage() {
		$this->list_tweet = $this->twitter->loadList($this->mem->since_list);
		$this->manageListTL();
		$this->saveMemFile();
	}

	public function manageTrendHour() {
		echo '<pre>';
		$words = $this->trendDAO->load_caches();
		// 出現回数を記録
		$this->trendDAO->insert_memorys($this->sortByCount($words));
		$trend_words_all = $this->collectTrends($words);

		$this->trendDAO->insert_logs($trend_words_all);

		$tmp = array_chunk($trend_words_all, TREND_HOUR_WORD_NUM, TRUE);
		$trend_words = $tmp[0];

		$chains = $this->trendDAO->check_chains($trend_words);
		$this->twitter->tweetTrend($trend_words, $chains);
		$this->saveMemFile();
	}

	public function manageTrendDay() {
		$words = $this->trendDAO->load_logs_yesterday();
		$this->twitter->tweetTrendDay($words);
		$this->mem->timestamp_postday = time();
		$this->saveMemFile();
	}

	public function manageMoodHour() {
		$this->mem->resetMood();
	}

	private function manageListTL() {
		if (empty($this->list_tweet)) {
			//			echo "Non newTweet" . PHP_EOL;
			return;
		}
		$lastId = $this->mem->since_list;
		foreach ($this->list_tweet as $tweet) {
			$tw = new Tweet($tweet);
			//			echo $tw;
			$lastId = max($lastId, $tw->id);
			if ($this->isFillterTweet($tw)) {
				continue;
			}
			$this->collectText($tw);
		}
		$this->mem->since_list = $lastId;
		$this->saveTrendPoint();
		$this->mem->timestamp_tl = time();
	}

	private function saveTrendPoint() {
		if (empty($this->cache_word_list)) {
			return;
		}
		$this->trendDAO->regist_words($this->cache_word_list);
	}

	/*
	 * Trim the tweet
	 * replay, Retweeted, fillUrl
	 * NGClient
	 */

	private function isFillterTweet(Tweet $tw) {
		foreach ($this->list_client_ng as $ng) {
			if ($ng == $tw->client_name) {
				return true;
			}
		}
		if ($tw->client_name == "午前3時の茨城県") { //←まじYAMERO
			$this->pushWord("午̷̖̺͈̆͛͝前̧̢̖̫̊3̘̦時̗͡の̶̛̘̙̤̙̌̉͢い̷゙̊̈̓̓̅ば̬̬̩͈̊͡ら゙̜̩̹ぎ̫̺̓ͣ̕͡げ̧̛̩̞̽ん゙̨̼̗̤̂̄", $tw->user_id);
			return true;
		}
		if (strpos(strtolower($tw->text), '@tdu_trend')) {
			return true;
		}
		if (strtolower($tw->user_screen_name) == tw_owner_name) {
			return true;
		}
		return false;
		//        return $tw->isReply || $tw->isMention();
	}

	/*
	 *
	 */

	private function collectText(Tweet $tw) {
		$text = $this->shaveText($tw->text);
		//		$this->checkMood($text);
		$tags = popHashTags($text);
		if (!empty($tags)) {
			foreach ($tags as $t) {
				$this->pushWord($t, $tw);
			}
		}
		// 顔文字の判定と処理
		$faces = trimFaceChars($text);
		if (!empty($faces)) {
			foreach ($faces as $t) {
				if (strlen($t) > 16) {
					continue;
				}
				$this->pushWord($t, $tw);
				$text = str_replace($t, ',', $text);
			}
		}
		// 登録されている(教育された)単語は優先的にチェック
		foreach ($this->list_word_procede as $p) {
			if (strpos($text, $p)) {
				$this->pushWord($p, $tw);
			}
		}

		$tiny = new TinySegmenterarray();
		$words = array_unique($tiny->segment($text, 'UTF-8'));
		foreach ($words as $w) {
			$this->pushCheckWord($w, $tw);
		}
	}

	private function checkMood($text) {
		$this->mem->mood->speed++;
		if (preg_match("/" . wo_mood_zzz . "/u", $text)) {
			$this->mem->mood->zzz++;
		}
		if (preg_match_all("/" . wo_mood_www . "/u", $text, $m = array())) {
			$this->mem->mood->www += count($m[0]);
		}
		if (preg_match("/" . wo_mood_train . "/u", $text)) {
			$this->mem->mood->train++;
		}
		if (preg_match("/" . wo_mood_temp_down . "/u", $text)) {
			$this->mem->mood->temp--;
		}
		if (preg_match("/" . wo_mood_temp_up . "/u", $text)) {
			$this->mem->mood->temp++;
		}
	}

	private function pushCheckWord($word_base, Tweet $tw) {
		$trim_list = array_merge(unserialize(sp_words_ng), unserialize(sp_words_sign));
		$trim_list[] = "\n";
		$trim_list[] = "\r";

		$word_base2 = trimWord($word_base);
		// 最後の半角、全角スペースを、空文字に置き換える
		$word = str_replace($trim_list, '', $word_base2);
		//         $word = preg_replace('/^(w|ｗ){1,5}$/u', "ｗｗｗ", $word);                //草刈機
		//         $word = preg_replace('/^(w|ｗ){6,10}$/u', "ｗｗｗｗｗｗｗ", $word);
		//         $word = preg_replace('/^(w|ｗ){11,}$/u', "ｗｗｗｗｗｗｗｗｗｗｗ", $word);
		$len = strlen($word);
		$mb_len = mb_strlen($word);
		// 単語の長さが短い場合
		if (($len == $mb_len && $len <= 3) || $mb_len == 1 || preg_match("/^[ぁ-んー]{0,3}$/u", $word) || preg_match('/^[一-龠][ぁ-んー]?$/u', $word)) {
			return false;
		}
		// ngな単語を含んでいる場合
		if (in_array($word, $this->list_word_ng)) {
			return false;
		}
		$this->pushWord($word, $tw);
		return true;
	}

	private function pushWord($word, Tweet $tw) {
		$obj = new stdClass();
		$obj->word = $word;
		$obj->twitter_id = $tw->user_id;
		$obj->timestamp = $tw->timestamp;
		$this->cache_word_list[] = $obj;
	}

	/**
	 * テキストから無駄なものをトリムする
	 * @param type $text
	 * @return type
	 */
	private function shaveText($text) {
		return preg_replace(re_url, "", shaveScreenNames($text));
	}

	// ----------------- Mention Manage ----------------- //
	private function manageReplay() {
		if (empty($this->list_replay)) {
			echo "Non newReplay" . PHP_EOL;
			return;
		}

		$lastId = $this->mem->since_mention;
		foreach ($this->list_replay as $tweet) {
			$tw = new Tweet($tweet);
			$lastId = max($lastId, $tw->id);
			if (!$this->twitter->isListFollow($tw->user_screen_name)) {
				// フォロバする
				$this->twitter->followList($tw->user_screen_name);
				$this->twitter->tweetFollowed($tw);
			} elseif (!$this->replayFillter($tw)) {
				// 順にどれか一つを実行
				($this->manageReplayState($tw) || $this->manageReplayNico($tw) || $this->manageReplayEducation($tw) || $this->manageReplayGoogle($tw));
			}
			$this->saveMemFile();
		}
		$this->mem->since_mention = $lastId;
	}

	/*
	 * fillter non replay pattern
	 * -RT, QT
	 */

	private function replayFillter(Tweet $tw) {
		if (preg_match('/(RT|QT).{0,4}@/', $tw->text)) {
			return true;
		}
		if (strtolower($tw->user_screen_name) == 'alicepmaster') {
			return true;
		}
		return false;
	}

	private function manageReplayGoogle(Tweet $tw) {
		$word = preg_replace("/(^ | .*$)/", "", shaveScreenNames($tw->text));
		$datas = getGoogleSuggest($word);
		if (empty($datas)) {
			$text = "( 'ω')";
			$hand = preg_split("/ /", "... /// ??? ｴｯ ん？ ()");
			$handr = $hand[rand(0, count($hand) - 1)];
			$text .= $handr;
		} else {
			$tmp_text = $datas[rand(0, count($datas) - 1)];
			if (strpos($tmp_text, " ") !== false) {
				$hand = preg_split("/ /", "は が で に を な");
				$handr = $hand[rand(0, count($hand) - 1)];
				echo $tmp_text . ":" . $handr;
				$text = preg_replace("/ /", $handr, $tmp_text);
				$text .= (rand(0, 4) == 0 ? "？" : "");
			}
			$text = $this->decoratePanel($text);
		}
		$mtext = "@{$tw->user_screen_name}\n $text";
		if (!ma_debug_tweet) {
			$this->postTweet($mtext, $tw->id);
		}
	}

	private function manageReplayEducation(Tweet $tw) {
		if (!preg_match(re_m_test, $tw->text, $matches = array())) {
			return false;
		}
		$words = explode(',', $matches[2]);
		$text = "";
		foreach ($words as $w) {
			$w = preg_replace("(^ +| +$)", "", $w);
			$l = strlen($w);
			if (strpos($w, "@")) {
				$text .= $this->decoratePanel("@入りはだめ") . "({$w})\n";
			} elseif ($l > 30) {
				$text .= $this->decoratepanel("なげぇよ") . "({$w})\n";
			} else if ($l < 2) {
				$text .= $this->decoratePanel("短い") . "({$w})\n";
			} else {
				$text .= "{$w}を覚えた！\n";
				$this->registProcedeWord($w);
			}
		}
		$mtext = "@{$tw->user_screen_name}\n" . $text;
		if (!ma_debug_tweet) {
			$this->postTweet($mtext, $tw->id);
		}
		return true;
	}

	private function manageReplayNico(Tweet $tw) {
		if (!preg_match(re_m_nico, $tw->text, $matches)) {
			return false;
		}
		$text = "";
		$text .= "　v\n[ 'ω']っ\n";
		$keyword = $matches[3];
		$resultn = get_nico_smurls($keyword, "n");
		$resultm = get_nico_smurls($keyword, "m");

		$ra = rand(0, 5);
		$text1 = $resultn[$ra]['url'] . " " . mb_substr($resultn[$ra]['name'], 0, 15) . "…";
		$text .= $text1 . "\n";
		$rb = rand(0, 5);
		if ($resultn[$ra]['url'] != $resultm[$rb]['url']) {
			$text2 = $resultm[$rb]['url'] . " " . mb_substr($resultm[$rb]['name'], 0, 15) . "…";
			$text .= $text2;
		}
		echo $mtext = "@{$tw->user_screen_name}\n" . $text;
		if (!ma_debug_tweet) {
			$this->postTweet($mtext, $tw->id);
		}
		return true;
	}

	public function manageReplayState(Tweet $tw, $debug = false) {
		if (preg_match(re_m_wind_spe, $tw->text)) {
			$h = date('H');
			$n = date('H-i');
			$pph = sprintf('%.2f', $this->mem->getPph());
			$text = "[" . ($h - 1) . ":00 - " . $n . "] " . $pph . "pph\n";
			$text .= $this->rateSpeed($pph) . "\n";
			$text .= $this->rateLaugh($this->mem->getPpw()) . "\n";
			$text .= $this->rateSleep($this->mem->getPpz()) . "\n";
			echo $mtext = "@{$tw->user_screen_name}\n" . $text;
			if (!ma_debug_tweet) {
				$this->postTweet($mtext, $tw->id);
			}
			return true;
		}
		return false;
	}

	// ----------------- TrendCollecting ----------------- //
	private function sortByCount($words) {
		$arr = array();
		foreach ($words as $word) {
			if (!isset($arr[$word->word])) {
				$arr[$word->word] = 0;
			}
			$arr[$word->word]++;
		}
		return $arr;
	}

	private function collectTrends($words) {
		$counts = array();
		$persons = array();
		foreach ($words as $word) {
			if (!trimWord($word->word)) {
				continue;
			}
			if (!isset($counts[$word->word])) {
				$counts[$word->word] = 0;
				$persons[$word->word] = array();
			}
			$counts[$word->word]++;
			if (!in_array($i = $word->twitter_id, $persons[$word->word])) {
				$persons[$word->word][] = $i;
			}
		}
		foreach ($counts as $word => &$count) {
			$count = $this->reflectPersion($count, count($persons[$word]));
			$count = $this->reflectMemory($word, $count);
		}
		arsort($counts);
		return $counts;
	}

	private function reflectMemory($word, $count) {
		$num = $this->trendDAO->count_memory($word);
		return max($count - sqrt($num), 1);
	}

	private function reflectPersion($count, $person_num) {
		return $count * $this->personRaito($person_num);
	}

	private function personRaito($num) {
		return sqrt($num);
	}

	private function collectTopTrend($data_tmp) {
		$data = $this->trendSort($data_tmp);
		$tops = array();
		foreach ($data as $key => $value) {
			$tops[$key] = $value;
			if (count($tops) > 5) {
				break;
			}
		}
		return $tops;
	}

	private function shiftPopDb($table_name_pop, $table_name_add) {
		$data_pop = DB::getTable($table_name_pop);
		if (!ma_debug_tweet) {
			foreach ($data_pop as $datum) {
				$this->pushPoint($table_name_add, $datum['text'], $datum['count']);
			}
			DB::deleteTable($table_name_pop);
		}
		return $this->convertArrayPointFormat($data_pop);
	}

	private function trendSort($data) {
		arsort($data);
		foreach ($data as $key => $value) {
			if ($value == 1) {
				unset($data[$key]);
			}
		}
		$tops = $this->loadTopWords();
		$i = 0;
		foreach ($data as $key_1 => &$value_1) {
			if ($i++ > 23) {
				break;
			}
			if (preg_match('/^(w|ｗ)+$/u', $value_1)) {
				//                $value_1 = $value_1 / 10;
				continue;
			}
			foreach ($data as $key_2 => &$value_2) {
				if ($key_1 == $key_2) {
					continue;
				}
				if (strpos($key_1, $key_2) !== false && $value_1 >= $value_2 / 2) {
					$value_1 += $value_2 / 2;
					$value_2 = 0;
				}
			}
		}
		foreach ($data as $key => &$value) {
			foreach ($tops as $key_t => $value_t) {
				if ($key == $key_t) {
					$value -= $value_t / 100;
				}
			}
			/////
		}
		arsort($data);
		return $data;
	}

	// ----------------- DB file IO ----------------- //
	public function regist_word($word) {
		// TODO: tweet 条件分けして 処理 
		var_dump($this->trendDAO->regist_procede_word($word));
	}

	// ----------------- Mem file IO ----------------- //
	private function loadMemFile($filename) {
		$data = file_get_contents($filename) or super_die(array('Error' => 'File get', 'method' => __METHOD__));
		$this->mem = new Memory(json_decode($data));
		$this->mem_json_filename = $filename;
	}

	private function saveMemFile() {
		if (empty($this->mem)) {
			super_die(array('Warning' => 'Attemp to save empty data', 'method' => __METHOD__));
		}
		$data = json_encode($this->mem);
		var_dump($data);
		file_put_contents($this->mem_json_filename, $data) or super_die(array('Error' => 'File put', 'method' => __METHOD__));
	}

	public function getLastTimestamp() {
		$text = "LastManageTL: " . date(fo_date, $this->mem->timestamp_tl);
		$text .= "(-" . date(fo_date, (time() - $this->mem->timestamp_tl)) . ")\n";
		return $text;
	}

	// ----------------- sub ----------------- //


	private function isTimeManage() {
		return date('i') % 5 == 0;
	}

	private function isTimeHourly() {
		$time_h = date('H');
		$last_h = date('H', $this->mem->timestamp_post);
		echo '{hourt post check}';
		echo $time_h . "::" . $last_h . PHP_EOL;
		return $time_h != $last_h;
	}

	private function isTimeDayly() {
		return date("H") == 0;
	}

	private function isTimeDelay() {
		return (date("i") + 7) % 5 == 0;
	}

	public function createChainNum($words) {
		$chains_pre = $this->mem->chain;
		$chains = array();
		foreach ($words as $word => $v) {
			$chains[$word] = 1;
			if (isset($chains_pre[$word])) {
				$chains[$word] += $chains_pre[$word];
			}
		}
		$this->mem->chain = $chains;
		return $chains;
	}

	public function _memoryMoodInitialize() {
		$this->mem->mood->mood_eee = 0;
		$this->mem->mood->mood_pre = 0;
		$this->mem->mood->mood_speed = 0;
		$this->mem->mood->mood_temp = 0;
		$this->mem->mood->mood_train = 0;
		$this->mem->mood->mood_ttt = 0;
		$this->mem->mood->mood_wea = 0;
		$this->mem->mood->mood_zzz = 0;
		$this->saveMemFile();
	}

}
