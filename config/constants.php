<?php

// variables, formats
define('LOAD_NUM', 50);
define('TOP_LIMIT', 40);

define('TREND_HOUR_WORD_NUM', 6);
define('TREND_DAY_WORD_NUM', 6);

define('TREND_HOUR_CARD_NUM_VIEW', 4);
define('TREND_HOUR_WORD_NUM_VIEW', 6);
define('TREND_HOUR_WORD_NUM_VIEW_DESC', 20);

define('DB_TN_PREFIX', 'tt_');

define('FORMAT_RANKS_TITLE_DATEHOUR', 'Y年m月d日 H時');
define('FORMAT_RANKS_TITLE_DATE', 'Y年m月d日');

// db constants
define('DB_TN_CACHES', DB_TN_PREFIX . 'caches');
define('DB_CN_CACHES_WORD', 'word');
define('DB_CN_CACHES_TWITTER_ID', 'twitter_id');
define('DB_CN_CACHES_TIMESTAMP', 'timestamp');

define('DB_TN_LOGS', DB_TN_PREFIX . 'logs');
define('DB_CN_LOGS_ID', 'log_id');
define('DB_CN_LOGS_WORD', 'word');
define('DB_CN_LOGS_POINT', 'point');
define('DB_CN_LOGS_DATEHOUR', 'datehour');

define('DB_TN_MEMORYS', DB_TN_PREFIX . 'memorys');
define('DB_CN_MEMORYS_WORD', 'word');
define('DB_CN_MEMORYS_COUNT', 'count');
define('DB_CN_MEMORYS_DATE', 'date');

define('DB_TN_SPECIALS', DB_TN_PREFIX . 'specials');
define('DB_CN_SPECIALS_WORD', 'word');
define('DB_CN_SPECIALS_TYPE', 'type');

define('MYSQL_TIMESTAMP', 'Y-m-d H:i:s');
define('MYSQL_TIMESTAMP_DATE', 'Y-m-d');
define('MYSQL_TIMESTAMP_DATEHOUR', 'Y-m-d H:00:00');

// url rootings
if (ENV == ENVIRONMENT_DEV) {
    define('SITE_ROOT', 'http://localhost/');
//    define('SITE_ROOT', 'http://' . $_SERVER['SERVER_ADDR'] . '/tdu_trend/');
} else {
    define('SITE_ROOT', 'http://trend.elzup.com/');
}
define('URL_LIB_MATERIALIZE', SITE_ROOT . 'bower_components/materialize/');
define('URL_LIB_MATERIALIZE_JS', URL_LIB_MATERIALIZE . 'bin/materialize.js');
define('URL_LIB_JQUERY', SITE_ROOT . 'bower_components/jquery/dist/jquery.min.js');
//define('URL_LIB_BG', SITE_ROOT . 'lib/jquery.backgroundpos.min.js');
define('URL_JS', SITE_ROOT . 'js/');
define('URL_JS_SCRIPT', URL_JS . 'script.js');

define('URL_LOG', 'log/');
define('URL_REGIST', 'regist/');
define('URL_REGIST_POST', URL_REGIST . 'post/');
define('URL_HELP', 'help/');

