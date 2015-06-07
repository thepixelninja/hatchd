<?php
/*
Plugin Name: Stackoverflow
Plugin URI: http://fryed.co.uk
Description: Brings in top stackoverflow answers
Version: 1
Author: Ed Fryer
Author URI: http://fryed.co.uk
*/

//incase we need to reset - uncomment
//delete_option("stackoverflow_last-updated");

//set the default vals
$stackSettings = array(
	"user_id"	=> 653882,
	"limit"		=> 3
);

//the stack overflow array
$stackoverflow = array();

//init the plugin
add_action("init","initStackPlugin");

function initStackPlugin(){
	
	//grab the settings
	global $stackSettings;

	//check if it has been a day since last update
	//if it has then poll stackoverflow api
	//in db else just use saved answers.
	$time = time();
	$lastUpdated = get_option("stackoverflow_last-updated");
	if($lastUpdated){
		$diff = $lastUpdated+86400;
	}else{
		$diff = 0;
	}
	
	//its been a day - fetch new answers
	if($time > $diff){
		
		//echo "using fresh";
		
		//get the info from the stack api
		$STACK 			= new stackAPI();
		$STACK->userid 	= $stackSettings["user_id"];
		$STACK->limit	= $stackSettings["limit"];
		$STACK->init();
		$answers 		= $STACK->getAnswers();
		$username 		= $STACK->getUser();
		$rep 			= $STACK->getRep();
		
		//if no answers fetched
		if(empty($answers)){
			$username 	= get_option("stackoverflow_username");
			$rep 		= get_option("stackoverflow_rep");
			$answers 	= unserialize(get_option("stackoverflow_feed"));
			return false;
		}
		
		//update stack settings
		update_option("stackoverflow_username",$username);
		update_option("stackoverflow_rep",$rep);
		
		//save answers
		$savedAnswers = serialize($answers);
		update_option("stackoverflow_feed",$savedAnswers);
		
		//update time last updated
		update_option("stackoverflow_last-updated",$time);
	
	//not yet been a day so just use cashed answers
	}else{
		//echo "using cached";
		$username 	= get_option("stackoverflow_username");
		$rep 		= get_option("stackoverflow_rep");
		$answers 	= unserialize(get_option("stackoverflow_feed"));
	}
	
	global $stackoverflow;
	$stackoverflow["username"] 	= $username;
	$stackoverflow["rep"] 		= $rep;
	$stackoverflow["feed"] 		= $answers;
	
	//echo "<pre>";
	//print_r($stackoverflow);
	//die("</pre>");
	
}

function getStackFeed(){
	global $stackoverflow;
	return $stackoverflow;
}

//-----BRING IN STACKOVERFLOW INFO FROM STACK API-----//

class stackAPI {
	
	//set defaults
	public $limit = 3;
	public $userid;
	public $username;
	public $rep;
	public $answers = array();
	public $timeout = 10;
	
	//get the users answers from stack overflow
	public function init(){
		
		//check curl is installed
		if(!function_exists("curl_init")){
			die("Error: curl not installed");
		}
		
		//get the answers
		$feed = $this->makeRequest("users/{$this->userid}/answers?filter=withbody&sort=votes&pagesize=".$this->limit);
		
		//get username
		$this->username = $feed->items[0]->owner->display_name;
		
		//get rep
		$this->rep = $feed->items[0]->owner->reputation;
	
		//get question ids
		$questionIDS = array();
		foreach($feed->items as $answer){
			$questionIDS[] = $answer->question_id;
		}
		$questionIDS = implode(";",$questionIDS);
		
		//get questions
		$questions = $this->makeRequest("questions/$questionIDS?sort=votes");

		//build up answer array
		foreach($feed->items as $key => $answer){
			$question							= $questions->items[$key];
			$this->answers[$key]["title"] 		= mysql_real_escape_string($question->title);
			//$this->answers[$key]["up_count"] 		= $answer->up_vote_count;
			//$this->answers[$key]["down_count"] 	= $answer->down_vote_count;
			$this->answers[$key]["views"] 		= $question->view_count;
			$this->answers[$key]["score"] 		= $question->score;
			$this->answers[$key]["body"] 		= mysql_real_escape_string($answer->body);
			$this->answers[$key]["link"] 		= "http://stackoverflow.com/questions/".$answer->answer_id;
		}

	}

	public function makeRequest($method){
		
		//init cuel
		$ch = curl_init();
		
		//get feed
		$feed = "http://api.stackexchange.com/2.2/$method&site=stackoverflow.com";
		curl_setopt($ch,CURLOPT_URL,$feed);
	
		//set encoding to gzip
		curl_setopt($ch,CURLOPT_ENCODING,"gzip");
		
		//return value, dont print
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

		//settimeout
		curl_setopt($ch,CURLOPT_TIMEOUT,$this->timeout);
		
		//download the feed
		$feed = curl_exec($ch);
		
		//close curl resource
    	curl_close($ch);
		
		//remove weird trailing characters
		$feed = explode("{",$feed);
		array_shift($feed);
		$feed = "{".implode("{",$feed);

		//decode json
		$feed = json_decode($feed);
		
		return $feed;
		
	}
	
	//return the answers
	public function getAnswers(){
		return $this->answers;
	}
	
	//return the reputation
	public function getRep(){
		return $this->rep;
	}
	
	//return the username
	public function getUser(){
		return $this->username;
	}
	
	public function debug($debug){
		echo "<pre style='background:#000; color:#fff; padding:10px;'>";
		print_r($debug);
		echo "</pre>";
		exit;
	}
	
}

?>