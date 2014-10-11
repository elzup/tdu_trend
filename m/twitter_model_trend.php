<?php

require_once('./twitter_model.php');

class TwitterModelTrend extends TwitterModel
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
    protected $map_trend_point;
    protected $map_user;

    public function __construct(TwitterOAuth $connection, $owner_name, $list_name, $mem_json_filename)
    {
        parent::__construct($connection, $owner_name);
        $this->list_name = $list_name;
        $this->mem = array();
        $this->loadMemFile($mem_json_filename);
        //		print_r($this->mem);
        $this->time_stamp_pre = $this->mem->timestamp_tl;
    }

    // ----------------- tweet Manage Wrap ----------------- //
    public function tweetTrend($words, $chains = NULL)
    {
        $text = "";
        foreach ($words as $key => $value)
        {
            $text .= $key;
            $text .= $this->createRateTextFromPoint($value, ma_only);
            if (!empty($chains[$key]))
            {
                $text .= $this->createChainText($chains[$key]);
            }
            $text .= "\n";
        }
        if (DEBUG)
            echo $text;
        else
            $this->postTweet($text);
    }

    public function tweetTrendDay($words)
    {
        $text = "【Daily Treand】\n";
        foreach ($words as $key => $value)
            $text .= $key . $this->createRateTextFromPointDay($value) . "\n";
        if (ma_debug_tweet)
            echo $text;
        else
            $this->postTweet($text);
    }

    public function tweetFollowed(Tweet $target)
    {
        $text = "@" . $target->user_screen_name . " TDUリストに追加しました( 'ω')v";
        $this->postTweet($text, $target->id);
    }

    private function isListFollow($target_screen_name)
    {
        $result = $this->getFollowedList($target_screen_name);
        echo "【リストリスト】\n";
        foreach ($result->lists as $li)
        {
            echo $li->name . PHP_EOL;
            if (strtolower($li->name) == strtolower($this->list_name))
            {
                echo "matched";
                return true;
            }
        }
        return false;
    }

    private function followList($target_screen_name)
    {
        return $result = $this->postFollowMemberCreate($this->owner_name, $this->list_name, $target_screen_name);
    }

}
