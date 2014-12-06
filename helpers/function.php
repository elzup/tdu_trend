<?php
//ゆ

define('rep_hand', '(:?\w|:?[／＼L><＞＜」卍└┘⊃⊂])?');
define('re_aa_face', '/' . rep_hand . '[\(（][^\(（\)）]+[\)）]' . rep_hand . '/u');
define('re_face', '/(:?\w|:?[／＼L」└┘⊃⊂])?[\(（][^\)）]+[\)）](:?\w|:?[／＼L」└┘⊃⊂])?/u');
define('re_url', '|https?://\w+(?:-\w+)*(?:\.\w+(?:-\w+)*)+(?::\d+)?(?:[/\?][\w%&=~\-\+/;\.\?]*(?:#[^<\s>]*)?)?|');
define('re_hashtag', '/#[^\s]+/u');

define('re_m_test', '/テスト[^(（]*(\s)?[(（](.*)[)）]/iu');
define('re_m_wind', '/風.*[強騒つさ]/iu');
define('re_m_wind_spe', '/(風|[tT][lL])/iu');
define('re_m_nico', '/(にこ|ニコ|25).*(\s)?[(（](.*)[)）]/ui');

define('fo_date', "Y-m-d H:i:s");

function setDB() {

	mb_regex_encoding('UTF-8');

	mb_language("uni");
	mb_internal_encoding("utf-8"); //内部文字コードを変更
	mb_http_input("auto");
	mb_http_output("utf-8");

	$link = mysqli_connect(db_url, db_user, db_password) or die("connectError:" . mysqli_error($link));
	mysqli_set_charset($link, "utf8")or die("ERROR charset");
	mysqli_select_db($link, db_name)or die('selectError:' . mysql_error());

	DB::$link = $link;
}

function setupEncodeing() {
	$charset = "utf8";
	header('Content-type:text/html; charset=utf-8');
	mb_regex_encoding('UTF-8');
	if (isset($_GET['pre']))
		echo "<pre>";
}

function debug($key) {
	return isset($_GET[$key]);
}

function getMemParamater($ver = '') {
	$mem = array();
	$fdata = readTextFile(fl_file_name . $ver . ".txt");
	foreach ($fdata as $data) {
		$mem["$data[0]"] = trimReturn($data[1]);
	}
	return $mem;
}

function setMemParameter($mem, $ver = '') {
	$text = "";
	foreach ($mem as $key => $data) {
		$text .= $key . "," . $data . "\n";
	}
	echo $text;
	putTextFile(fl_file_name . $ver . ".txt", $text);
}

function readTextFile($fname) {
	$fp = fopen($fname, "r");
	$datas = array();
	$i = 0;
	while ($line = fgets($fp)) {
		$keywords = preg_split("/,+/", $line);
		$datas[$i++] = $keywords;
	}
	return $datas;
}

function putTextFile($fname, $text) {
	file_put_contents($fname, $text);
}

function trimReturn($str) {
	return str_replace(array("\r\n", "\n", "\r"), '', $str);
}

function toCompText($text, $toArray = false) {
	if (empty($text))
		return null;
	if ($toArray) {
		$array = array();
		if (strpos($text, cut) === false)
			$array[0] = $text;
		else
			$array = array_unique(preg_split("/\s*[,]\s*/", $text));
		return $array;
	}
	if (strpos($text, cut) === false)
		return $text;
	return strtolower(implode(array_unique(preg_split("/\s*[,]\s/", $text))));
}

function getRandMaterial($array, $isImageArray = false) {
	if (empty($array))
		return false;
	if ($count = count($array) == 0 || !isset($array))
		return false;

	if ($isImageArray) {
		return $array[array_rand($array)];
	}
	if (count($array) == 1)
		return $array[0];
	return $array[rand(0, count($array) - 1)];
}

function getout($code, $str = "") {
	echo B . "【" . $code . "】" . $str . B;
}

function str_replace_once($search, $replace, $subject) {
	if (strpos($subject, $search) === false)
		return $subject;
	$strs = explode($search, $subject);
	$result = $strs[0] . $replace;
	for ($i = 1; $i < count($strs); $i++) {
		$result.= $strs[$i];
	}
	return $result;
}

function getTimeStamp() {
	$ts = time();
	return $ts;
}

function getName($name, $sname) {
	if (preg_match("/(.*)(@|\().*/u", $name, $matches))
		$name = $matches[2];
	if (mb_strlen($name) < 30)
		return $name;
	return $sname;
}

function getReturnCount($str) {
	preg_match_all("/\n/", $str, $matches);
	return count($matches[0]);
}

function transTimeStr($created_at) {	//mmddttmm
	$strs = preg_split('/[ :]/', $created_at);
	//    print_r($strs);
	$month = transMonthToNum($strs[1]);
	$day = $strs[2];
	$t = $strs[3];
	$m = $strs[4];
	$s = $strs[5];
	return $month . $day . $t . $m . $s;
}

function super_die($contents) {
	$num = 1;
	echo "<pre>";
	foreach ($contents as $key => $value) {
		echo $num . " :" . "[$key]" . PHP_EOL;
		if ($value === true)
			echo "true";
		elseif ($value === false)
			echo "false";
		elseif (empty($value))
			echo "empty";
		else {
			print_r($value);
		}
		echo PHP_EOL;
		echo PHP_EOL;
	}
	exit;
}

function is_sql($input) {
	$search = array("\\", "\0", "\n", "\r", "\x1a", "'", '"');
	$replace = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"');
	return str_replace($search, $replace, $input);
	//    return mysql_real_escape_string($input);
	//    return mysql_real_escape_string("%".$input."%");
	//    return "%".mysql_real_escape_string($input)."%";
}

function trimValueA($str) {
	preg_match("/<a[^>]*>([^>]*)<\/a>/", $str, $matches);
	return isset($matches[1]) ? $matches[1] : false;
}

function trimScreenNames($str) {
	preg_match_all("/@(\w+)/", $str, $matches);
	return isset($matches[1]) ? $matches[1] : false;
}

function shaveScreenNames($str) {
	$str = preg_replace("/@(\w+)/", '', $str);
	return $str;
}

function trimFaceChars($str) {
	preg_match_all(re_face, $str, $matches);
	if (!empty($matches))
		return isset($matches[0]) ? $matches[0] : false;
}

function popHashTags(&$str) {
	if (preg_match_all(re_hashtag, $str, $matches)) {
		$str = str_replace($matches[0], '', $str);
	}
	return isset($matches[0]) ? $matches[0] : false;
}

function h($str) {
	return htmlspecialchars($str);
}

function get_nico_smurls($keyword, $sort) {
	$html = file_get_contents("http://www.nicovideo.jp/search/{$keyword}?sort={$sort}");
//    echo "<textarea>".h($html)."</textarea>";
	preg_match_all('/title=\"(.*)\"\shref=\".*(sm\d+).*\"/', $html, $matches, PREG_SET_ORDER);

	$i = 0;
	foreach ($matches as $match) {
		$urls[$i]['name'] = $match[1];
		//$urls[$i]['url'] = "http://www.nicovideo.jp/watch/".$match[1];
		$urls[$i]['url'] = "nico.ms/" . $match[2];
		if ($i++ > 5)
			break;
	}

	return $urls;
}

function getGoogleSuggest($text) {
	// Google
	$url = 'http://www.google.com/complete/search?hl=ja&output=toolbar&qu=' . urlencode($text);
	$xml = file_get_contents($url);
	$xml = mb_convert_encoding($xml, 'UTF-8', 'Shift-JIS');
	$data = simplexml_load_string($xml);
	$datas = array();
	foreach ($data->CompleteSuggestion as $ele) {
		$at = (string) $ele->suggestion->attributes()->data;
		$datas[] = $at;
	}
	return $datas;
}

function getTimeMessage() {
	$data = array();
	$data[0] = array(
		"",
		"",
	);
	$data[1] = array(
		"",
		"",
	);
	$data[2] = array(
		"",
		"",
	);
	$data[3] = array(
		"",
		"",
	);
	$data[4] = array(
		"",
		"",
	);
	$data[5] = array(
		"",
		"",
	);
	$data[6] = array(
		"おはよう",
		"",
	);
	$data[7] = array(
		"おはよう",
		"",
	);
	$data[8] = array(
		"",
		"",
	);
	$data[9] = array(
		"",
		"",
	);
	$data[10] = array(
		"",
		"",
	);
	$data[11] = array(
		"",
		"",
	);
	$data[12] = array(
		"",
		"",
	);
	$data[13] = array(
		"",
		"",
	);
	$data[14] = array(
		"",
		"",
	);
	$data[15] = array(
		"",
		"",
	);
	$data[16] = array(
		"",
		"",
	);
	$data[17] = array(
		"",
		"",
	);
	$data[18] = array(
		"",
		"",
	);
	$data[19] = array(
		"",
		"",
	);
	$data[20] = array(
		"",
		"",
	);
	$data[21] = array(
		"",
		"",
	);
	$data[22] = array(
		"",
		"",
	);
	$data[23] = array(
		"",
		"",
	);
}

function createChainText($chain) {
	return ($chain <= 1 ? '' : "【{$chain}連続】");
}

function createRateTextFromPoint($point, $only = false) {
	$text_rate = "";
//		$point_t = $point;
	if ($point < 15) {
		$point = 1;
	} else {
		$point = sqrt(sqrt($point * 100)) / 2;
		if ($point < 1) {
			$point = 1;
		}
	}
	while ($point-- >= 1) {
		$text_rate.= ($only ? "[llll]" : "■");
	}
	$point_least = ($point + 1) * 6;
	while ($point_least -- >= 1) {
		$text_rate .= "l";
	}

	return $text_rate;
}

function createRateTextFromPointDay($point) {
	$text_rate = "";
//		$point_t = $point;
	if ($point < 15) {
		$point /= 15;
		$point ++;
	} else {
		$point = sqrt(sqrt($point * 100)) / 3;
		if ($point < 1) {
			$point = 1;
		}
	}
	while ($point-- >= 1) {
		$text_rate.= "★";
	}
	$point_least = ($point + 1) * 6;
	while ($point_least-- >= 1) {
		$text_rate .= "l";
	}

	return $text_rate;
}

function rateSpeed($a) {
	if ($a > 1000) {
		return "(:3っ)っ 三三三=ｰ";
	} else if ($a > 500) {
		return "(:3っ)っ 三三=ｰ";
	} else if ($a > 100) {
		return "(:3っ)っ ==-";
	} else if ($a > 50) {
		return "(:3っ)っ)))";
	}
	return "(:3っ)っ";
}

function rateLaugh($a) {
	$text = "( ﾟ∀ﾟ)";
	$i = 2;
	while ($a > 0) {
		$text .= "w";
		$a -= pow($i, 2);
		$i++;
	}
	return $text;
}

function rateSleep($a) {
	$text = "(|3[   ]";
	$i = 1;
	while ($a > 0) {
		$text .= "z";
		$a -= $i++;
	}
	return $text;
}

function convertArrayPointFormat($data) {
	$data_f = array();
	foreach ($data as $datum) {
		$data_f[$datum['text']] = $datum['count'];
	}
	return $data_f;
}

function decoratePanel($str) {
	return "('ω')o[{$str}]o";
}

function trimWord($word) {
	return preg_replace('/[ 　]+$/u', '', preg_replace('/^[ 　]+/u', '', trim($word)));
}

function numtodatehour($num) {
    $nums = str_split($num, 2);
    return $nums[0] . $nums[1] . '-' . $nums[2] . '-' . $nums[3] . ' ' . $nums[4] . ':00:00';
}

function datehourtonum($datehour) {
    return str_replace(array('-', ' '), array('', ''), substr($datehour, 0, 13));
}
