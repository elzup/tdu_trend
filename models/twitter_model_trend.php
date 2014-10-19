<?php

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
            $text .= createRateTextFromPoint($value, ma_only);
            if (!empty($chains[$key]))
            {
                $text .= createChainText($chains[$key]);
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
			$text .= $key . createRateTextFromPointDay($value) . "\n";
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

    public function isListFollow($target_screen_name)
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

    public function followList($target_screen_name)
    {
        return $result = $this->postFollowMemberCreate($this->owner_name, $this->list_name, $target_screen_name);
    }

	public function loadList($sleg) {
		return $this->getListTimeline($this->owner_name, $this->list_name, $sleg);
	}

	public function loadMention($from) {
		return $this->getMentions($from);
	}

}
