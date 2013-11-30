<?php

//import headerOption
header('Content-type:text/html; charset=utf-8');
//require_once('../lib/class.image.php');
require_once('./lib/twitteroauth.php');
require_once('./lib/tiny_segmenter.php');
require_once('./lib/word.php');
mb_regex_encoding('UTF-8');

$min = date("i");
$hour=date("H");

//MySQL Connection

//Get SpecialWord
$NGwordSpecial=array();
$PrecedeWord=array();
$NGclients=array();

$endMentionID=0;
$endTweetID=0;

//Twitter Connection
$screen_name="tdu_trend";
//$screen_name="elzzup0105";
$list_name="students";
//$list_name="u";
$getCount="200";
$keys=array(
	'consumer_key'		=>"UqdDAdEEdpDGjFnCnqDzg",
	'consumer_secret'	=>"j1O8nap0XYR8dWJ65ELenQRVrkytarzaHWWEQE8w0",
	'oauth_token'		=>"1112823384-98fxU1cA4EFPmdm0Y8ZDlWwEbxGhSI8lBmYEpRJ",
	'oauth_token_secret'=>"o6tg4f7V44gBtUlrAxdOts8SyoOerm6Kw4NJxDgk",
);
$connection = new TwitterOAuth($keys['consumer_key'], $keys['consumer_secret'],$keys['oauth_token'], $keys['oauth_token_secret']);
$json=json_decode($connection);
//var_dump($connection);
print "connection ok<br>";


$cursor='1425128228788857939';
$t=0;
while($t<100){
	$url="https://api.twitter.com/1.1/lists/members.json";
	$parameters = array(
	    'owner_screen_name' => "tahiro_1986jpn",
	    'slug' => "北千住関係",
	    'skip_status' => true,
	    'cursor' =>$cursor,
	);
	$data = $connection->OAuthRequest($url, "GET", $parameters);
	$isFollow=json_decode($data);
//	var_dump($isFollow);
	$t++;
	foreach($isFollow->users as $user){
		echo "<br>".$user->screen_name;
		$url="https://api.twitter.com/1.1/lists/members/create.json";
		$parameters = array(
		    'owner_screen_name' => $screen_name,
		    'slug' => $list_name,
		    'user_id' => $user->id,
		    
		);
		$data = $connection->OAuthRequest($url, "POST", $parameters);
		$result=json_decode($data);
		if(isset($result->errors))echo "●";
		else echo "○";

//		var_dump(json_decode($data));
		$t++;
	}
	$cursor=$isFollow->next_cursor;
	echo "<br>【".$cursor."】<br>";
//	break;
}




?>