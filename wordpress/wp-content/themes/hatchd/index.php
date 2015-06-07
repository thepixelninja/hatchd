<?php
//Template name: Home page
get_header();
//get the feature slides
$featureSlides = $C->pageFeature(0,0,1);
//get the featured pages
$featuredPages = $C->siteFeaturedPages(true);
?>

<?php if(!empty($featureSlides)): ?>
<section id="featureSlider">

	<div id="slider" class="carousel slide" data-ride="carousel">

		<div class="carousel-inner">

			<?php foreach($featureSlides as $key => $slide): ?>
			<div class="item <?php if($key == 0): ?>active<?php endif; ?>">
				<h2>
				<?php if($slide["link"] != ""): ?><a href="<?php echo $slide["link"]; ?>" title="<?php echo $slide["text"]; ?>"><?php endif; ?>
				<small>The</small><span><?php echo $slide["text"]; ?></span><small>Ninja</small>
				<?php if($slide["link"] != ""): ?></a><?php endif; ?>
				</h2>
			</div>
			<?php endforeach; ?>

		</div>

		<ol class="carousel-indicators">
			<?php foreach($featureSlides as $key => $slide): ?>
			<li data-target="#slider" data-slide-to="<?php echo $key; ?>" <?php if($key == 0): ?>class="active"<?php endif; ?>></li>
			<?php endforeach; ?>
		</ol>

	</div>

</section>
<?php endif; ?>

<?php get_footer(); ?>
