<?php

define('ENV_DEVELOP', 'development');
define('ENV_PRODUCT', 'production');
if (file_exists('./env')) {
	define('ENV', ENV_PRODUCT);
} else {
	define('ENV', ENV_DEVELOP);
}

define('DEBUG', TRUE);

define('ma_only', false);
define('ma_debug_tweet', false);

define('num_get_count', 200);
define('tw_owner_name', "tdu_trend");
define('tw_list_name', "students");

define('mem_json_filename', 'data/mem.json');

define('wo_mood_zzz', '寝|おや|布団|ふとん|ねる|眠|ねむ');
define('wo_mood_www', '[wｗWＷ]');
define('wo_mood_train', '電車|遅延');
define('wo_mood_wea_sun', 'wea');
define('wo_mood_wea_rain', 'wea');
define('wo_mood_wea_thunder', 'wea');
define('wo_mood_temp_up', '熱|あつ|あち');
define('wo_mood_temp_down', '寒|さむ|さみ');

define('sp_words_ng', serialize(array('よりやら', 'かしら', 'ばかり', 'くらい', 'けれども', 'ところが', 'られる', 'られれ', 'られよ', 'させる', 'させれ', 'させよ', 'させろ', 'なかろ', 'なかっ', 'なけれ', 'たかろ', 'たかっ', 'たけれ', 'たがる', 'たがら', 'たがり', 'たがっ', 'たがれ', 'ましょ', 'ますれ', 'そうだ', 'そうだろ', 'そうだっ', 'そうで', 'そうに', 'そうなら', 'そうな', 'やがる', 'やがら', 'やがり', 'やがっ', 'やがれ', 'そうだ', 'そうで', 'らしい', 'らしかっ', 'らしく', 'らしけれ', 'べきだ', 'べきだろ', 'べきだっ', 'べきなら', 'ようだ', 'ようだろ', 'ようだっ', 'ようで', 'ように', 'ようなら', 'ような', 'でしょ')));
define('sp_words_ngt', serialize(array('から', 'よりやら', 'なり', 'だの', 'かしら', 'とも', 'ばかり', 'まで', 'だけ', 'ほど', 'くらい', 'など', 'なり', 'やら', 'こそ', 'でも', 'さえ', 'だに', 'けれども', 'ところが', 'のに', 'から', 'ので', 'れる', 'れれ', 'れろ', 'れよ', 'られる', 'られ', 'られれ', 'られよ', 'せる', 'せれ', 'せよ', 'せろ', 'させる', 'させ', 'させれ', 'させよ', 'させろ', 'ない', 'なかろ', 'なかっ', 'なく', 'なけれ', 'ぬん', 'よう', 'まい', 'たい', 'たかろ', 'たかっ', 'たけれ', 'たがる', 'たがら', 'たがり', 'たがっ', 'たがれ', 'たろ', 'だろ', 'たら', 'だら', 'ます', 'ませ', 'ましょ', 'まし', 'ます', 'ますれ', 'ませ', 'そうだ', 'そうだろ', 'そうだっ', 'そうで', 'そうに', 'そうなら', 'そうな', 'やがる', 'やがら', 'やがり', 'やがっ', 'やがれ', 'そうだ', 'そうで', 'らしい', 'らしかっ', 'らしく', 'らしけれ', 'べきだ', 'べきだろ', 'べきだっ', 'べき', 'べきなら', 'ようだ', 'ようだろ', 'ようだっ', 'ようで', 'ように', 'ようなら', 'ような', 'だろ', 'だっ', 'なら', 'です', 'でしょ', 'でし')));
define('sp_words_sign', serialize(array('、', '。', '，', '．', '・', '：', '；', '？', '！', '゛', '゜', '´', '｀', '¨', '＾', '〇', '‐', '～', '…', '‥', '‘', '’', '“', '”', '（', '）', '〔', '〕', '［', '］', '｛', '｝', '「', '」', '『', '』', '【', '】', '＋', '－', '×', '÷', '＝', '￥', '％', '＃', '＆', '＊', '＠', '☆', '★', '○', '●', '◎', '◇', '◆', '□', '■', '△', '▲', '▽', '▼', '※', '〒', '〓', '≪', '≫', '◯', '!', '\"', '\'', '#', '$', '%', '&', '(', ')', '*', '+', ',', '-', '.', '/', ':', ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^', '|', '{', '}', '_', '~', '゜', '゛', '　', '～', '＿', '￣', '⊂', '⊃', '∩', '∨', '∧', '＜', '＞')));

# must issue a "use" statement in your closure if passing variables
ActiveRecord\Config::initialize(function($cfg) {
	$cfg->set_model_directory('/path/to/your/model_directory');
	$cfg->set_connections(array('main' => DB_CONNECTION));

	# default connection is now production
	$cfg->set_default_connection('main');
});
