<?php
/*
Plugin Name: Fryed Import
Plugin URI: http://fryed.co.uk
Description: Import pages from previous site
Version: 1
Author: Ed Fryer
Author URI: http://fryed.co.uk
*/

return false;

if(isset($_GET["import"])){
	include_once("posts.php");
	add_action("init","initImport");
	return false;
}

function initImport(){
	global $posts;
	foreach($posts as $post){
		echo "Inserting post '{$post->post_title}'<br/>";
		wp_insert_post($post);
	}
	exit;
}

//return false;

session_start();

include_once("db_connect.php");

//connect to DB
$DBprefix = "fryed";
$DB = new DBconnect();
$DB->username 		= 	"root";
$DB->password 		= 	"root";
$DB->database 		= 	"live_fryed.co.uk";
$DB->host 			= 	"localhost";
$DB->connect();

$pages = $DB->query("*","pages","");
$blog = $DB->query("*","blog","");
$pages = array_merge($pages,$blog);
$base = dirname(__FILE__);

//debug($_SESSION["errors"]);
//debug($pages);

$posts = array();
foreach($pages as $page){
	$type = "page";
	if(strstr($page["section"],"portfolio") !== false){
		$type = "portfolio";
	}
	if(strstr($page["section"],"blog") !== false){
		$type = "post";
	}
	$posts[] = array(
		"post_title" 	=> $page["title"],
		"post_content" 	=> str_replace("\$","\\$",str_replace("\"","\'",strip_tags(trim($page["content"]),"<p><a><ul><li><ol><blockquote><code>"))),
		"post_excerpt"	=> addslashes(str_replace("\"","\'",strip_tags(trim($page["description"])))),
		"post_name"		=> str_replace("/","",$page["uri"]),
		"post_type"		=> $type
	);
}

$file = "<?php \n \$posts = array(); \n";
foreach($posts as $post){
	$file .= "\$posts[] = array(
		'post_title' 	=> \"{$post["post_title"]}\",
		'post_content' 	=> \"{$post["post_content"]}\",
		'post_excerpt'	=> \"{$post["post_excerpt"]}\",
		'post_name'		=> \"{$post["post_name"]}\",
		'post_type'		=> \"{$post["post_type"]}\"
	);\n";
}
$file .= "\n?>";

$fp = fopen($base."/posts.php","w");
fputs($fp,$file);
fclose($fp);

//echo $file;

debug($posts);

//debug($posts);

function debug($debug){
	
	echo "<pre style='padding:10px; background:#000; color:#fff;'>";
	print_r($debug);
	echo "</pre>";
	
}

unset($_SESSION["errors"]);

exit;

?>