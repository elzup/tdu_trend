<?php

/* composer modules */
require_once('./vendor/autoload.php');

/* include controllers */
require_once('./c/page_controller.php');
require_once('./c/job_controller.php');
require_once('./c/trend_manager.php');
/* include classes */
require_once('./m/trend_model.php');
require_once('./m/twitter_model_trend.php');

$app = new \Slim\Slim(array(
    'debug'              => true,
    'log.level'          => \Slim\Log::DEBUG,
    'log.enabled'        => true,
    'cookies.encrypt'    => true,    //cookie
));

$app->get('/', '\PageController:showIndex');

$app->get('/job/log', '\JobController:log_tl');

$app->get("/info", function() use ($app){
    phpinfo();
})->name("info");


$app->run();
