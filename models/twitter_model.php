<?php

class TwitterModel {

    protected $connection;
    protected $owner_name;

    protected function __construct(TwitterOAuth $connection, $owner_name){
		$connection->host = "https://api.twitter.com/1.1/";
        $this->connection = $connection;
        $this->owner_name = $owner_name;
    }


    /* --------------------------------------------------------- *
     *     gets
    * --------------------------------------------------------- */

    //Serach word
    protected function getSearch($text, $since_id = -1, $until = null, $count = num_get_count) {
        global $debug;
        $url = "search/tweets";
        $parameters = array(
                'q' => $text,
                'count' => $count,
                'since_id' => $since_id,
        );
        if (!empty($until)) {
            array_pop($parameters);
            $parameters['until'] = $until;
        }
        $data = $this->connection->get($url, $parameters);
        $this->checkRequestError($data, $url);
        return $data;
    }



    //Get DM
    protected function getDirectMessage($since_id = -1, $count = num_get_count) {
        $url="direct_messages";
        $parameters = array(
                'count' => $count,
                'since_id' => $since_id,
        );
        $data = $this->connection->get($url, $parameters);
        $this->checkRequestError($data, $url);
        return $data;
    }



    //Get Timeline
    protected function getTimeline($user_name = null, $since_id = -1, $count = num_get_count){
        $url="statuses/home_timeline";
        $parameters = array(
                'count' => $count,
                'since_id' => $since_id,
        );
        $data = $this->connection->get($url, $parameters);
        $this->checkRequestError($data, $url);
        return $data;
    }

    //Get List
    protected function getListTimeline($owner_name, $list_name, $since_id = -1, $count = num_get_count){
        $url="lists/statuses";
        $parameters = array(
                'owner_screen_name' => $owner_name,
                'slug' => $list_name,
                'count' => $count,
                'since_id' => $since_id,
                //                'list_id' => tw_list_id,
        );
        $data = $this->connection->get($url, $parameters);
        return $this->checkRequestError($data, $url) ? $data : null;
    }

    //Get mentions
    protected function getMentions($since_id = -1, $count = num_get_count){
        $url="statuses/mentions_timeline";
        $parameters = array(
                'count' => $count,
                'since_id' => $since_id,
        );
        $data = $this->connection->get($url, $parameters);
        return $this->checkRequestError($data, $url) ? $data : null;
    }

    protected function getUser($user_id){
        $url="users/show";
        $parameters = array(
                'user_id' => "$user_id",
                'include_entities' => '1',
        );
        $data = $this->connection->get($url, $parameters);
        $this->checkRequestError($data, $url);
        return $data;
    }

    protected function getListMembersShow($list_owner_name, $list_name, $screen_name, $skip = true) {
        $url="lists/members/show";
        $parameters = array(
                'owner_screen_name' => $list_owner_name,
                'slug' => $list_name,
                'screen_name' => $screen_name,
                'skip_status' => $skip,
        );
        print_r($parameters);
        $data = $this->connection->get($url, $parameters);
        $this->checkRequestError($data, $url);
        return $data;
    }

    //list show
    protected function getFollowedList($screen_name) {
        $url="lists/memberships";
        $parameters = array(
                'screen_name' => $screen_namen,
        );
        $data = $this->connection->get($url, $parameters);
        $this->checkRequestError($data, $url);
        return $data;
    }

    /* --------------------------------------------------------- *
     *     posts
    * --------------------------------------------------------- */

    //Tweet
    protected function postTweet($text, $mentionID = null){
        $url = "statuses/update";
        $text = mb_substr($text, 0, 140);
        $parameters = array(
                'status' => $text,
        );
        print_r($parameters);
        if(isset($mentionID))$parameters['in_reply_to_status_id'] = $mentionID;
        $data = $this->connection->post($url, $parameters);	//debugSet
        $this->checkRequestError($data, $url);
    }

    //udpate profile
    protected function postProfile($type, $value){
        $url="account/update_profile";
        $parameters = array(
                $type => $value,
        );
        $data = $this->connection->post($url, $parameters);	//debugSet
        $this->checkRequestError($data, $url);
        return $data;
    }

    //follow member
    protected function postFollowMemberCreate($list_owner_name, $list_name, $screen_name, $skip = true) {
        $url="lists/members/create";
        $parameters = array(
                'owner_screen_name' => $list_owner_name,
                'slug' => $list_name,
                'screen_name' => $screen_name,
        );
        $data = $this->connection->post($url, $parameters);
        $this->checkRequestError($data, $url);
        return $data;
    }


    protected function checkRequestError($data, $url = null) {
        if(isset($data->errors)){
            print_r($data);
            print_r($data, $url);
//            super_die(array($data, $url));
            return false;
        }
        return true;
    }
}



?>
