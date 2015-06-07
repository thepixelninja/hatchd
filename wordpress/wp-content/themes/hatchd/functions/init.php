<?php
/*
 * INIT THE THEME FUNCTIONS
 */

//load up the required files
require_once("core.php");
require_once("validator.php");

//init the core class
$C = new core();

//add featured image support
add_theme_support("post-thumbnails");
add_image_size("square_thumb",300,300,true);
add_image_size("small_image",480,99999);
add_image_size("medium_image",750,99999);
add_image_size("full_web",1200,99999);

//allow upload of svg files
add_filter("upload_mimes","customUploadMimes");
add_action("admin_head","svgCss");
function customUploadMimes($existing_mimes=array()){
	//add the file extension to the array
	$existing_mimes["svg"] = "image/svg+xml";
	//call the modified list of extensions
	return $existing_mimes;
}
function svgCss(){
	echo "
		<style type='text/css'>
			img[src*='.svg'] {
				width:100%;
				height:auto;
			}
		</style>
	";
}

//add custom css to admin pages to fix crayon plugin
function crayonFix(){
	echo "
		<style type='text/css'>
			.crayon-span, 
			.crayon-span-5, 
			.crayon-span-10, 
			.crayon-span-50, 
			.crayon-span-100, 
			.crayon-span-110 {
				float:none;
			}
		</style>
	";
}
add_action("admin_head","crayonFix");

//handle the home page (remember to change htaccess)
add_action("get_header","setupHomepage");
function setupHomepage(){
	global $C;
	global $post;
	if($C->isHome() && !isset($_GET["preview"])){
		$home = new WP_Query(array(
			"numberposts" 	=> 1,
			"offset"		=> 0,
			"post_type" 	=> "home"
		));
		$post = $home->post;
	}
}

//remove wp added jquery and others if needed
function deregister_scripts(){
	global $wp_scripts;
	//scripts to deregister
	$deregisteredscripts = array("jquery");
	//degregister each script
	foreach($deregisteredscripts as $script){
		wp_deregister_script($script);
	}
	//remove deregistered scripts as dependencies of any other scripts depending on them  
	if(false != $wp_scripts->queue){
		foreach($wp_scripts->queue as $script){
			if(isset($wp_scripts->registered[$script])){
				$wp_scripts->registered[$script]->deps = array_diff($wp_scripts->registered[$script]->deps,$deregisteredscripts);
			}
		}
	}
}
add_action("wp_print_scripts","deregister_scripts",101);

//move all js and css to the bottom of the page
add_action("wp_print_styles",function(){
	//ensure we are on head
	if(!doing_action("wp_head")){
		return;
	}
	global $wp_scripts,$wp_styles;
	//save actual queued scripts and styles
	$queued_scripts = $wp_scripts->queue; 
	//$queued_styles  = $wp_styles->queue;
	//empty the scripts and styles queue
	if(false != $wp_scripts->queue){
		$wp_scripts->queue = array();
		$wp_scripts->to_do = array();
	}
	if(false != $wp_styles->queue){
		//$wp_styles->queue  = array();
		//$wp_styles->to_do  = array();
	}
	add_action("wp_footer",function()use($queued_scripts,$queued_styles){
		//reset the queue to print scripts and styles in footer
		global $wp_scripts,$wp_styles;
		if(!empty($queued_scripts)){
			$wp_scripts->queue = $queued_scripts;
			$wp_scripts->to_do = $queued_scripts;
		}
		if(!empty($queued_styles)){
			//$wp_styles->queue  = $queued_styles;
			//$wp_styles->to_do  = $queued_styles;
		}
	},0);
},0);

//add short codes
add_shortcode("logos",array("core","printPageLogos"));

//flush the rewrite rules
//flush_rewrite_rules(false);

?>