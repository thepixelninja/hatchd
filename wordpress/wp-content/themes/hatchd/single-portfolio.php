<?php 
get_header();
//grab the gallery images
$gallery = $C->pageGallery();
?>

<div class="row">
			
	<div class="col-md-9">
		
		<article class="content">
		
			<?php if($C->isPageFeatureImage()): ?>
			<div class="featureImage">
				<?php echo $C->pageFeatureImage(); ?>
			</div>
			<?php endif; ?>
			
			<?php if($C->isPageMeta("page_details_subtitle")): ?>
			<h2 class="pageTitle"><?php echo $C->pageMeta("page_details_subtitle"); ?></h2>
			<?php else: ?>
			<h2 class="pageTitle"><?php echo $C->pageTitle(); ?></h2>
			<?php endif; ?>
			
			<?php include("includes/social-buttons.php"); ?>
			
			<?php echo $C->pageContent(); ?>
			
			<?php if(!empty($gallery)): ?>
			<div id="folioGal">
				<?php foreach($gallery as $image): ?>
				<div class="imageHolder">
					<h4><?php echo $image["title"]; ?></h4>
					<a href="<?php echo $image["full"][0]; ?>" class="lightBox" title="<?php echo $image["title"]; ?>">
						<?php echo $image["picturefill"]; ?>
					</a>
				</div>	
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
			
			<?php include("includes/logo-loop.php"); ?>
	
		</div>
							
	</article>
	
	<div class="col-md-3">
		
		<?php include("includes/sidebar.php"); ?>
		
	</div>

</div>

<?php get_footer(); ?>