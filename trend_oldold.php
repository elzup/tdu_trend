<?php

if(0<= $min%10 && $min%10 < 5 || $debug['a']){

    $sql="SELECT word,type FROM SpecialWord";
    echo $sql;
    $result=mysql_query($sql);
    while ($row = mysql_fetch_assoc($result)) {
        //    print('<p>');
        //    print('word='.$row['word']);
        //    print(',type='.$row['type']);
        //    print('</p>');
        if($row['type']=='f'){
            array_push($NGwordSpecial,$row['word']);

        }else if($row['type']=='p'){
            array_push($PrecedeWord,$row['word']);

        }else if($row['type']=='c'){
            array_push($NGclients,$row['word']);

        }else if($row['type']=='e'){
            $endMentionID=$row['word'];

        }else if($row['type']=='t'){
            $topCount =$row['word'];

        }else if($row['type']=='n'){
            $hourTweetNum=$row['word'];

        }else if($row['type']=='m'){
            $hourTweetNumP=$row['word'];

            //		if($bool)$endMentionID=0;
        }else if($row['type']=='l'){
            $endTweetID=$row['word'];

            //		if($bool)$endTweetID=0;
        }
    }
    echo "<br><p>【【 SpecialWords 】】<p>\n<br>";

    echo "<br>NGWord";
    var_dump($NGwordSpecial);
    echo "<br><br>PrecedeWord";
    var_dump($PrecedeWord);
    echo "<br><br>NGclients";
    var_dump($NGclients);
    $NGword=array_merge($NGwordSpecial,$NGword);
    echo "【".$endMentionID."】【".$endTweetID."】";
    echo "<br><br>";

    //Twitter Connection
    $screen_name="tdu_trend";
    //$screen_name="elzzup0105";
    $list_name="students";
    //$list_name="u";
    $getCount="200";

    //var_dump($connection);


    //Segmener
    $segmenter=new TinySegmenterarray();



    //Get List
    $url="https://api.twitter.com/1.1/lists/statuses.json";
    $parameters = array(
            'owner_screen_name' => $screen_name,
            'slug' => $list_name,
            'count' => $getCount,
            'since_id'=>$endTweetID,
    );
    print_r($parameters);
    if (method_exists($connection, "OAuthRequest"))
        $data = $connection->OAuthRequest($url, "GET", $parameters);
    else die("connection is none");
    $lists=json_decode($data);

    $countWords=array();
    $pointWords=array();
    $tweet_owner_name = "";

    $hourTweetNum+=count($lists);

    echo "<br><p>【【 getTimeline 】】<p>\n<br>";
    foreach($lists as $tweet){
        $text=$tweet->text;
        $tweet_owner_name=$tweet->user->screen_name;
        echo "【【【".mb_detect_encoding($text);
        echo $text."<br>■";
        $pat = '/(https?|ftp?|http)(:\/\/[[:alnum:]\+\$\;\?\.%,!#~*\/:@&=_-]+)/';
        $text = preg_replace($pat, "", $text);		//url delete
        $tweetID=$tweet->id_str;
        if($endTweetID<$tweetID){
            $endTweetID=$tweetID;
        }
        echo $tweetID;


        preg_match('/\>(.*)\</is', $tweet->source, $matches);
        $cliant=$matches[0];
        echo "cliant: ".$cliant;
        $c=false;
        foreach($NGclients as $check){
            if(strpos($cliant,$check)!=false){
                $c=true;
                break;
            }
        }
        if($c){
            echo "<font color=\"#f00\"> NG!! </font>";
            continue;
        }

        if(strpos($text,'TDU_trend')!=false){
            echo "mineError";
            continue;
        }


        $textCountWords=array();

        //	$nameStr="@[a-zA-Z0-9_.-]*/u";
        //	$text=preg_replace($nameStr,' ',$text);		//screen_name delete
        $nameStr="/(?<![0-9a-zA-Z'\"#@=:;])@([0-9a-zA-Z_]{1,15})/u";
        $text=preg_replace($nameStr,'',$text);		//screen_name delete
        //	$ptext=preg_replace($nameStr,"<font color=\"#fa0\">@\\1</font>",$ptext);

        if(preg_match_all('/[(（][^()（）]+[)）]/iu', $text, $matches)){
            echo "FaceHit■■■■■■■■■■■■■■■■■";
            print_r($matches);
            foreach($matches[0] as $wordp){
                echo $wordp;
                addWord($wordp);
                array_push($textCountWords,$wordp);
            }
        }

        //	$hashStr="#[a-zA-Z0-9_.-]*";
        if (preg_match_all('/(?:^|[^ｦ-ﾟー゛゜々ヾヽぁ-ヶ一-龠ａ-ｚＡ-Ｚ０-９a-zA-Z0-9&_\/]+)[#＃]([ｦ-ﾟー゛゜々ヾヽぁ-ヶ一-龠ａ-ｚＡ-Ｚ０-９a-zA-Z0-9_]*[ｦ-ﾟー゛゜々ヾヽぁ-ヶ一-龠ａ-ｚＡ-Ｚ０-９a-zA-Z]+[ｦ-ﾟー゛゜々ヾヽぁ-ヶ一-龠ａ-ｚＡ-Ｚ０-９a-zA-Z0-9_]*)/u', $text, $matches)){
            for($k=0;isset($matches[1][$k]);$k++){
                $hash="#".$matches[1][$k];
                $text=preg_replace($hash,'',$text);
                $hasht=$hash;
                if(strlen(str_replace($NGword,'',$hasht))<2)break;
                addWord($hash);
                array_push($textCountWords,$hash);
                echo "<font color=\"#0a0\">/#".$matches[1][$k]."/</font>";
            }
        }
        //        $text=str_replace($hashStr,'',$text);		//screen_name delete

        foreach($PrecedeWord as $check){
            if($check == "")continue;
            $result=strpos($text,$check);
            if($result===false)continue;
            addWord($check);
            array_push($textCountWords,$check);
            echo "p/$check/";
        }

        $result=$segmenter->segment($text,'UTF-8');
        foreach($result as $word){
            //		$count = ($word =~ s/\p{Hiragana}/$&/g);
            $word=trim($word);
            //		$word=str_replace('/\[.+?\]/','',$word);
            $word=str_replace($NGsign,'',$word);
            $word=str_replace($NGword,'',$word);
            if($word=="")continue;
            $strCount=strlen($word);
            $mbCount=mb_strlen($word);
            if($strCount == $mbCount){
                if($strCount<3)continue;
            }
            else if($word == "´")continue;
            else{
                if (preg_match("/^[ぁ-んー]+$/u", $word)) {
                    if($strCount<7)continue;
                }
                if(!(preg_match('/[^一-龠]/u',$word))){
                    if($strCount<4)continue;
                }
            }
            //		echo $word."/";
            //		echo $word."<".$count.">/";
            $sameFlag=false;
            foreach($textCountWords as $mark){
                if($mark==$word){
                    $sameFlag=true;
                    break;
                }
            }
            if($sameFlag)continue;
            else array_push($textCountWords,$word);
            addWord($word);
        }
        echo "<br><br>";
    }


    //Sort Data
    asort($countWords);
    foreach($countWords as $key=>$val){
        echo "$key = $val :: ";
        $sql="INSERT INTO trend(text, count) VALUES('$key', '$val') ON DUPLICATE KEY UPDATE count = count + $val";
        $result=mysql_query($sql);
    }

}



if(0<=$min && $min<5){
    $sql = "DELETE FROM SpecialWord WHERE type = 'm'";
    $result=mysql_query($sql);
    $sql="INSERT INTO SpecialWord(word,type)VALUES('$hourTweetNum', 'm')";
    $result=mysql_query($sql);
    $sql = "DELETE FROM SpecialWord WHERE type = 'n'";
    $result=mysql_query($sql);
    $sql="INSERT INTO SpecialWord(word,type)VALUES('0', 'n')";
    $result=mysql_query($sql);
}
else{
    $sql = "DELETE FROM SpecialWord WHERE type = 'n'";
    $result=mysql_query($sql);
    $sql="INSERT INTO SpecialWord(word,type)VALUES('$hourTweetNum', 'n')";
    $result=mysql_query($sql);
}


//Get Mention
$url="https://api.twitter.com/1.1/statuses/mentions_timeline.json";
$parameters = array(
        'since_id'=>$endMentionID,
);

$data = $connection->OAuthRequest($url, "GET", $parameters);
$mentions=json_decode($data);
echo "<br><p>【【 mentions 】】<p>\n<br>";

print_r($mentions);

foreach($mentions as $tweet){
    print_r($tweet);
    echo "1<br>";
    $mention_owner_name=$tweet->user->screen_name;
    $mention_text=$tweet->text;
    $mention_id=$tweet->id_str;
    if($endMentionID< $mention_id){
        $endMentionID=$mention_id;
    }
    if(!(isset($mention_id))){
        echo "error";
        continue;
    }

    echo "2";

    //	if(strpos($tweet->source,$NGclients)===false)continue;

    //	echo "【".$endMentionID."】<br>";
    //	echo "【".$mention_id."】";

    echo "<br>$mention_owner_name";
    $message_text="@$mention_owner_name ";						//debugSet
    if($mention_owner_name=="elzzup0105" || $mention_owner_name=="Elzzup0105"){
        echo "3";
        echo $mention_text;
        if (preg_match('/NG\((.*)\)/is', $mention_text, $matches)){
            echo "<br><p>【【 add NG from Elzzup 】】<p>\n<br>";
            $pieces   = explode(",", $matches[1]);
            var_dump($pieces);
            foreach($pieces as $pieces_input){
                $sql="REPLACE INTO SpecialWord(word,type)VALUES('$pieces_input', 'f')";
                echo "$sql <br>";
                $result=mysql_query($sql);
                $sql = "DELETE FROM trend WHERE text = '$pieces_input'";
                echo "$sql <br>";
                $result=mysql_query($sql);
                $message_text.="('ω' )b　NG:".$pieces_input;
            }
        }
    }

    $url="https://api.twitter.com/1.1/lists/members/show.json";
    $parameters = array(
            'owner_screen_name' => $screen_name,
            'slug' => $list_name,
            'screen_name' => $mention_owner_name,
            'skip_status' => true
    );
    $data = $connection->OAuthRequest($url, "GET", $parameters);
    $isFollow=json_decode($data);
    //	var_dump($isFollow);
    echo"4<br>";
    if($isFollow->following)
    {
        if (preg_match('/テスト[^(（]*(\s)?[(（](.*)[)）]/iu', $mention_text, $matches))
        {
            echo"5<br>";
            $pieces   = explode(",", $matches[2]);
            var_dump($pieces);
            foreach($pieces as $pieces_input)
            {
                var_dump($pieces_input);
                if(strpos($pieces_input,"テストに出るよ")){
                    $message_text.="('ω')o[あかん]o";
                    break;
                }else if(strpos($pieces_input,"@")){
                    $message_text.="('ω')o[@はだめ]o";
                    break;
                }
                $strCount=strlen($pieces_input);
                if($strCount>30){
                    $message_text.="('ω')o[なげぇよ]o";
                    break;
                }
                $mbCount=mb_strlen($pieces_input);
                //                $outFlag=false;
                //                 if($strCount == $mbCount){
                //                     if($strCount<3)$outFlag=true;
                //                 }
                //                 if (preg_match("/^[ぁ-んー]+$/u", $pieces_input)) {
                //                     if($strCount<7)$outFlag=true;
                //                 }
                //                 if($outFlag){
                //                     $message_text.="".$pieces_input."むずかしくて おぼえられなかった！\n";
                //                 }else{
                $message_text.="".$pieces_input."をおぼえた！\n";
                //                 }
                $sql="REPLACE INTO SpecialWord(word,type)VALUES('$pieces_input', 'p')";
                echo $sql;
                $result=mysql_query($sql);
                $message_text.=$mention_owner_name."は\n".levelUp();
            }
        }
        else if (preg_match('/風.*[強騒つさ]/is', $mention_text, $matches))
        {
            $phour=$hour-1;
            if($phour<0)$phour+=24;
            $message_text.="\n";
            $message_text.=$phour."じ　".$hourTweetNumP.".000 pph\n";
            $omake=revSpeed($hourTweetNumP);
            $message_text.=$omake."\n";

            if(0<=$min && $min<5){
            }else{

                $pph=round($hourTweetNum*60 / (date(i)+1),3);
                $message_text.=$hour."じ　$pph pph";
                $message_text.="($hourTweetNum)\n";
                $omake="";
                //			$str="&#9602;&#9605;&#9607;&#9608;&#9619;&#9618;&#9617;(’ω’)&#9617;&#9618;&#9619;&#9608;&#9607;&#9605;&#9602; ";
                //			$uwaa=htmlspecialchars($str,ENT_QUOTES,"UTF-8");
                $omake=revSpeed($pph);
                $message_text.=$omake;
            }
        }else if(preg_match('/ちぇんじ.*(\s)?\((.*)\)/is', $mention_text, $matches)){
            $mtext=$matches[2];
            echo $mtext.B;
            var_dump($matches);

            $mtext=str_replace_one("janken",$mtext);
            $mtext=str_replace_one("color",$mtext);
            $mtext=str_replace_one("c",$mtext);
            $mtext=str_replace_one("tramp",$mtext);
            echo $mtext;
            while(preg_match("/\{(\d+)-(\d+)\}/is",$mtext,$matches)){
                $min=$matches[1];
                $max=$matches[2];
                if($min>$max){
                    $tenpc=$min;$min=$max;$max=$tenpc;
                }

                $strs=explode($matches[0],$mtext);
                $result=$strs[0];
                for($i=1;$i<count($strs);$i++){
                    $randNum=rand($min,$max);
                    $result.=$randNum.$strs[$i];

                    //          $result.=$replace[$i].$strs[$i];
                }
                $mtext=$result;
                //   $mtext=str_replace($matches[0],$randNum,$mtext);
            }
            echo $mtext;
            $message_text.=$mtext;
        }
        else if (preg_match('/(にこ|ニコ|25).*(\s)?[(（](.*)[)）]/ui', $mention_text, $matches))
        {
            $message_text.="\n　v\n[ 'ω']っ\n";
            //            $pieces   = explode(",", $matches[3]);
            $keyword = $matches[3];

            $resultn = get_nico_smurls($keyword,"n");
            $resultm = get_nico_smurls($keyword,"m");

            $ra = rand(0, 5);
            $text1 = $resultn[$ra]['url']." ".mb_substr($resultn[$ra]['name'],0,10)."…";
            $message_text.= $text1."\n";
            $rb = rand(0, 5);
            if($resultn[$ra]['url'] != $resultm[$rb]['url']){
                $text2 = $resultm[$rb]['url']." ".mb_substr($resultm[$rb]['name'],0,10)."…";
                $message_text.=$text2;
            }
        }
        else if(preg_match('/NG\((.*)\)/is', $mention_text, $matches) && ($mention_owner_name != "Elzzup0105" && $mention_owner_name != "arzzup")) {
            $message_text.="('ω' )<Only Elzzup!";
        }else if (preg_match('/\[(.*)\]/is', $mention_text, $matches)){
            echo "○trender";
            $pieces   = explode(",", $matches[1]);
            var_dump($pieces);
            $points=array();
            $message_text.="\n";
            echo "afterGT\n";
            $points=getTrend($pieces);
            var_dump($points);
            for($o=0;$o<count($pieces);$o++){
                $message_text.="( 'ω'o[".$pieces[$o]."]o<".$points[$o]."点\n";
            }
            echo "\n$message_text\n";
        }else{
            $message_text.="( 'ω')";
        }

        echo strlen($message_text).": ".$message_text;

        if(strlen($message_text)>140){
            $message_text=substr($message_text,0,140);
        }
        $url="https://api.twitter.com/1.1/statuses/update.json";
        $parameters = array(
                'status' => $message_text,
                'in_reply_to_status_id' => $endMentionID,
        );
        $data = $connection->OAuthRequest($url, "POST", $parameters);	//debugSet
        $result=json_decode($data);
        var_dump($result);
    }
    else if(!$isFollow->following){
        $url="https://api.twitter.com/1.1/lists/members/create.json";
        $parameters = array(
                'owner_screen_name' => $screen_name,
                'slug' => $list_name,
                'screen_name' => $mention_owner_name,

        );
        $data = $connection->OAuthRequest($url, "POST", $parameters);

        $message_text="@".$mention_owner_name.' TDUリストに追加しました⊂(\'ω\'⊂ )';
        $url="https://api.twitter.com/1.1/statuses/update.json";
        $parameters = array(
                'status' => $message_text,
                'in_reply_to_status_id' => $endMentionID,
        );
        echo $massage_text;
        $data = $connection->OAuthRequest($url, "POST", $parameters);
    }
    $sql = "DELETE FROM SpecialWord WHERE type = 'e'";
    $result=mysql_query($sql);
    $sql="INSERT INTO SpecialWord(word,type)VALUES('$endMentionID', 'e')";
    $result=mysql_query($sql);

    echo"<br><br><br>";
}


if($endMentionID==$endTweetID)$endTweetID++;
echo "【".$endMentionID."】【".$endTweetID."】";

$sql = "DELETE FROM SpecialWord WHERE type = 'l'";
$result=mysql_query($sql);
$sql="INSERT INTO SpecialWord(word,type)VALUES('$endTweetID', 'l')";
$result=mysql_query($sql);





if((0<=$min && $min<5) || $dool){
    trendTweet();
    if($hour=="00" || $dool){
        trendTweetDays();
    }
    //	refollow();
}

//refollow();


///test
//$url="https://api.twitter.com/1.1/statuses/update.json";
//$parameters = array(
//    'status' => "@elzzup0105 \ntest\nTest",
//);
//$data = $connection->OAuthRequest($url, "POST", $parameters);
//$result=json_decode($data);



//function countWords Update
function addWord($str)
{
    global $countWords;
    global $pointWords;
    global $tweet_owner_name;

    if(isset($countWords[$str]))
    {
        $f = false;
        foreach($pointWords[$str] as $ton){
            if($ton == $tweet_owner_name){
                $f = true;
            }
        }
        if(!$f){
            $cc = count($pointWords[$str]) + 1;
            $pointWords[$str][$cc] = $tweet_owner_name;
            $countWords[$str] += $cc;
        }
    }
    else
    {
        //		echo "$str";
        $addArray=array($str => 1);
        //		var_dump($addArray);
        //		$countWords=$countWords+$addArray;
        $countWords=array_merge($countWords,$addArray);
    }
    echo $str."/";
}


if(!empty($debug['b'])){
    trendTweet();
}
function trendTweet(){
    echo "<br><p>【【 trendPost 】】<p>\n<br>";
    global $connection;
    $trendData=array();
    $sql="SELECT text,count FROM trend";
    $result=mysql_query($sql);
    while ($row = mysql_fetch_assoc($result)) {
        $textR=$row['text'];
        $sql="SELECT text,count FROM trendCashAll WHERE `text` LIKE '$textR'";
        $resultB=mysql_query($sql);
        $rowB=mysql_fetch_assoc($resultB);
        $minus=0;
        if(isset($rowB['count']))$minus=$rowB['count']/100;
        $row['count']-=$minus;
        $addArray=array($row['text'] => $row['count']);
        $trendData=array_merge($trendData, $addArray);
    }
    echo "<pre>dumps<br><br>";
    arsort($trendData);
    print_r($trendData);
    /*
     foreach($trendData as $word=>$value){
    if($value == 0)continue;
    foreach ($trendData as $word2=> $value2){
    if($word2 == $word || $value2 == 0)continue;
    if(!(strpos($word2 , $word) === false)){
    if($value > $value2){
    $trendData[$word] += $value2;
    $trendData[$word2] = 0;
    }else {
    $trendData[$word2] += $value;
    $trendData[$word] = 0;
    }
    }else if(!(strpos($word , $word2) === false)){
    if($value2 > $value){
    $trendData[$word] += $value2;
    $trendData[$word2] = 0;
    }else {
    $trendData[$word2] += $value;
    $trendData[$word] = 0;
    }
    }
    }
    }
    */
    //    arsort($trendData);
    echo "dumps2";
    print_r($trendData);
    global $debug;

    if(!empty($debug['b'])){
        exit;
        return;
    }
    $c=0;
    //	$cashDB=array();
    $tweetText="";
    foreach($trendData as $word=>$value){
        $temp="<br> 【".++$c."】".$word." = ".$value;
        if($c<7){
            $rev=rev($value);
            //			$tweetText.=$word."【".$value."】"\n";
            $tweetText.=$word.$rev."\n";
        }
        echo $temp;

        $sql="INSERT INTO trendCash(text, count) VALUES('$word', '$value') ON DUPLICATE KEY UPDATE count = count + $value";
        $result=mysql_query($sql);
        echo "$sql <br>";

        if($c>19)break;
    }
    echo "<pre>";
    print_r($tweetText);

    $url="https://api.twitter.com/1.1/statuses/update.json";
    $parameters = array(
            'status' => $tweetText,
    );
    $data = $connection->OAuthRequest($url, "POST", $parameters);
    $result=json_decode($data);
    //	var_dump($result);


    $sql="DELETE FROM trend";
    $result=mysql_query($sql);
}


//refollow();
function refollow(){
    echo "<br><p>【【 Refollow 】】<p>\n<br>";
    global $connection;
    $url="https://api.twitter.com/1.1/followers/ids.json";
    $parameters = array(
    );
    $data = $connection->OAuthRequest($url, "GET", $parameters);
    $result=json_decode($data);
    var_dump($result);
    $f=0;
    foreach($result->ids as $incoming){
        $url="https://api.twitter.com/1.1/friendships/lookup.json";
        $parameters = array(
                'user_id' => $incoming,
        );
        $data = $connection->OAuthRequest($url, "GET", $parameters);
        $result=json_decode($data);

        $flagb=false;
        var_dump($result);
        echo "<br>";
        echo $result->connections->connection[0];
        echo $result->connections[0];
        echo $result->connection[0];
        echo $result->name;
        echo $result->connections->following;


        foreach($result->connections->connection as $followF){
            echo "check";
            echo $followF;
            if($followF=="following"){
                $flagb=true;
                echo "skip<br>";
                break;
            }
        }
        if($flagb)continue;
        else echo "sent<br>";


        if(isset($result->errors))return;

        echo "<br>$incoming <br>";
        $url="https://api.twitter.com/1.1/friendships/create.json";
        $parameters = array(
                'user_id' => $incoming,
        );
        //		$data = $connection->OAuthRequest($url, "POST", $parameters);
        $result=json_decode($data);
        if($f++>4)break;
    }
}


function rev($num){
    $num=(int)$num;
    global $topCount;
    $top = (int)$topCount;
    if($num > $top){
        $topCount = $num;
        $sql = "DELETE FROM SpecialWord WHERE type = 't'";
        $result=mysql_query($sql);
        $sql="INSERT INTO SpecialWord(word,type)VALUES('$num', 't')";
        $result=mysql_query($sql);
        return " ■■■■■ ☆new Record!!$num P";
    }
    if($num<10){
        return " ■";
    }
    if($num<20){
        return " ■■";
    }
    if($num<50){
        return " ■■■";
    }
    if($num<100){
        return " ■■■■";
    }
    return "■■■■■";
}
function revD($num){
    $num=(int)$num;
    if($num<10){
        return " ★";
    }
    if($num<30){
        return " ★★";
    }
    if($num<50){
        return " ★★★";
    }
    if($num<100){
        return " ★★★★";
    }
    if($num<1000){
        return " ★★★★★";
    }
    return "";
}


function trendTweetDays(){
    echo "<br><p>【【 trendPostAll 】】<p>\n<br>";
    global $connection;
    $trendData=array();
    $sql="SELECT text,count FROM trendCash";
    $result=mysql_query($sql);
    while ($row = mysql_fetch_assoc($result)) {
        $addArray=array($row['text'] => $row['count']);
        $trendData=array_merge($trendData,$addArray);
    }
    arsort($trendData);
    $c=0;
    $tweetText="【DAILY TREND】\n";
    foreach($trendData as $word=>$value){
        //		$temp="<br> 【".++$c."】".$word." = ".$value;
        ++$c;
        if($c<6){
            for($i=1;$i<$c;$i++){
                $tweetText.=" ";
            }
            $rev=revD($value);
            //			$tweetText.=$word."【".$value."】"\n";
            $tweetText.=$word.$rev."\n";
        }
        //		echo $temp;

        $sql="INSERT INTO trendCashAll(text, count) VALUES('$word', '$value') ON DUPLICATE KEY UPDATE count = count + $value";
        $result=mysql_query($sql);
        echo "$sql <br>";

        if($c>19)break;
    }

    $url="https://api.twitter.com/1.1/statuses/update.json";
    $parameters = array(
            'status' => $tweetText,
    );
    $data = $connection->OAuthRequest($url, "POST", $parameters);
    $result=json_decode($data);
    //	var_dump($result);


    $sql="DELETE FROM trend";
    $result=mysql_query($sql);

    $sql="DELETE FROM trendCash";
    $result=mysql_query($sql);
}

function levelUp(){
    echo "1";
    $n=rand(1,3);
    $rTx=array(
            "運が","経済力が","計算力が","イケメンが","かわいさが",
            "かっこよさが","力が","身長が(mm)","体重が(kg)","優しさが",
            "儚さが","妄想力が","歌唱力が","宣伝力が","なんか",
            "面倒くささ","積極力が","ポジティブが","テンションが","理解力が",
            "ギャグセンスが","攻撃力が","防御力が","san値が","集中力が",
            "暖かさが","冷たさが","頭脳が","食欲が","疲れが",
            "知識が","X座標が","Y座標が","Z座標が","悟りが",
            "破壊力が","切なさがが","３分の１の純情な感情が","レディーガが","たけのこ派度が",
            "きのこ派度が","収入が","モラルが","体力が","HPが",
            "MPが","こうげきが","ぼうぎょが","とくこうが","とくぼうが",
            "すばやさが","綿密さが","ストレスが","画力が","眼力が",
            "聴力が","視力が","モチベーションが","英語力が","情報力が",
            "影響力が","エイムが","涙もろさが","どうでもよさが","回復力が",
            "妄想力が","煩悩が","┌（┌ ＾o＾）┐が","笑顔が","忍耐力が",
            "脚力が","魔法が","語彙が","中二病が","記憶力が",
            "","","","",""
    );
    echo "2";
    $text="";
    for($i=0;$i<$n;$i++){
        echo "3";
        $r=rand(0,75);
        $p=rand(1,102);
        $k=rand(1,8);
        echo "4";
        if($p==101 || $p==102)$p=999;


        $text.=$rTx[$r];
        echo "5";
        $text.=$p;
        if($k!=1){
            $text.="上がった\n";
        }
        else $text.="下がった\n";
        echo "6";
    }
    echo "7";
    return $text;
}

function revSpeed($a){
    $uwaa="＼( 'ω')／ウオオアアアー！";
    if($a>1000)return $uwaa;
    else if($a>500)return "(:3っ)っ 三三=ｰΣ[___]";
    else if($a>100)return "(:3っ)っ 三=ｰ [___]";
    else if($a>50)return "(:3[___]";
    else return "(￤3[___]";
    return "";
}





function checkClassTweet(){
    $tag="";
    $url="https://api.twitter.com/1.1/search/tweets.json";
    $parameters = array(
            'q' => "",
            'status' => $tweetText,
    );
    $data = $connection->OAuthRequest($url, "POST", $parameters);
    $result=json_decode($data);
}


function getTrend($strs){
    global $connection;
    global $screen_name,$list_name,$getCount;
    echo "gt:00";
    $url="https://api.twitter.com/1.1/lists/statuses.json";
    echo "gt:001";
    $parameters = array(
            'owner_screen_name' => $screen_name,
            'slug' => $list_name,
            'count' => $getCount,
    );
    echo "gt:002";
    $data = $connection->OAuthRequest($url, "GET", $parameters);
    echo "gt:003";
    $lists=json_decode($data);
    echo "gt:004";
    $point_counters=array();
    echo "gt:01";
    $after_ow=array();
    for($o;$o<count($strs);$o++){
        $point_counters[$o]=-15;
    }
    foreach($lists as $tweet){
        echo $tweet->text."<br>\n";
        for ($o=0;$o<count($strs);$o++){
            if(strpos($tweet->text,$strs[$o])){
                if($after_ow[$o]==$tweet->user->name){
                    $point_counters[$o]+=7;
                }else{
                    $point_counters[$o]+=15;
                }
                $after_ow[$o]=$tweet->user->name;
                echo "○".$point_counters[$o]."\n";
            }
        }
    }
    echo "gt:02";
    for ($o=0;$o<count($strs);$o++){
        $sql="SELECT text,count FROM trend WHERE `text` LIKE '$strs[$o]' limit 1";
        $result=mysql_query($sql);
        $row=mysql_fetch_assoc($result);
        $point01=$row['count'];
        echo "::".$point01;

        $sql="SELECT text,count FROM trendCash WHERE `text` LIKE '$strs[$o]' limit 1";
        $result=mysql_query($sql);
        $row=mysql_fetch_assoc($result);
        $point02=$row['count'];
        echo "::".$point02;

        $sql="SELECT text,count FROM trendCashAll WHERE `text` LIKE '$strs[$o] limit 1'";
        $result=mysql_query($sql);
        $row=mysql_fetch_assoc($result);
        $point03=$row['count'];
        echo "::".$point03;

        $point_counters[$o]+=$point01*10+$point02*3+$point03;
        //         if($point_counters[$o]>10 && $point02==0){
        //             $point_counters[$o]+=100;
        //         }
        //         if($point_counters[$o]>10 && $point03==0){
        //             $point_counters[$o]+=50;
        //         }
        echo "gt$o:d";
    }
    echo "gt:03";
    var_dump($point_counters);
    return $point_counters;
}



function getWords($key){
    $words=array();
    switch($key){
        case "janken":
            $words=array("グー","チョキ","パー");
            break;
        case "color":
            $words=array("赤","青","緑","黄","灰","黒","橙","白","水",
            "黄緑","茶","琥珀","瑠璃","肌","紅","桃","明","暗","薄","鮮",
            "濃","暖","寒");
            break;
        case "c":
            return chr(rand(65,90));
            //             for($i=0;$i<20;$i++)
            //                 $words[$i]=chr(rand(65,90));
            //             return $words;
            break;
        case "tramp":
            return toMark(rand(0,3)).rand(1,14);
            //             for($i=0;$i<20;$i++){
            //                 $words[$i]=toMark(rand(0,3)).rand(1,14);
            //             }
            //            return $words;
            break;
        default:
            break;
    }
    if(isset($words)){
        return $words[rand(0,count($words)-1)];
        //         $twords=array();
        //         $num=count($words)-1;
        //         for($i;$i<20;$i++){
        //             $twords=$words[rand(0,$num)];
        //         }
    }
    return -1;
}

function toMark($num){
    switch($num){
        case 0:return "♠";break;
        case 1:return "♣";break;
        case 2:return "♥";break;
        default: return "♦";break;
    }
}





define("B","<br>\n");
if(isset($_GET['elzzup'])){
    echo "l-1".B;
    $text=$_GET['elzzup'];
    //    $mtext=$matched[2];
    if(preg_match('/ちぇんじ.*(\s)?\((.*)\)/is', $text, $matches)){
        echo "l-2".B;
        echo $mtext=$matches[2];
        $mtext=str_replace_one("janken",$mtext);
        var_dump($mtext);
        echo B;
        $mtext=str_replace_one("color",$mtext);
        var_dump($mtext);
        echo B;
        $mtext=str_replace_one("c",$mtext);
        var_dump($mtext);
        echo B;
        $mtext=str_replace_one("tramp",$mtext);
        var_dump($mtext);
        echo B;
        echo "l-3".B;
        $tt=0;
        while(preg_match("/\{(\d+)-(\d+)\}/is",$mtext,$matches)){
            $min=$matches[1];
            $max=$matches[2];
            if($min>$max){
                $tenpc=$min;$min=$max;$max=$tenpc;
            }

            $strs=explode($matches[0],$mtext);
            $result=$strs[0];
            for($i=1;$i<count($strs);$i++){
                $randNum=rand($min,$max);
                $result.=$randNum.$strs[$i];
                //          $result.=$replace[$i].$strs[$i];
            }
            $mtext=$result;
            //   $mtext=str_replace($matches[0],$randNum,$mtext);
        }
        echo"l-4".B;
        echo $mtext.B;
    }
    echo "l-5".B;
    echo $mtext.B;

    //    var_dump(str_replace_one("te","あああ{te}いい{te}ううう"));

    //   var_dump(getWords("color"));
}



function str_replace_one($search,$str){
    $splitter="{".$search."}";
    $strs=explode($splitter,$str);
    $result=$strs[0];
    for($i=1;$i<count($strs);$i++){
        $result.=getWords($search).$strs[$i];
        //          $result.=$replace[$i].$strs[$i];
    }
    return $result;
}





function get_nico_smurls($keyword,$sort){
    $html = file_get_contents("http://www.nicovideo.jp/search/$keyword?sort=$sort&order=d");
    $urls = array();
    preg_match_all('/href="watch\/(\w+)[^>]*title="([^"]*)/is', $html, $matches, PREG_SET_ORDER);

    $i=0;
    foreach($matches as $match){
        $urls[$i]['name'] = $match[2];
        //$urls[$i]['url'] = "http://www.nicovideo.jp/watch/".$match[1];
        $urls[$i]['url'] = "nico.ms/".$match[1];
        $i++;
    }

    return $urls;
}


?>
