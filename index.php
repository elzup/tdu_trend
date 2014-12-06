<?php

/* composer modules */
require_once('./vendor/autoload.php');

require_once './lib/tiny_segmenter.php';

require_once './config/config.php';
require_once './config/constants.php';
require_once './config/keys.php';

/* include controllers */
require_once('./controllers/page_controller.php');
require_once('./controllers/job_controller.php');
require_once('./controllers/trend_manager.php');
/* include classes */
require_once('./models/trend_model.php');
require_once('./models/twitter_model.php');
require_once('./models/twitter_model_trend.php');

require_once('./classes/memory.php');
require_once('./classes/tweet.php');
require_once('./helpers/function.php');

$app = new \Slim\Slim(array(
    'debug'              => true,
    'log.level'          => \Slim\Log::DEBUG,
    'log.enabled'        => true,
    'cookies.encrypt'    => true,    //cookie
));

$app->get('/', '\PageController:showIndex');

$app->get('/job/', '\JobController:walk_tl');
$app->get('/job/h', '\JobController:tweet_hour');
$app->get('/job/d', '\JobController:tweet_day');

$app->get('/job/r/:word', function($word) {
	(new JobController()).regist_word($word);
});

$app->get("/info", function() {
    phpinfo();
})->name("info");


$app->run();
