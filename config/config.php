<?php

define('DEBUG', FALSE);
define('ma_only'        , false);
define('ma_debug_tweet' , false);

define('url_site'      , 'http://www42.atpages.jp/elzzup0105/trend');
define('num_get_count' , 200);

define('db_table_name_1'   , 'trend');
define('db_table_name_2'   , 'trendCash');
define('db_table_name_3'   , 'trendCashAll');
define('db_table_name_spe' , 'SpecialWord');

define('tw_owner_name', "tdu_trend");
define('tw_list_name', "students");

define('mem_json_filename', 'mem.json');

// define('me_since_list'        , 'since_list');
// define('me_since_mention'     , 'since_mention');
// define('me_point_top'         , 'point_top');
// define('me_point_top_day'     , 'point_top_day');
// define('me_count_hour'        , 'count_hour');
// define('me_count_hour_p'      , 'count_hour_p');
// define('me_timestamp_tl'      , 'timestamp_tl');
// define('me_timestamp_post'    , 'timestamp_post');
// define('me_timestamp_postday' , 'timestamp_postday');
// define('me_mood_zzz'   , 'zzz');
// define('me_mood_ttt'   , 'ttt');
// define('me_mood_eee'   , 'eee');
// define('me_mood_speed' , 'speed');
// define('me_mood_pre'   , 'speed_pre');
// define('me_mood_wea'   , 'wea');
// define('me_mood_temp'  , 'temp');
// define('me_mood_train' , 'train');

define('wo_mood_zzz'   , '寝|おや|布団|ふとん|ねる|眠|ねむ');
define('wo_mood_www'   , '[wｗWＷ]');
define('wo_mood_train' , '電車|遅延');
define('wo_mood_wea_sun'   , 'wea');
define('wo_mood_wea_rain'   , 'wea');
define('wo_mood_wea_thunder'   , 'wea');
define('wo_mood_temp_up'  , '熱|あつ|あち');
define('wo_mood_temp_down'  , '寒|さむ|さみ');

define('ap_consumer_key'       , 'EQnvsFVdzMrXdkPRIW5fxl69r');
define('ap_consumer_secret'    , 'jUTQFH9X5hAGus89CZ1taZLSfTW4WxIAVxcLDZmZfqyY1lFGKe');
define('ap_access_token'       , '1112823384-nnLYhKUjhTAh2FadKzFrZ8X7uMDCVAGo68NSF1l');
define('ap_access_token_scret' , 'pCexCOv44x8R6flZwoaSrehDqQFT7TQ66wx9sNEpGWtMZ');


define('sp_words_ng', serialize(array('よりやら','かしら','ばかり','くらい','けれども','ところが','られる','られれ','られよ','させる','させれ','させよ','させろ','なかろ','なかっ','なけれ','たかろ','たかっ','たけれ','たがる','たがら','たがり','たがっ','たがれ','ましょ','ますれ','そうだ','そうだろ','そうだっ','そうで','そうに','そうなら','そうな','やがる','やがら','やがり','やがっ','やがれ','そうだ','そうで','らしい','らしかっ','らしく','らしけれ','べきだ','べきだろ','べきだっ','べきなら','ようだ','ようだろ','ようだっ','ようで','ように','ようなら','ような','でしょ')));
define('sp_words_ngt', serialize(array('から','よりやら','なり','だの','かしら','とも','ばかり','まで','だけ','ほど','くらい','など','なり','やら','こそ','でも','さえ','だに','けれども','ところが','のに','から','ので','れる','れれ','れろ','れよ','られる','られ','られれ','られよ','せる','せれ','せよ','せろ','させる','させ','させれ','させよ','させろ','ない','なかろ','なかっ','なく','なけれ','ぬん','よう','まい','たい','たかろ','たかっ','たけれ','たがる','たがら','たがり','たがっ','たがれ','たろ','だろ','たら','だら','ます','ませ','ましょ','まし','ます','ますれ','ませ','そうだ','そうだろ','そうだっ','そうで','そうに','そうなら','そうな','やがる','やがら','やがり','やがっ','やがれ','そうだ','そうで','らしい','らしかっ','らしく','らしけれ','べきだ','べきだろ','べきだっ','べき','べきなら','ようだ','ようだろ','ようだっ','ようで','ように','ようなら','ような','だろ','だっ','なら','です','でしょ','でし')));
define('sp_words_sign', serialize(array('、','。','，','．','・','：','；','？','！','゛','゜','´','｀','¨','＾','〇','‐','～','…','‥','‘','’','“','”','（','）','〔','〕','［','］','｛','｝','「','」','『','』','【','】','＋','－','×','÷','＝','￥','％','＃','＆','＊','＠','☆','★','○','●','◎','◇','◆','□','■','△','▲','▽','▼','※','〒','〓','≪','≫','◯','!','\"','\'','#','$','%','&','(',')','*','+',',','-','.','/',':',';','<','=','>','?','@','[','\\',']','^','|','{','}','_','~','゜','゛','　','～','＿','￣','⊂','⊃','∩','∨','∧','＜','＞')));
