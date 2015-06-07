<?php
//the core class
global $C;
//the post
global $post;
//the featured image
$featuredImage = $C->pageFeatureImage(false,false,true);
?>
<!DOCTYPE html>
<!--[if IE 6]><html id="ie6" class="msie"><![endif]-->
<!--[if IE 7]><html id="ie7" class="msie"><![endif]-->
<!--[if IE 8]><html id="ie8" class="msie"><![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8) ]><!-->
<html lang="en">
<!--<![endif]-->

<head>

<title><?php echo $C->pageMetaTitle(); ?></title>

<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>

<meta property="og:site_name" content="<?php echo $C->siteName(); ?>"/>
<?php if($C->pageFeatureThumb() != ""): ?>
<meta property="og:image" content="<?php echo $C->pageFeatureThumb("full_web",false,true); ?>"/>
<?php else: ?>
<meta property="og:image" content="<?php echo $C->themePath(); ?>/images/og-image.jpg"/>
<?php endif; ?>

<base href="<?php echo $C->sitePath(); ?>/"/>

<link rel="shortcut icon" href="<?php echo $C->themePath(); ?>/images/favicon.png"/>

<style type="text/css">
	html {
		width:100%;
		height:100%;
		background:#45626A url(<?php echo $C->themePath(); ?>/images/loading.gif) no-repeat center center;
	}
	#page,
	#mainNav {
		opacity:0;
		transition:all 1s;
		-webkit-transition:all 1s;
		-moz-transition:all 1s;
	}
</style>

<?php wp_head(); ?>

</head>

<body id="hatchd" class="<?php echo $C->pageTemplate(); ?> <?php echo $C->pageClass(); ?>">

<div id="fb-root"></div>

<nav id="mainNav">

	<i class="glyphicon glyphicon-menu-hamburger" id="menuIcon"></i>

	<div id="menuTitle">
		<h3><?php echo $C->siteName(); ?></h3>
	</div>
	
	<div class="container">
		<div class="row">
			<div class="col-md-3 hidden-xs hidden-sm">
				<a class="logo" href="<?php echo $C->sitePath(); ?>" title="<?php echo $C->siteTagline(); ?>">
					<span><small>Best Laid</small>Plans</span>
				</a>
			</div>
			<div class="col-md-9">
				<?php echo $C->siteMenu(1,"list-unstyled"); ?>
			</div>
		</div>
	</div>

</nav>

<div id="page">

	<header>
		
		<div id="siteSocial" class="socialButtons clearfix st-shape-r4">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="pull-right">
							<a class="st-icon-twitter" href="#">Follow us on Twitter</a>
							<a class="st-icon-facebook" href="#">Follow us on Facebook</a>
							<a class="st-icon-instagram" href="#">Follow us on Instagram</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?php if($featuredImage != ""): ?>
		<div id="featuredImage" style="background-image:url(<?php echo $featuredImage; ?>);">
		<?php else: ?>
		<div id="featuredImage" style="background-image:url(<?php echo $C->themePath(); ?>/images/default-featured-image.jpg);">	
		<?php endif; ?>
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div id="innerFeatured">
							<a class="logo visible-xs visible-sm" href="<?php echo $C->sitePath(); ?>" title="<?php echo $C->siteTagline(); ?>">
								<span><small>Best Laid</small>Plans</span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

	</header>

	<div id="mainContent">
		
		<div class="container">
				
			<div id="innerMainContent">
	
				<?php if(!$C->pageTemplateIs("index")): ?>
				<?php include("includes/bread.php"); ?>
				<?php endif; ?>
