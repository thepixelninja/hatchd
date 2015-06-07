<?php
/*
 * THE CORE CLASS
 */

class core {

	/*
	 * @desc - inits the class
	 */
	public function __construct(){

	}

	/*
	 * @desc 	- check if page is home page
	 * @return  - bool, whether on home page
	 */
	public function isHome(){

		$base 	= str_replace("index.php","",$_SERVER["SCRIPT_NAME"]);
		$uri	= explode("?",$_SERVER["REQUEST_URI"]);
		$uri	= $uri[0];
		if($base == $uri && !isset($_GET["preview"])){
			return true;
		}else{
			return false;
		}

	}

	/*
	 * @desc 			- builds the main menu
	 * @return (string) - the main menu
	 */
	public function siteMenu($depth=2,$classes="",$raw=false){

		//parse a page into a menu item
		if(!function_exists("parsePage")){
			function parsePage($C,$page){
				$li = array();
				$li["title"] 			= $page->post_title;
				$li["excerpt"] 			= $C->pageExcerpt($page->ID,20);
				$li["link"]				= $page->guid;
				$li["id"]				= $page->ID;
				if(get_the_ID() == $page->ID){
					$li["active"] = true;
				}else{
					$li["active"] = false;
				}
				if(is_404()){
					$li["active"] = false;
				}
				return $li;
			}
		}

		//the build array loop
		if(!function_exists("buildLevel")){
			function buildLevel($C,$pages,$depth,$pass){
				$pass++;
				$ul = array();
				foreach($pages as $page){
					$li = parsePage($C,$page);
					if($pass < $depth){
						$li["children"] = array();
						$types		= $C->postTypes($page->post_name);
						$children 	= $C->pageChildren($page->ID);
						$all = array_merge($types,$children);
						if(!empty($all)){
							$li["children"] = buildLevel($C,$all,$depth,$pass);
						}
					}
					$ul[] = $li;
				}
				return $ul;
			}
		}

		//the build html loop
		if(!function_exists("buildHtml")){
			function buildHtml($level){
				$ul ="";
				foreach($level as $li){
					if($li["active"]){
						$active = "active";
					}else{
						$active = "";
					}
					$ul .= "<li class='$active page-{$li["id"]}'><a href='{$li["link"]}' title='{$li["excerpt"]}'>{$li["title"]}</a>";
					if(!empty($li["children"])){
						$ul .= "<ul class='children'>";
						$ul .= buildHtml($li["children"]);
						$ul .= "</ul>";
					}
					$ul .= "</li>";
				}
				return $ul;
			}
		}

		//define menu vars
		$menu 	 = array();
		$mainNav = "<ul class='$classes'>\n";

		//grab the top level pages
		$pages = get_pages(array(
			"numberposts" 	=> $max,
			"offset"		=> $start,
			"child_of" 		=> $id,
			"sort_column" 	=> "menu_order",
			"post_status"   => "publish",
			"parent"		=> 0
		));

		//init the build
		$menu = buildLevel($this,$pages,$depth,0);

		//add the home page onto the beginning
		$home 	= $this->postTypes("home");
		array_unshift($menu,parsePage($this,$home["posts"][0]));

		//if raw just return array
		if($raw){
			return $menu;
		}

		//else build html menu
		$mainNav .= buildHtml($menu);
		$mainNav .= "</ul>\n";

		return $mainNav;

	}

	/*
	 * @desc 			- works out the site path
	 * @return (string) - the site path
	 */
	public function sitePath(){

		return get_bloginfo("url");

	}

	/*
	 * @desc 			- grabs the site tagline
	 * @return (string) - the site tagline
	 */
	public function siteTagline(){

		return get_bloginfo("description");

	}

	/*
	 * @desc 			- grabs the site name
	 * @return (string) - the site name
	 */
	public function siteName(){

		return get_bloginfo("name");

	}

	/*
	 * @desc 			- grabs the site tags
	 * @return (array)  - the site tags
	 */
	public function siteTags(){

		$tags = get_tags();
		return $tags;

	}

	/*
	 * @desc 			- works out the sites theme path
	 * @return (string) - the site theme path
	 */
	public function themePath(){

		return get_bloginfo("template_directory");

	}

	/*
	 * @desc 			- get the page title
	 * @return (string) - the page title
	 */
	public function pageTitle($id=false){

		if(!$id){
			global $post;
			$id = $post->ID;
		}
		if($this->isCategory()){
			$title = $this->getCategory()->name;
		}else if(is_404()){
			$title = "Page not found";
		}else{
			$title = get_the_title($id);
		}
		return $title;

	}

	/*
	 * @desc 			- get the page meta title
	 * @return (string) - the page meta title
	 */
	public function pageMetaTitle($id=false){

		if(!$id){
			global $post;
			$id = $post->ID;
		}
		if(is_404()){
			$title = "Page not found";
		}else if($this->isCategory()){
			$title = $this->getCategory()->name;
		}else{
			$title = get_the_title($id);
		}
		$title = $title." | ".get_bloginfo("name");
		return $title;

	}

	/*
	 * @desc 			- create a page body class
	 * @return (string) - the page body class
	 */
	public function pageClass($id=false){

		if(!$id){
			global $post;
		}else{
			$post = get_post($id);
		}
		$cls = $post->post_name;
		return $cls;

	}

	/*
	 * @desc 			- get the page content
	 * @return (string) - the page content
	 */
	public function pageContent($id=false,$debug=false){

		if(!$id){
			global $post;
			$id 	 = $post->ID;
			$content = $post->post_content;
		}else{
			$content = get_post_field("post_content",$id);
		}
		$content = apply_filters("the_content",$content);
		if($content != ""){
			if($this->isCategory()){
				$content = strip_tags($content);
				$content = "<p>$content</p>";
			}
			$content = $this->addPicturefill($content);
			return "<div class='editableContent'>".$content."</div>";
		}else{
			return "";
		}

	}

	/*
	 * @desc 					- create valid picturefill elements for images
	 * @param $content (string)	- the content to parse for picturefill
	 * @return (string)			- the modified content
	 */
	public function addPicturefill($content){

		//find the images in the content
		preg_match_all("/<img[^']*?src=\"([^']*?)\"[^']*?>/",$content,$matches,PREG_PATTERN_ORDER);

		//if no images just return content
		if(!isset($matches[0]) || empty($matches[0])){
			return $content;
		}

		$found = array();

		//grab the img classes and try to get the image id
		foreach($matches[0] as $img){
			$doc = new DOMDocument();
			$doc->loadHTML($img);
			$i = $doc->getElementsByTagName("img");
			foreach($i as $tag){
				$class = $tag->getAttribute("class");
				$classes = explode(" ",$class);
				foreach($classes as $class){
					if(strstr($class,"wp-image") !== false){
						$id = str_replace("wp-image-","",$class);
						$found[$id] = $img;
					}
				}
			}
		}

		//loop the found images and create the picture fill markup
		foreach($found as $key => $img){

			$image	= get_post($key);
			$small 	= wp_get_attachment_image_src($key,"small_image");
			$medium = wp_get_attachment_image_src($key,"medium_image");
			$large 	= wp_get_attachment_image_src($key,"full_web");
			$picturefill = "
				<picture>
					<!--[if IE 9]><video style='display:none;'><![endif]-->
					<source srcset='{$large[0]}' media='(min-width: 1200px)'>
					<source srcset='{$medium[0]}' media='(min-width: 750px)'>
					<source srcset='{$small[0]}'>
					<!--[if IE 9]></video><![endif]-->
					<img srcset='{$small[0]}' alt='{$image->post_title}'/>
				</picture>
			";
			$content = str_replace($img,$picturefill,$content);

		}

		return $content;

	}

	/*
	 * @desc - produce the picturefill html
	 */
	public function picturefill($img,$alt=""){

		$img 	= explode(".",$img);
		$ext 	= array_pop($img);
		$img 	= implode(".",$img);
		$img 	= $this->themePath()."/images/".$img;
		$large 	= $img."-large".".".$ext;
		$medium = $img."-medium".".".$ext;
		$small 	= $img."-small".".".$ext;

		return "
			<picture>
				<!--[if IE 9]><video style='display:none;'><![endif]-->
				<source srcset='$large' media='(min-width: 1200px)'>
				<source srcset='$medium' media='(min-width: 750px)'>
				<source srcset='$small'>
				<!--[if IE 9]></video><![endif]-->
				<img srcset='$small' alt='$alt'/>
			</picture>
		";

	}

	/*
	 * @desc - grab an svg
	 */
	public function svg($svg,$upload=false){

		if(!$upload){
			$svg = $this->themePath()."/images/$svg";
		}else{
			if(strstr($svg,".svg") === false){
				return $svg;
			}
			$dom = new DOMDocument();
			$dom->loadHTML($svg);
			$svg 	= $dom->getElementsByTagName("img")->item(0)->getAttribute("src");
			$base 	= str_replace("/index.php","",$_SERVER["SCRIPT_FILENAME"]);
			$svg 	= str_replace($this->sitePath(),$base,$svg);
		}
		return @file_get_contents($svg);

	}

	/*
	 * @desc 			- get the page excerpt
	 * @return (string) - the page excerpt
	 */
	public function pageExcerpt($id=false,$truncate=60){

		if(!$id){
			global $post;
			$id = $post->ID;
		}else{
			$post = get_post($id);
		}
		$excerpt = $post->post_excerpt;
		if($excerpt == ""){
			$excerpt	= $this->pageContent($id,$debug);
			$excerpt	= $this->stripTag("style",$excerpt);
			$excerpt	= $this->stripTag("code",$excerpt);
			$excerpt 	= $this->truncate(strip_tags($excerpt),$truncate);
		}
		//$excerpt = htmlspecialchars($excerpt,ENT_QUOTES);
		$excerpt = addslashes($excerpt);
		return $excerpt;

	}

	/*
	 * @desc 					- remove tags and content from string
	 * @param $tag (string)		- the tag to remove
	 * @param $string (string)	- the string to check
	 * @return (string) 		- the string without the specified tags
	 */
	public function stripTag($tag,$string){

		$startPoint = "<$tag";
		$endPoint	= "</$tag>";
		$newText	= ">";
		$string 	= preg_replace('#('.preg_quote($startPoint).')(.*)('.preg_quote($endPoint).')#si','$1'.$newText.'$3',$string);
		return $string;

	}

	/*
	 * @desc 			- get the page slug
	 * @return (string) - the page slug
	 */
	public function pageLink($id=false){

		$slug = array();
		if(!$id){
			global $post;
		}else{
			$post = get_page($id);
		}
		$link = $post->guid;
		$pageTrail = $this->getPageTrail($post);
		foreach($pageTrail as $trail){
			$slug[] = $trail->post_name;
		}
		$slug = implode("/",array_reverse($slug));
		if(substr($slug,-1) != "/"){
			$slug = $slug."/";
		}
		return $slug;

	}

	/*
	 * @desc 			- get the post slug
	 * @return (string) - the post slug
	 */
	public function postLink($id=false){

		$slug = array();
		if(!$id){
			global $post;
		}else{
			$post = get_post($id);
		}
		$link = $post->guid;
		$cat 	 = get_the_category($post->ID);
		$cat 	 = $cat[0]->slug;
		$slug[]  = $cat;
		$slug[] = $post->post_name;
		$slug = implode("/",$slug);
		if(substr($slug,-1) != "/"){
			$slug = $slug."/";
		}
		return $slug;

	}

	/*
	 * @desc 			- get the page template
	 * @return (string) - the page template
	 */
	public function pageTemplate(){

		global $post;
		$meta = get_post_meta($post->ID);
		if($this->isHome()){
			$template = "index";
		}elseif($post->post_type != "post" && $post->post_type != "page" && isset($meta["_wp_mf_page_template"])){
			$template = str_replace(".php","",$meta["_wp_mf_page_template"][0]);
		}else{
			$template = explode("/",get_page_template());
			$template = str_replace(".php","",$template[count($template)-1]);
		}
		return $template;

	}

	/*
	 * @desc 					 - check the page template
	 * @param $template (string) - the page template to check against
	 * @return (bool)			 - whether the template matches
	 */
	public function pageTemplateIs($template){

		$match = false;
		if($this->pageTemplate() == $template){
			$match = true;
		}
		return $match;

	}

	/*
	 * @desc 				- check the page catrgory
	 * @param $cat (string) - the page template to check against
	 * @return (bool)		- whether the template matches
	 */
	public function pageCategoryIs($cat){

		$match = false;
		$curCat = get_the_category();
		foreach($curCat as $c){
			if($c->name == $cat){
				$match = true;
			}
		}
		return $match;

	}

	/*
	 * @desc 			- list the sites categories
	 * @return (string)	- the category list
	 */
	public function siteCategories(){

		$cats = "<ul class='list-unstyled'>";
		$cats .= wp_list_categories(array(
			"echo" 		=> false,
			"title_li" 	=> ""
		));
		$cats .= "</ul>";
		return $cats;

	}

	/*
	 * @desc 			- check if on taxonomy page
	 * @return (bool) 	- whether on taxonomy page
	 */
	public function isTaxonomy(){

		return is_tax();

	}

	/*
	 * @desc 			- check if on category page
	 * @return (bool) 	- whether on category page
	 */
	public function isCategory($cat=""){

		return is_category($cat);

	}

	/*
	 * @desc 			- get a category
	 * @return (object) - the category
	 */
	public function getCategory($object=false){

		if($this->isCategory()){
			return get_category(get_query_var("cat"),false);
		}else{
			$cat = get_the_category();
			if(isset($cat[0]) && isset($cat[0]->cat_name) && !$object){
				return $cat[0]->cat_name;
			}else{
				return $cat;
			}
		}
		return false;

	}

	/*
	 * @desc 				- grab some post meta
	 * @param $key (string) - the meta key to fetch
	 * @param $id (int) 	- the id of the page meta to fetch
	 * @return (string)		- the meta value
	 */
	public function pageMeta($key,$id=false,$unique=true){

		if(!$id){
			global $post;
			$id = $post->ID;
		}
		$meta = get_post_meta($id,$key,$unique);
		return $meta;

	}

	/*
	 * @desc 				- check is a post has meta set
	 * @param $key (string) - the meta key to check
	 * @param $id (int) 	- the id of the page meta to check
	 * @return (bool)		- whether the meta value exists
	 */
	public function isPageMeta($key,$id=false){

		if(!$id){
			global $post;
			$id = $post->ID;
		}
		$meta = get_post_meta($id,$key,true);

		if($meta == ""){
			return false;
		}else{
			return true;
		}

	}

	/*
	 * @desc 					- grab a pages magic fields group
	 * @param $name (string) 	- the name of the group
	 * @param $id (int) 		- the id of the page group to get
	 * @return (array)			- the group
	 */
	public function pageGroup($name,$id=false){

		if(!$id){
			global $post;
			$id = $post->ID;
		}
		if(function_exists("get_group")){
			$group = get_group($name,$id);
			return $group;
		}else{
			return array();
		}

	}

	/*
	 * @desc 						- grab the pages feature slides
	 * @param $class (string) 		- classes to add
	 * @param $id (int) 			- the id of the page group to get
	 * @return (string or array)	- the slides
	 */
	public function pageFeature($class="",$id=false,$raw=false){

		$feature = array();

		if(!$id){
			global $post;
			$id = $post->ID;
		}

		$slides = $this->pageGroup("feature_slide",$id);

		if(empty($slides)){
			return false;
		}

		$imgIDs = get_post_meta($id,"feature_slide_image");

		$i = 0;
		foreach($slides as $key => $slide){
			$feature[$i]["text"]  = $slide["feature_slide_text"][1];
			$feature[$i]["link"]  = $slide["feature_slide_link"][1];
			$feature[$i]["image"] = $this->getImage($imgIDs[$key-1],"full_web",true);
			$feature[$i]["thumb"] = $this->getImage($imgIDs[$key-1],"square_thumb",true);
			$i++;
		}

		if($raw){
			return $feature;
		}

		$featureList = "<ul class='$class list-unstyled'>";
		foreach($feature as $f){
			$featureList .= "
				<li>
					<h2>{$f["title"]}</h2>
					<img src='{$f["image"][0]}' alt='{$f[title]}' class='image'/>
					<img src='{$f["thumb"][0]}' alt='{$f[title]}' class='thumb'/>
					<a href='{$f["link"]}' title='{$f["title"]}'></a>
				</li>
			";
		}
		$featureList .= "</ul>";
		return $featureList;

	}

	/*
	 * @desc 				 - get the page feature image
	 * @param $type (string) - the type of image to return ie thumb
	 * @param $link (bool) 	 - whether to wrap the image in a link
	 * @param $id (int) 	 - the id of the page feature to fetch
	 * @return (string) 	 - the page feature image
	 */
	public function pageFeatureImage($link=false,$id=false,$raw=false,$class=""){

		if(!$id){
			global $post;
			$id = $post->ID;
		}
		$image = false;
		if(has_post_thumbnail($id)){
			$imgId = get_post_thumbnail_id($id);
			if($raw){
				$src 	= wp_get_attachment_image_src($imgId,"full_web");
				$image  = $src[0];
				return $image;
			}
			$imgInfo 	= wp_get_attachment_image_src($imgId,$type);
			$imgAlt		= get_post_meta($imgId,"_wp_attachment_image_alt",true);
			$imgTitle 	= get_the_title($imgId);

			$small 	= wp_get_attachment_image_src($imgId,"small_image");
			$medium = wp_get_attachment_image_src($imgId,"medium_image");
			$large 	= wp_get_attachment_image_src($imgId,"full_web");
			$image = "
				<picture>
					<!--[if IE 9]><video style='display:none;'><![endif]-->
					<source srcset='{$large[0]}' media='(min-width: 1200px)'>
					<source srcset='{$medium[0]}' media='(min-width: 750px)'>
					<source srcset='{$small[0]}'>
					<!--[if IE 9]></video><![endif]-->
					<img srcset='{$small[0]}' alt='{$imgAlt}'/>
				</picture>
			";

			if($link){
				$bigInfo = wp_get_attachment_image_src($imgId,"full");
				$image 	 = "<a class='lightBox' title='{$imgTitle}' href='{$bigInfo[0]}'>$image</a>";
			}
		}
		return $image;

	}

	/*
	 * @desc 				 - check if there is a page feature image
	 * @param $id (int) 	 - the id of the page feature to check
	 * @return (bool) 	 	 - whether there is a page feature image
	 */
	public function isPageFeatureImage($id=false){

		if(!$id){
			global $post;
			$id = $post->ID;
		}
		$image = false;
		if(has_post_thumbnail($id)){
			return true;
		}else{
			return false;
		}

	}

	/*
	 * @desc 				 - get the page feature thumb
	 * @param $id (int) 	 - the id of the page feature to fetch
	 * @param $raw (bool) 	 - whether to bring back the raw src or the html img el
	 * @return (string) 	 - the page feature thumb
	 */
	public function pageFeatureThumb($type="square_thumb",$id=false,$raw=false){

		if(!$id){
			global $post;
			$id = $post->ID;
		}
		$image = $this->getImage(get_post_meta($id,"page_details_thumbnail",true),$type,$raw);
		if($image == ""){
			$image = get_the_post_thumbnail($id,$type);
			if($raw && $image != ""){
				$doc = new DOMDocument();
				$doc->loadHTML($image);
				$i = $doc->getElementsByTagName("img");
				foreach($i as $img){
					$image = $img->getAttribute("src");
				}
			}
		}else{
			if($raw && isset($image[0])){
				$image = $image[0];
			}
		}
		return $image;

	}

	/*
	 * @desc 				 - get an image from magic fields
	 * @param $id (int) 	 - the id of the page to fetch
	 * @param name (string)	 - the name of the field
	 * @param $raw (bool)	 - whether to bring back the src or the img
	 * @return (string) 	 - the image
	 */
	public function mfImage($name,$id=false,$type="full",$raw=false){

		if(!$id){
			global $post;
			$id = $post->ID;
		}else{
			$post = get_post($id);
		}
		$image = $this->getImage(get_post_meta($id,$name,true),$type,$raw);
		return $image;

	}

	/*
	 * @desc 			 - get the page images
	 * @param $id (int)  - the id of the page images to fetch
	 * @return (array) 	 - list of pages images
	 */
	public function pageImages($id=false){

		if(!$id){
			global $post;
			$id = $post->ID;
		}
		$images = array();
		$imgs = get_post_meta($id,"image");
		if(has_post_thumbnail($id)){
			$fid = get_post_thumbnail_id($id);
			array_unshift($imgs,$fid);
		}
		foreach($imgs as $i){
			$post = get_post($i);
			$img = array(
				"full_src"  => wp_get_attachment_image_src($i,"full"),
				"thumb_src" => wp_get_attachment_image_src($i,"square_thumb"),
				"caption"	=> htmlspecialchars($post->post_excerpt,ENT_QUOTES),
				"title"		=> $post->post_title
			);
			$images[] = $img;
		}
		return $images;

	}

	/*
	 * @desc 			   		- get a single image by id
	 * @param $id (int)    		- the id of the image to fetch
	 * @param $type (string)  	- the size of image to fetch
	 * @return (string)    		- the image
	 */
	public function getImage($id,$type="thumb",$raw=false){

		if($type == "picturefill"){
			$image	= get_post($id);
			$small 	= wp_get_attachment_image_src($id,"small_image");
			$medium = wp_get_attachment_image_src($id,"medium_image");
			$large 	= wp_get_attachment_image_src($id,"full_web");
			$img = "
				<picture>
					<!--[if IE 9]><video style='display:none;'><![endif]-->
					<source srcset='{$large[0]}' media='(min-width: 1200px)'>
					<source srcset='{$medium[0]}' media='(min-width: 750px)'>
					<source srcset='{$small[0]}'>
					<!--[if IE 9]></video><![endif]-->
					<img srcset='{$small[0]}' alt='{$image->post_title}'/>
				</picture>
			";
		}else{
			$img = wp_get_attachment_image_src($id,$type);
			if(!$raw && $img != ""){
				$alt = htmlspecialchars(get_post($id)->post_excerpt,ENT_QUOTES);
				$img = "<img src='{$img[0]}' width='{$img[1]}' height='{$img[2]}' alt='$alt'/>";
			}
		}
		return $img;

	}

	/*
	 * @desc 			   		- get the page gallery
	 * @param $id (int)    		- the id of page images to fetch
	 * @return (array)    		- array of images
	 */
	public function pageGallery($id=false){

		if(!$id){
			global $post;
			$id = $post->ID;
		}
		$meta 	 = get_post_meta($id,"gallery_image");
		if(!function_exists("get_group")){
			return "";
		}
		$group 	 = get_group("Gallery",$id);
		$gallery = array();
		foreach($meta as $key => $img){
			if($img != ""){
				$imgPost = get_post($img);
				$gallery[$key]["thumb"] 		= $this->getImage($img,"square_thumb",true);
				$gallery[$key]["full"]  		= $this->getImage($img,"full_web",true);
				$gallery[$key]["medium"]  		= $this->getImage($img,"medium_image",true);
				$gallery[$key]["small"]  		= $this->getImage($img,"small_image",true);
				$gallery[$key]["picturefill"] 	= $this->getImage($img,"picturefill");
				$gallery[$key]["title"] 		= $group[$key+1]["gallery_title"][1];//$imgPost->post_title;
			}
		}
		return $gallery;

	}

	/*
	 * @desc 				- get home page featured pages
	 * @param $raw (bool) 	- whether to return an array or string of html
	 * @return (array) 		- an array of pages
	 */
	public function siteFeaturedPages($raw=false){

		$pages = new WP_Query(array(
			"meta_key" 			=> "featured_featured",
			"meta_value" 		=> "1",
			"posts_per_page"	=> -1,
			"post_type"			=> "any"
		));
		$results = array();
		foreach($pages->posts as $page){
			$page->meta 			= get_post_meta($page->ID);
			$page->post_excerpt 	= $this->pageExcerpt($page->ID,40);
			$page->thumb 			= $this->pageFeatureThumb("square_thumb",$page->ID);
			$page->image 			= "<img src='".$this->pageFeatureImage("square_thumb",$page->ID,true)."'/>";
			$order 					= get_post_meta($page->ID,"featured_order",true);
			$order 					= $order[0]."_".$page->ID;
			$results[$order] 		= $page;
		}
		ksort($results);
		if($raw){
			return $results;
		}
		$pods = "<ul class='featuredPages list-unstyled'>";
		foreach($results as $pod){
			$pods .= "
				<li>
					<h3><a href='{$pod->guid}' title='{$pod->post_excerpt}'>{$pod->post_title}</a></h3>
					<a href='{$pod->guid}'>{$pod->thumb}</a>
					<p>{$page->post_excerpt}</p>
					<a href='{$pod->guid}'>Read more</a>
				</li>
			";
		}
		$pods .= "</ul>";
		return $pods;

	}

	/*
	 * @desc 				- get children of a page
	 * @param $id (int) 	- the id of the parent
	 * @param $start (int) 	- the start index
	 * @param $max (int) 	- the max number to fetch
	 * @return (array) 		- an array of child pages
	 */
	public function pageChildren($id=false,$start=0,$max=999){

		if(!$id){
			global $post;
			$id = $post->ID;
		}
		$children = array();
		$pages = get_pages(array(
			"numberposts" 	=> $max,
			"offset"		=> $start,
			"child_of" 		=> $id,
			"sort_column" 	=> "menu_order"
		));
		$i = 1;
		foreach($pages as $page){
			if($page->post_parent == $id){
				if($i >= $start && $i <= $max){
					$page->thumb = $this->pageFeatureThumb("square_thumb",false,$page->ID);
					$page->meta  = get_post_meta($page->ID);
					$children[]  = $page;
				}
				$i++;
			}
		}
		return $children;

	}

	/*
	 * @desc 				- get a certain post type
	 * @param $id (int) 	- the id of the parent
	 * @param $start (int) 	- the start index
	 * @param $max (int) 	- the max number to fetch
	 * @return (array) 		- an array of pages
	 */
	public function postTypes($type="all",$start=0,$max=999,$tax=null,$term=null){

		if(isset($_GET["paging"])){
			$start = $_GET["paging"];
		}
		$found = array();
		if($tax && $term){
			$taxQuery = array(
				array(
					"taxonomy" 	=> $tax,
					"field"		=> "slug",
					"terms"		=> $term
				)
			);
			$postNo = count(get_posts(array("numberposts"=>-1,"tax_query"=>$taxQuery)));
			$posts = new WP_Query(array(
				"offset"			=> $start,
				//"orderby"			=> "menu_order",
				//"order"			=> "ASC",
				"posts_per_page"	=> $max,
				"tax_query"			=> $taxQuery
			));
		}else{
			$taxQuery = null;
			$postNo = count(get_posts(array("numberposts"=>-1,"post_type"=>$type)));
			$posts = new WP_Query(array(
				"post_type" 		=> $type,
				"offset"			=> $start,
				//"orderby"			=> "menu_order",
				//"order"			=> "ASC",
				"posts_per_page"	=> $max,
				"tax_query"			=> $taxQuery
			));
		}
		$i = 1;
		$found["posts"] = array();
		foreach($posts->posts as $post){
			if($i >= $start && $i <= $max){
				if($type == $post->post_type || $type == "all"){
					$post->thumb = $this->pageFeatureThumb("square_thumb",false,$post->ID);
					$post->meta  = get_post_meta($post->ID);
					$found["posts"][]  = $post;
				}
				$i++;
			}
		}
		$id	  = get_the_ID();
		$next = $start+$max;
		$prev = $start-$max;
		if($prev < 0){
			$prev = false;
		}else if($prev == 0){
			$prev = "{$this->sitePath()}?p=$id";
		}else{
			$prev = "{$this->sitePath()}?p=$id&paging=$prev";
		}
		if($next >= $postNo){
			$next = false;
		}else{
			$next = "{$this->sitePath()}?p=$id&paging=$next";
		}
		$found["paging"] = array(
			"next"  => $next,
			"prev"	=> $prev
		);
		return $found;

	}

	/*
	 * @desc 				- get children of a page
	 * @param $id (int) 	- the id of the parent
	 * @param $start (int) 	- the start index
	 * @param $max (int) 	- the max number to fetch
	 * @return (array) 		- an array of child pages
	 */
	public function pageSiblings($id=false,$start=0,$max=999){

		if(!$id){
			global $post;
			$id 	= $post->ID;
		}else{
			$post = get_page($id);
		}
		$parent   = $post->post_parent;
		$siblings = array();
		if($parent != 0){
			$pages = get_pages(array(
				"numberposts" 	=> $max,
				"offset"		=> $start,
				"parent" 		=> $parent,
				"child_of"		=> $parent,
				"sort_column" 	=> "menu_order"
			));
			$i = 1;
			foreach($pages as $page){
				if($i >= $start && $i <= $max){
					$page->thumb = $this->pageFeatureThumb("thumb",false,$page->ID);
					$page->meta  = get_post_meta($page->ID);
					$siblings[]  = $page;
				}
				$i++;
			}
		}
		return $siblings;

	}

	/*
	 * @desc 				- get related posts - ie posts in same cat
	 * @param $id (int) 	- the id of the page
	 * @param $start (int) 	- the start index
	 * @param $max (int) 	- the max number to fetch
	 * @return (array) 		- an array of related pages
	 */
	public function pageRelated($cat=false,$start=0,$max=999){

		if(!$cat){
			$catInfo = get_the_category();
			$catId	 = $catInfo[0]->term_id;
		}else{
			$catInfo = get_category_by_slug($cat);
			$catId	 = $catInfo->term_id;
		}
		if(!empty($catInfo)){
			$related = array();
			$posts = get_posts(array(
				"posts_per_page" 	=> $max,
				"offset"			=> $start,
				"category"			=> $catId
			));
			foreach($posts as $post){
				$post->thumb = $this->pageFeatureThumb("thumb",false,$post->ID);
				$post->meta  = get_post_meta($post->ID);
				$related[]  = $post;
				$i++;
			}
		}
		return $related;

	}

	/*
	 * @desc 						- get the posts from a cat
	 * @param $catName (string) 	- the id of the parent
	 * @param $start (int) 			- the start index
	 * @param $max (int) 			- the max number to fetch
	 * @return (array) 				- an array of posts
	 */
	public function pagePosts($catName,$start=0,$max=999,$truncate=60){

		if(isset($_GET["paging"])){
			$start = $_GET["paging"];
		}
		$list	 		= array();
		$catInfo 		= get_category_by_slug($catName);
		$catId	 		= $catInfo->term_id;
		$postNo  		= count(get_posts(array("numberposts"=>-1,"category"=>$catId)));
		$posts = get_posts(array(
			"numberposts" 	=> $max,
			"offset"		=> $start,
			"category"		=> $catId,
			"post_status"	=> "publish"
		));
		$list["posts"] = array();
		foreach($posts as $post){
			$post->thumb = $this->pageFeatureThumb("thumb",false,$post->ID);
			$post->meta  = get_post_meta($post->ID);
			$post->tags	 = wp_get_post_tags($post->ID);
			if($post->post_excerpt == ""){
				$post->post_excerpt = strip_tags($this->truncate($post->post_content,$truncate));
			}
			$post->post_excerpt = htmlspecialchars($post->post_excerpt,ENT_QUOTES);
			$post->post_created = date("D jS M Y",strtotime($post->post_date));
			$list["posts"][] = $post;
		}
		$id	  = get_the_ID();
		$next = $start+$max;
		$prev = $start-$max;
		if($prev < 0){
			$prev = false;
		}else if($prev == 0){
			$prev = "{$this->sitePath()}?p=$id";
		}else{
			$prev = "{$this->sitePath()}?p=$id&paging=$prev";
		}
		if($next >= $postNo){
			$next = false;
		}else{
			$next = "{$this->sitePath()}?p=$id&paging=$next";
		}
		$list["paging"] = array(
			"next"  => $next,
			"prev"	=> $prev
		);
		return $list;

	}

	/*
	 * @desc 				- format a date
	 * @param $id (int)		- the id of the page parent
	 * @return (string) 	- the date
	 */
	public function postDate($id=false,$format="d/m/y"){

		if(!$id){
			global $post;
			$id = $post->ID;
		}else{
			$post = get_post($id);
		}
		$date 	= $post->post_date;
		$date 	= new DateTime($date);
		//$day  	= $date->format("jS");
		$day  	= $date->format("d");
		$month	= $date->format("M");
		$year	= $date->format("Y");
		//$date = $date->format($format);
		$date = "<span class='day'>$day</span><span class='month'>$month</span><span class='year'>$year</span>";
		return $date;

	}

	/*
	 * @desc 					- grab a posts tags
	 * @param $id (int)			- the id of the post
	 * @return (array/string) 	- the tags
	 */
	function pageTags($id=false,$string=false,$divider=" "){

		if(!$id){
			global $post;
			$id = $post->ID;
		}

		$tags = wp_get_post_tags($id);
		if(!$string){
			return $tags;
		}

		$string = "";
		foreach($tags as $tag){
			$string .= $tag->slug.$divider;
		}
		return $string;

	}

	/*
	 * @desc 					- grab a posts terms
	 * @param $id (int)			- the id of the post
	 * @param $term (string)	- the term to fetch
	 * @param $string (string)	- whether to return string or array
	 * @param $divider (string)	- if string, what to seperate terms with
	 * @return (array/string) 	- the terms
	 */
	function pageTerms($id=false,$term,$string=false,$divider=" ",$singular=false){

		if(!$id){
			global $post;
			$id = $post->ID;
		}

		$terms = wp_get_post_terms($id,$term,array("fields"=>"all"));
		if(!$string){
			if($singular){
				if(isset($terms[0])){
					return $terms[0];
				}else{
					return $terms;
				}
			}else{
				return $terms;
			}
		}

		$string = "";
		if($singular){
			if(isset($terms[0])){
				$string = $terms[0]->slug.$divider;
			}
		}else{
			foreach($terms as $term){
				$string .= $term->slug.$divider;
			}
		}
		return $string;

	}

	/*
	 * @desc 					- grab a taxonomies terms
	 * @param $tax (string)		- the tax to fetch
	 * @param $raw (bool)		- whether to return array or string
	 * @param $class (string)	- a class to add to the list
	 * @return (string/array) 	- a list/array of terms
	 */
	function getTerms($tax,$raw=true,$class=""){

		$terms = get_terms($tax,array());
		if(empty($terms)){
			return false;
		}
		if($raw){
			return $terms;
		}
		$termList = "<ul class='$class'>";
		foreach($terms as $term){
			$termList .= "<li data-slug='{$term->slug}' data-id='{$term->term_id}'><a href='{$this->sitePath()}/{$term->slug}' title='{$term->description}'>{$term->name}</a></li>";
		}
		$termList .= "</ul>";
		return $termList;

	}

	/*
	 * @desc 				- get the tax children
	 * @param tax (string)	- the tax name
	 * @param max (int)		- the no of children to fetch
	 * @return (array)		- the term children
	 */
	public function taxChildren($tax,$start=0,$max=999){

		$children = array();
		$terms = $this->getTerms($tax,true);
		if(isset($terms->errors)){
			return $children;
		}
		foreach($terms as $term){
			$children[$term->slug] = array(
				"term" 	=> $term,
				"posts" => $this->postTypes("all",$start,$max,$tax,$term->slug)
			);
		}
		return $children;

	}

	/*
	 * @desc 				- get the term children
	 * @param tax (string)	- the tax name
	 * @param term (string)	- the term
	 * @param max (int)		- the no of children to fetch
	 * @return (array)		- the term children
	 */
	public function termChildren($tax=false,$term=false,$start=0,$max=999){

		if(!$tax){
			$tax = get_query_var("taxonomy");
		}
		if(!$term){
			$term = get_query_var("term");
		}
		$posts = $this->postTypes("all",$start,$max,$tax,$term);
		return $posts;

	}

	/*
	 * @desc 				- grab a pages top most parent
	 * @param $id (int)		- the id of the page parent
	 * @return (string) 	- the section
	 */
	public function getTopParent($id=false){

		if(!$id){
			global $post;
			$parent = $post;
		}else{
			$parent = get_page($id);
		}

		if($parent->post_type == "post"){

			$cat = get_the_category($post->ID);
			$cat = $cat[0]->slug;
			$parent->cust_section = $cat;
			return $parent;

		}else{

			if($parent->post_parent != 0){
				return $this->getTopParent($parent->post_parent);
			}else{
				$parent->cust_section = $parent->post_name;
				return $parent;
			}

		}

	}

	/*
	 * @desc 				- grab all a pages parents
	 * @param $id (int)		- the id of the page parent
	 * @return (array) 		- an array of all the parents
	 */
	public function getPageTrail($page=false,$trail=array()){

		if(!$page){
			global $post;
			$page = $post;
		}
		$trail[] = $page;
		if($page->post_parent){
			$parent = $this->getParent($page->ID);
			return $this->getPageTrail($parent,$trail);
		}else if($this->isCategory()){
			$cat = $this->getCategory(true);
			$c 				= new stdClass();
			$c->guid 		= $this->sitePath()."/category/".$cat->slug;
			$c->post_title 	= $cat->name;
			$c->isCat		= true;
			$trail 			= array($c);
			return $trail;
		}else if($page->post_type == "post"){
			$cat = $this->getCategory(true);
			if(isset($cat[0]) && is_array($cat)){
				$c 				= new stdClass();
				$c->guid 		= $this->sitePath()."/category/".$cat[0]->slug;
				$c->post_title 	= $cat[0]->name;
				$c->isCat		= true;
				$trail[] = $c;
			}
			return $trail;
		}else if($page->post_type != "page"){
			$parent = new WP_Query(array(
				"name" 			 => $page->post_type,
				"post_type"		 => "page",
				"posts_per_page" => 1
			));
			if(!empty($parent->posts)){
				$parent = $parent->posts[0];
				return $this->getPageTrail($parent,$trail);
			}else{
				return $trail;
			}
		}else{
			return $trail;
		}

	}

	/*
	 * @desc 				- grab a pages parent
	 * @param $id (int)		- the id of the page parent
	 * @return (object) 	- the pages parent
	 */
	public function getParent($id){

		$page = get_page($id);
		if($page->post_parent != 0){
			return get_page($page->post_parent);
		}else{
			return $page;
		}

	}

	/*
	 * @desc 				- echo out a gravity form
	 * @param $id (int)		- the id of the gravity form
	 * @echo (string) 		- the form
	 */
	public function pageForm($id,$class=""){

		global $formClass;
		$formClass = $class;
		add_filter("gform_form_tag","form_tag",10,2);
		add_filter("gform_field_content","subsection_field",10,5);
		add_filter("gform_submit_button","form_submit_button",10,2);
		add_filter("gform_register_init_scripts","register_form_scripts",10,2);
		add_filter("gform_init_scripts_footer","__return_true");
		if(!function_exists("subsection_field")){
			function subsection_field($content,$field,$value,$lead_id,$form_id){
				if(strstr($content,"gsection_title")){
					$label 		= GFCommon::get_label($field);
					$newContent = "<h3>$label</h3>";
				}else{

					$label 		 = GFCommon::get_label($field);
					$placeholder = str_replace(":","",$label);
					$desc  		 = rgget("description",$field);

					$valiFailed  = rgget("failed_validation",$field);
					$valiMessage = $field["validation_message"];
					$isRequired	 = strstr($content,"gfield_required");
					$isPhone	 = strstr(strtolower($label),"phone");
					$isEmail	 = strstr(strtolower($label),"email");
					$isURL	 	 = strstr(strtolower($label),"website");

					$input 		 = GFCommon::get_field_input($field,$value,0,$form_id);
					$input 		 = str_replace("ginput_container","",$input);
					$input 		 = str_replace("<input","<input placeholder='$placeholder' ",$input);
					$input 		 = str_replace("<textarea","<textarea placeholder='$placeholder' ",$input);
					$isGroup	 = strstr($input,"gfield_checkbox");
					$isTextarea	 = strstr($input,"textarea");

					$input = str_replace("<input","<input class='form-control' ",$input);
					$input = str_replace("<select","<select class='form-control' ",$input);
					$input = str_replace("<textarea","<textarea class='form-control' ",$input);

					if($isRequired){
						$input = str_replace("<input","<input required='required' ",$input);
						$input = str_replace("<select","<select required='required' ",$input);
						$input = str_replace("<textarea","<textarea required='required' ",$input);
					}

					if($isPhone){
						//$input = str_replace("type='text'","type='number'",$input);
					}

					if($isURL){
						$input = str_replace("type='text'","type='url'",$input);
					}

					if($isEmail){
						$input = str_replace("type='text'","type='email'",$input);
					}

					if($desc != ""){
						$desc = "<small class='help-block'><em>$desc</em></small>";
					}

					$groupClass = "";
					if($isGroup){
						$groupClass = "input-group";
					}
					if($isTextarea){
						$groupClass = "textarea";
					}

					$newContent = "
						<div class='form-group $groupClass'>
							<label>$label</label>
							<small class='validation_message'>$valiMessage</small>
							$input
							$desc
						</div>
					";

				}
				return $newContent;
			}
		}
		if(!function_exists("form_tag")){
			function form_tag($form_tag,$form){
				global $formClass;
				$form_tag = str_replace("<form","<form class='$formClass' ",$form_tag);
				return $form_tag;
			}
		}
		if(!function_exists("form_submit_button")){
			function form_submit_button($button,$form){
				$button = "
					<button class='btn' type='submit' id='gform_submit_button_{$form["id"]}'>Submit</button>
				";
				return $button;
			}
		}
		if(function_exists("gravity_form")){
			echo "<div class='custForm'>";
			gravity_form($id,false,true,false,"",false);
			echo "</div>";
		}

	}

	/*
	 * @desc 					- truncate a string
	 * @param $string (string)	- the string to truncate
	 * @param $limit (int)		- the number of words to truncate to
	 * @return (string) 		- the page excerpt
	 */
	public function truncate($string,$limit){

		if(str_word_count($string,0) > $limit){
			$words 	= str_word_count($string,2);
			$pos 	= array_keys($words);
			$string = substr($string,0,$pos[$limit])."...";
		}
		return $string;

	}

	/*
	 * @desc 					- send a post value to the page
	 * @param $name (string)	- the name of the post field
	 * @return (string) 		- the field value
	 */
	public function postValue($name){

		$val = "";
		if(isset($_POST["$name"])){
			$val = $_POST["$name"];
		}
		return $val;

	}

	/*
	 * @desc					- gets a group image with thumb options
	 * @param $group (string)	- group field name
	 * @param $key (int)		- the key of the field in the array
	 * @param $id (int)			- id of the page
	 * @param $type (string)	- type of image to fetch
	 * @return (string)			- the image html
	 */
	public function getGroupImage($group,$key,$id,$type="square_thumb"){

		//get the meta
		$meta 	= get_post_meta($id,$group);
		if(isset($meta[$key-1])){
			$id = $meta[$key-1];
			$img = $this->getImage($id,$type);
			return $img;
		}else{
			return false;
		}

	}

	/*
	 * @desc 				- check a text string for links and if found turn into anchor tags
	 * @param (string) 		- the string to process
	 * @return (string) 	- the string with links
	 */
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

	/*
	 * @desc 				- change a plural string to singular
	 * @param (string) 		- the string to process
	 * @return (string) 	- the processed string
	 */
	public function singular($str){

		if(substr($str,-1) == "s"){
			$str = substr($str,0,-1);
		}
		return $str;

	}

	/*
	 * @desc 				- change a singular string to plural
	 * @param (string) 		- the string to process
	 * @return (string) 	- the processed string
	 */
	public function plural($str){

		if(substr($str,-1) != "s"){
			$str = $str."s";
		}
		return $str;

	}

	/*
	 * @desc  					- prints out an object or array
	 * @param (array or object) - the array or object to debug
	 */
	public function debug($debug){

		echo "<pre style='color:#fff; padding:10px; background:#000;'>";
		print_r($debug);
		echo "</pre>";
		exit;

	}

}

?>
