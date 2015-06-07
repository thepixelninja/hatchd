<?php
/*
Plugin Name: Fryed Twitter
Plugin URI: http://fryed.co.uk
Description: Brings in latest tweets
Version: 1
Author: Ed Fryer
Author URI: http://fryed.co.uk
*/

//incase we need to reset - uncomment
//delete_option("twitter_last-updated");

//the settings
$twitterSettings = array(
	"consumer_key" 		=> "wdB0QQtjswAvtYNE035QNegK0",
	"consumer_secret" 	=> "5d3kVVGaB4RgXRkUk8ITdR4SPiyDwCB0ZqyM0TKwOOmu72e2mu",
	"access_token" 		=> "2534602160-LKaJdp2FvXk1JHZom0jGlaq7HgR8pSbsUDJPpvS",
	"secret"			=> "vTygx5CLJUPZNdjhqxRrLaMJ6SWA8x0XMT2hV9tOyY3gA",
	"limit"				=> 3
);
		
//the twitter array
$twitterFeed = array();

//init the plugin
add_action("init","initTwitterPlugin");

function initTwitterPlugin(){
	
	//grab the twitter class
	global $twitterSettings;
	
	//check if it has been a day since last update
	//if it has then poll stackoverflow api
	//in db else just use saved answers.
	$time = time();
	$lastUpdated = get_option("twitter_last-updated");
	if($lastUpdated){
		$diff = $lastUpdated+86400;
	}else{
		$diff = 0;
	}
	
	//its been a day - fetch new answers
	if($time > $diff){
		
		//echo "using fresh";
		
		//init the twitter class
		$TW = new twitter();
		$TW->consumerKey 		= $twitterSettings["consumer_key"];
		$TW->consumerSecret 	= $twitterSettings["consumer_secret"];		
		$TW->accessToken 		= $twitterSettings["access_token"];
		$TW->secret 			= $twitterSettings["secret"];
		$TW->limit 				= $twitterSettings["limit"];
		$tweets = $TW->getTweets();
		
		//if no tweets found
		if(empty($tweets)){
			$tweets["username"] = get_option("twitter_username");
			$tweets["tweets"] 	= unserialize(get_option("twitter_tweets"));
			return false;
		}
		
		//update twitter settings
		update_option("twitter_username",$tweets["username"]);
		$savedTweets = serialize($tweets["tweets"]);
		update_option("twitter_tweets",$savedTweets);
		
		//update time last updated
		update_option("twitter_last-updated",$time);
	
	//not yet been a day so just use cashed answers
	}else{
		//echo "using cached";
		$tweets = array();
		$tweets["username"] = get_option("twitter_username");
		$tweets["tweets"] 	= unserialize(get_option("twitter_tweets"));
	}
	
	global $twitterFeed;
	$twitterFeed["username"] 	= $tweets["username"];
	$twitterFeed["tweets"] 		= $tweets["tweets"];
	
	//echo "<pre>";
	//print_r($twitterFeed);
	//die("</pre>");
	
}

function getTwitterFeed(){
	global $twitterFeed;
	return $twitterFeed;
}

//----------TWITTER POST CLASS----------//

class twitter {
	
	public $consumerKey;
	public $consumerSecret;
	public $accessToken;
	public $secret;
	public $connection;
	public $limit;
	
	//----------GET TWEETS----------//
	
	function getTweets(){
			
		//include the twitter library file
		include("twitteroauth/twitteroauth.php");
		
		//connect to twitter
		$this->connection = new TwitterOAuth($this->consumerKey,$this->consumerSecret,$this->accessToken,$this->secret);
	
		//get tweets
		$tweets = $this->connection->get("statuses/user_timeline.json?count=".$this->limit);
		
		if($tweets->errors){
			return array();
		}
		
		$info = array();
		$info["tweets"] = array();
		
		foreach($tweets as $tweet){
			$text = trim($tweet->text);
			$info["tweets"][] = array(
				"id"	=> $tweet->id_str,
				"tweet" => $this->addLinks($text)
			);
		}
		
		if(isset($tweets[0])){
			$info["username"] = $tweets[0]->user->screen_name;
		}
		
		return $info;
		
	}
	
	//----------ADD LINKS----------//
	
	public function addLinks($text){
		
		$regEx = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
		//if there is a link in the text
		if(preg_match($regEx,$text,$url)){
			//add the links into the text
			return preg_replace($regEx,"<a target='_blank' href='{$url[0]}'>{$url[0]}</a>",$text);
		//else just return the text
		}else{
			return $text;
		}
		
	}
	
}

?>