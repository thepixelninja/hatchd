<?php
//Template name: List children
get_header();
//grab children
$posts = $C->pageChildren();
?>

<div class="row">
			
	<div class="col-md-9">
		
		<div class="content">
		
			<?php if($C->isPageMeta("page_details_subtitle")): ?>
			<h2 class="pageTitle"><?php echo $C->pageMeta("page_details_subtitle"); ?></h2>
			<?php else: ?>
			<h2 class="pageTitle"><?php echo $C->pageTitle(); ?></h2>
			<?php endif; ?>
			<?php echo $C->pageContent(); ?>
			
			<?php include("includes/post-loop.php"); ?>
		
		</div>
							
	</div>
	
	<div class="col-md-3">
		
		<?php include("includes/sidebar.php"); ?>
		
	</div>

</div>

<?php get_footer(); ?>