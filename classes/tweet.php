<?php

class Tweet
{

    public $text;
    public $id;
    public $user_screen_name;
    public $user_id;
    public $isReply;
    public $client_name;
    public $source;

    public function __construct(stdClass $t)
    {
        $this->text = $t->text;
        $this->id = $t->id_str;
        $this->user_screen_name = $t->user->screen_name;
        $this->user_id = $t->user->id;

        $this->isReply = isset($t->in_replay_to_status_id);
        $this->client_name = trimValueA($t->source);
        if (empty($this->client_name)) {
            $this->client_name = 'web';
		}
        $this->source = $t->source;

        //        $this->screen_name = $t->;
    }

    public function isMention()
    {
        $sns = trimScreenNames($this->text);
        return !empty($sns);
    }

    public function __toString()
    {
        return "[{$this->id}]@{$this->user_screen_name}:{$this->text}\n";
    }

}
