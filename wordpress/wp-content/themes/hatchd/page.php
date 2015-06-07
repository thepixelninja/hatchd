<?php 
get_header(); 
//grab the post
global $post;
?>

<div class="row">
			
	<div class="col-md-9">
		
		<article class="content">
			
			<?php if($post->post_type == "post"): ?>
			<div class="date egg"><?php echo $C->postDate($post->ID); ?></div>
			<?php endif; ?>
			
			<div class="titleArea">
				<h1><?php echo $C->pageTitle(); ?></h1>
				<?php if($C->isPageMeta("page_details_subtitle")): ?>
				<h2><?php echo $C->pageMeta("page_details_subtitle"); ?></h2>	
				<?php endif; ?>
			</div>
			
			<?php echo $C->pageContent(); ?>
			
		</article>
							
	</div>
	
	<div class="col-md-3">
		
		<?php include("includes/sidebar.php"); ?>
		
	</div>

</div>

<?php get_footer(); ?>