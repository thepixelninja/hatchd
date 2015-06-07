<?php
//the core class
global $C;
//the post
global $post;
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
		background:#191919 url(<?php echo $C->themePath(); ?>/images/loading.gif) no-repeat center center;
	}
	#page,
	#mainNav {
		/*display:none;*/
	}
</style>

<?php wp_head(); ?>

</head>

<body id="hatchd" class="<?php echo $C->pageTemplate(); ?> <?php echo $C->pageClass(); ?>">

<div id="fb-root"></div>

<nav id="mainNav">

	<i class="glyphicon glyphicon-align-justify" id="menuIcon"></i>

	<div id="menuTitle">
		<h3>ThePixelNinja</h3>
	</div>

	<?php echo $C->siteMenu(2,"list-unstyled"); ?>

</nav>

<div id="page">

	<div id="siteSocial" class="socialButtons lightSocial clearfix">
		<div class="buttonHolder">
			<a class="st-icon-facebook st-icon-square" href="#" data-share="facebook" data-placement="left" data-url="<?php echo $C->sitePath(); ?>"></a>
			<a class="st-icon-googleplus st-icon-square" href="#" data-share="google" data-placement="left" data-url="<?php echo $C->sitePath(); ?>"></a>
			<a class="st-icon-twitter st-icon-square" href="#" data-share="twitter" data-placement="left" data-intent="follow" data-url="<?php echo $C->sitePath(); ?>"></a>
		</div>
	</div>

	<div class="colorBar"></div>

	<header>

		<div class="container">

			<div class="row">

				<div class="col-md-12">

					<div id="paralax">

						<a id="logo" href="<?php echo $C->sitePath(); ?>" title="<?php echo $C->siteTagline(); ?>">
							<?php echo $C->svg("the-pixel-ninja-logo.svg"); ?>
						</a>
						<h1><?php echo $C->pageTitle(); ?></h1>

					</div>

				</div>

			</div>

		</div>

	</header>

	<div id="mainContent">

		<?php if(!$C->pageTemplateIs("index")): ?>
		<div class="container">

			<?php include("includes/bread.php"); ?>

		<?php endif; ?>
