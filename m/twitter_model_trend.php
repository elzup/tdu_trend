<?php

require_once('./twitter_model.php');

class TwitterModelTrend extends TwitterModel
{
    protected $list_name;

    public function __construct(TwitterOAuth $connection, $owner_name, $list_name)
    {
        parent::__construct($connection, $owner_name);
        $this->list_name = $list_name;
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
        if (DEBUG) {
			echo $text;
		} else {
			$this->postTweet($text);
		}
	}

    public function tweetTrendDay($words)
    {
        $text = "【Daily Treand】\n";
        foreach ($words as $key => $value) {
			$text .= $key . $this->createRateTextFromPointDay($value) . "\n";
		}
		if (ma_debug_tweet) {
			echo $text;
		} else {
			$this->postTweet($text);
		}
	}

    public function tweetFollowed(Tweet $target)
    {
        $text = "@" . $target->user_screen_name . " TDUリストに追加しました( 'ω')v";
        $this->postTweet($text, $target->id);
    }

    private function isListFollow($target_screen_name)
    {
        $result = $this->getFollowedList($target_screen_name);
        foreach ($result->lists as $li)
        {
            echo $li->name . PHP_EOL;
            if (strtolower($li->name) == strtolower($this->list_name))
            {
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
