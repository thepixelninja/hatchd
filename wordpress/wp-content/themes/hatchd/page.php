<?php 
get_header(); 
//grab the post
global $post;
?>

<div class="row">
			
	<div class="col-md-9">
		
		<article class="content">
			
			<?php if($post->post_type == "post"): ?>
			<div class="date starburst"><div><?php echo $C->postDate($post->ID); ?></div></div>
			<?php endif; ?>
			
			<?php if($C->isPageFeatureImage()): ?>
			<div class="featureImage">
				<?php echo $C->pageFeatureImage(); ?>
			</div>
			<?php endif; ?>
			
			<?php if($C->isPageMeta("page_details_subtitle")): ?>
			<h2 class="pageTitle"><span><?php echo $C->pageMeta("page_details_subtitle"); ?></span></h2>
			<?php else: ?>
			<h2 class="pageTitle"><span><?php echo $C->pageTitle(); ?></span></h2>
			<?php endif; ?>
			
			<?php if($post->post_type == "post" || $post->post_type == "labs"): ?>
			<?php include("includes/social-buttons.php"); ?>
			<?php endif; ?>
			
			<?php echo $C->pageContent(); ?>
			
			<?php if($post->post_type == "post" || $post->post_type == "labs"): ?>
			<div id="comments"></div>
			<?php endif; ?>
		
		</article>
							
	</div>
	
	<div class="col-md-3">
		
		<?php include("includes/sidebar.php"); ?>
		
	</div>

</div>

<?php get_footer(); ?>