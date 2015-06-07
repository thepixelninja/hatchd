<?php
//Template name: List children
get_header();
//grab children
$posts = $C->pageChildren();
?>

<div class="row">
			
	<div class="col-md-9">
		
		<div class="content">
		
			<div class="titleArea">
				<h1><?php echo $C->pageTitle(); ?></h1>
				<?php if($C->isPageMeta("page_details_subtitle")): ?>
				<h2><?php echo $C->pageMeta("page_details_subtitle"); ?></h2>	
				<?php endif; ?>
			</div>
			<?php echo $C->pageContent(); ?>
			
			<?php include("includes/post-loop.php"); ?>
		
		</div>
							
	</div>
	
	<div class="col-md-3">
		
		<?php include("includes/sidebar.php"); ?>
		
	</div>

</div>

<?php get_footer(); ?>