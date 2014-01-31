<?php


ini_set('display_errors', "1");
error_reporting(E_ALL);



//require_once('../lib/class.image.php');
require_once('./lib/twitteroauth.php');
require_once('./lib/tiny_segmenter.php');

require_once('./config.php');
require_once('./c_DB.php');
require_once('./TwitterManager.php');
require_once('./TwitterManagerTrend.php');
require_once('./function.php');


echo "<pre>";
echo "【start】".PHP_EOL;

setupEncodeing();
setDB();

$connection = new TwitterOAuth(ap_consumer_key, ap_consumer_secret, ap_access_token, ap_access_token_scret);
print_r($connection);

$trend_manager = new TwitterManagerTrend($connection, tw_owner_name, tw_list_name, mem_json_filename);
//$manager->_initializeMemFile();

echo $trend_manager->getLastTimestamp();
$trend_manager->manage(!!debug('do'));


if (debug('doh'))
    $trend_manager->manageTrendHour();
if (debug('dod'))
    $trend_manager->manageTrendDay();
if (debug('tl')) {
    $data->text = "風";
    $tw = new Tweet2($data);
    $trend_manager->manageReplay3($tw, true);

}

if (debug('post')) {
    $trend_manager->tweetTrend(array("this" => 1, "is" => 200, "Debug" => 2));
//    $trend_manager->tweetTrendDay(array("this" => 1, "is" => 10, "Debug" => 2));
}

//$trend_manager->_memoryMoodInitialize();

echo "【end】";


?>